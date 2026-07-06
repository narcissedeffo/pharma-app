<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    /**
     * Autorisation : Le pharmacien ou le fournisseur de la commande peuvent voir le PDF.
     */
    private function authorizeUser(Facture $facture, Request $request): void
    {
        $commande = $facture->commande;
        $user = $request->user();

        $isPharmacien = $commande->pharmacien_id === $user->id;
        $isFournisseur = $commande->fournisseur_id === $user->id;

        abort_unless($isPharmacien || $isFournisseur, 403);
    }

    public function downloadFacture(Request $request, Facture $facture)
    {
        $this->authorizeUser($facture, $request);
        $facture->load('commande.items', 'commande.fournisseur', 'commande.pharmacien');

        $pdf = Pdf::loadView('pdf.facture', compact('facture'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
            ]);

        return $pdf->download($facture->reference . '.pdf');
    }

    public function downloadBonLivraison(Request $request, Facture $facture)
    {
        $this->authorizeUser($facture, $request);
        $facture->load('commande.items', 'commande.fournisseur', 'commande.pharmacien');

        $pdf = Pdf::loadView('pdf.bon_livraison', compact('facture'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
            ]);

        return $pdf->download($facture->bl_reference . '.pdf');
    }
}
