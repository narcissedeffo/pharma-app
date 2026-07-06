<?php

namespace App\Http\Controllers\Fournisseur;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ImportProduitController extends Controller
{
    /**
     * Affiche le formulaire d'import.
     */
    public function create()
    {
        return view('fournisseur.produits.import');
    }

    /**
     * Traite le fichier uploadé (CSV ou PDF).
     */
    public function store(Request $request)
    {
        $request->validate([
            'fichier' => [
                'required',
                'file',
                'max:5120', // 5 MB
                'mimes:csv,txt,pdf',
            ],
        ], [
            'fichier.required'  => 'Veuillez sélectionner un fichier.',
            'fichier.max'       => 'Le fichier ne doit pas dépasser 5 Mo.',
            'fichier.mimes'     => 'Seuls les formats CSV et PDF sont acceptés.',
        ]);

        $file      = $request->file('fichier');
        $extension = strtolower($file->getClientOriginalExtension());
        $mode      = $request->input('mode_import', 'ajouter'); // 'ajouter' ou 'remplacer'

        try {
            $rows = match ($extension) {
                'pdf'         => $this->parsePdf($file->getRealPath()),
                'csv', 'txt'  => $this->parseCsv($file->getRealPath()),
                default       => throw new \InvalidArgumentException("Format non supporté."),
            };
        } catch (\Throwable $e) {
            Log::error('ImportProduit: ' . $e->getMessage());
            return back()->withErrors(['fichier' => 'Impossible de lire le fichier : ' . $e->getMessage()]);
        }

        if (empty($rows)) {
            return back()->withErrors(['fichier' => 'Aucune ligne valide trouvée dans le fichier.']);
        }

        $fournisseurId = Auth::id();

        // En mode remplacement, supprime les produits existants
        if ($mode === 'remplacer') {
            Produit::where('fournisseur_id', $fournisseurId)->delete();
        }

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        foreach ($rows as $i => $row) {
            $name = trim($row['nom'] ?? $row['name'] ?? '');
            if ($name === '') {
                $skipped++;
                continue;
            }

            $cip = trim($row['cip'] ?? null);

            $price = null;
            if (isset($row['prix_ht']) && $row['prix_ht'] !== '') {
                $priceVal = str_replace([',', ' '], ['.', ''], trim($row['prix_ht']));
                $price    = is_numeric($priceVal) ? round((float) $priceVal, 2) : null;
            } elseif (isset($row['price']) && $row['price'] !== '') {
                $priceVal = str_replace([',', ' '], ['.', ''], trim($row['price']));
                $price    = is_numeric($priceVal) ? round((float) $priceVal, 2) : null;
            }

            $datePeremption = null;
            if (isset($row['date_peremption']) && $row['date_peremption'] !== '') {
                try {
                    // Try parsing multiple formats, or just let Carbon handle it
                    $datePeremption = \Carbon\Carbon::parse(trim($row['date_peremption']))->format('Y-m-d');
                } catch (\Exception $e) {
                    $datePeremption = null;
                }
            }

            $available = true;
            if (isset($row['is_available'])) {
                $val       = strtolower(trim($row['is_available']));
                $available = !in_array($val, ['0', 'false', 'non', 'no', 'indisponible'], true);
            }

            try {
                Produit::create([
                    'fournisseur_id'  => $fournisseurId,
                    'cip'             => $cip ? substr($cip, 0, 50) : null,
                    'name'            => substr($name, 0, 255),
                    'description'     => isset($row['description']) ? substr(trim($row['description']), 0, 2000) : null,
                    'price'           => $price,
                    'date_peremption' => $datePeremption,
                    'is_available'    => $available,
                ]);
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = "Ligne " . ($i + 2) . " : " . $e->getMessage();
                $skipped++;
            }
        }

        $message = "$imported produit(s) importé(s) avec succès.";
        if ($skipped > 0) {
            $message .= " $skipped ligne(s) ignorée(s).";
        }

        return redirect()
            ->route('fournisseur.produits.index')
            ->with('status', $message)
            ->with('import_errors', $errors);
    }

    // ──────────────────────────────────────────
    // Parsers
    // ──────────────────────────────────────────

    /**
     * Parse un fichier CSV.
     * Colonnes attendues (ligne 1 = en-tête) :
     *   name, description, price, is_available
     */
    private function parseCsv(string $path): array
    {
        $rows      = [];
        $handle    = fopen($path, 'r');
        if ($handle === false) {
            throw new \RuntimeException("Impossible d'ouvrir le fichier CSV.");
        }

        // Détection du séparateur (virgule ou point-virgule)
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = substr_count($firstLine, ';') >= substr_count($firstLine, ',') ? ';' : ',';

        // Lecture de l'en-tête
        $header = fgetcsv($handle, 0, $delimiter);
        if (!$header) {
            fclose($handle);
            throw new \RuntimeException("Le fichier CSV semble vide ou mal formaté.");
        }

        // Normalisation des noms de colonnes
        $header = array_map(fn($h) => strtolower(trim(str_replace(["\xEF\xBB\xBF", '"'], '', $h))), $header);

        while (($line = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count($line) < 1) continue;
            // Si pas d'en-tête reconnue, on tente cip en col 0, nom en 1, description en 2, prix en 3, date_peremption en 4
            if (!in_array('nom', $header) && !in_array('name', $header)) {
                $rows[] = ['cip' => $line[0] ?? '', 'nom' => $line[1] ?? '', 'description' => $line[2] ?? '', 'prix_ht' => $line[3] ?? '', 'date_peremption' => $line[4] ?? ''];
            } else {
                $rows[] = array_combine($header, array_pad($line, count($header), ''));
            }
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Parse un fichier PDF via smalot/pdfparser.
     * Format attendu dans le PDF :
     *   Chaque ligne de produit = "Nom produit ; prix ; description"
     *   ou simplement "Nom produit"
     */
    private function parsePdf(string $path): array
    {
        if (!class_exists(\Smalot\PdfParser\Parser::class)) {
            throw new \RuntimeException("Le module de lecture PDF n'est pas installé (smalot/pdfparser).");
        }

        $parser  = new \Smalot\PdfParser\Parser();
        $pdf     = $parser->parseFile($path);
        $text    = $pdf->getText();
        $lines   = preg_split('/\r\n|\r|\n/', $text);

        $rows = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strlen($line) < 2) continue;

            // Ignore les lignes qui ressemblent à des en-têtes
            $lower = strtolower($line);
            if (in_array($lower, ['name', 'nom', 'produit', 'nom;prix', 'name;price', 'name,price'])) continue;

            // Séparateur ; ou ,
            $parts = preg_split('/[;,\t]/', $line, 4);
            $rows[] = [
                'name'         => trim($parts[0] ?? ''),
                'price'        => trim($parts[1] ?? ''),
                'description'  => trim($parts[2] ?? ''),
                'is_available' => trim($parts[3] ?? '1'),
            ];
        }

        return $rows;
    }
}
