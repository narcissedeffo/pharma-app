<?php

namespace App\Http\Controllers\Pharmacien;

use App\Http\Controllers\Controller;
use App\Models\CommandeFournisseur;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExportCommandeController extends Controller
{
    /**
     * Vérifie que le pharmacien connecté est propriétaire de la commande.
     */
    private function authorize(CommandeFournisseur $commande, Request $request): void
    {
        abort_unless($commande->pharmacien_id === $request->user()->id, 403);
    }

    /**
     * Télécharge la commande en PDF.
     */
    public function pdf(Request $request, CommandeFournisseur $commande)
    {
        $this->authorize($commande, $request);

        $commande->load('fournisseur', 'pharmacien', 'items');

        $pdf = Pdf::loadView('pharmacien.commandes.export_pdf', compact('commande'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'    => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
            ]);

        $filename = 'commande-' . $commande->reference . '-' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Télécharge la commande en CSV.
     */
    public function csv(Request $request, CommandeFournisseur $commande)
    {
        $this->authorize($commande, $request);

        $commande->load('fournisseur', 'pharmacien', 'items');

        $filename = 'commande-' . $commande->reference . '-' . now()->format('Ymd') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
        ];

        $callback = function () use ($commande) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 pour Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // ── Métadonnées ──
            fputcsv($handle, ['Référence commande', $commande->reference], ';');
            fputcsv($handle, ['Statut', $commande->statusLabel()], ';');
            fputcsv($handle, ['Date envoi', $commande->sent_at ? $commande->sent_at->format('d/m/Y H:i') : 'Brouillon'], ';');
            fputcsv($handle, ['Pharmacie', $commande->pharmacien->name], ';');
            fputcsv($handle, ['Fournisseur', $commande->fournisseur->name], ';');
            if ($commande->notes) {
                fputcsv($handle, ['Notes', $commande->notes], ';');
            }
            fputcsv($handle, [], ';'); // Ligne vide

            // ── En-tête articles ──
            fputcsv($handle, ['Désignation du produit', 'Quantité commandée', 'Prix unitaire (XAF)', 'Total (XAF)'], ';');

            // ── Lignes articles ──
            foreach ($commande->items as $item) {
                fputcsv($handle, [
                    $item->nom_medicament,
                    $item->quantite,
                    $item->prix_unitaire ?? 'Sur devis',
                    $item->prix_unitaire ? ($item->prix_unitaire * $item->quantite) : 'Sur devis',
                ], ';');
            }

            // ── Pied de page ──
            fputcsv($handle, [], ';');
            fputcsv($handle, ['Total articles', $commande->items->count()], ';');
            fputcsv($handle, ['Total estimé de la commande (XAF)', $commande->total()], ';');
            fputcsv($handle, ['Exporté le', now()->format('d/m/Y à H:i')], ';');

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
