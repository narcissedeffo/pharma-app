<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $facture->reference }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #1e293b; margin: 0; padding: 0; }
        .header { width: 100%; margin-bottom: 40px; }
        .title { font-size: 28px; font-weight: bold; color: #0f766e; text-transform: uppercase; letter-spacing: 1px; }
        .invoice-details { margin-top: 15px; }
        .invoice-details table { width: 100%; border-collapse: collapse; }
        .invoice-details th { text-align: left; color: #64748b; font-size: 10px; text-transform: uppercase; padding-bottom: 5px; }
        .invoice-details td { font-size: 13px; font-weight: bold; }
        .info-box { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; min-height: 120px; }
        .info-title { font-weight: bold; color: #64748b; font-size: 10px; text-transform: uppercase; margin-bottom: 10px; }
        .company-name { font-size: 16px; font-weight: bold; color: #0f766e; margin-bottom: 5px; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 30px; margin-bottom: 20px; }
        table.items th { background-color: #0f766e; color: white; text-align: left; padding: 12px; font-size: 11px; text-transform: uppercase; }
        table.items td { border-bottom: 1px solid #e2e8f0; padding: 12px; font-size: 12px; }
        table.items tr:nth-child(even) { background-color: #f8fafc; }
        .totals { width: 300px; float: right; margin-top: 20px; }
        .totals table { width: 100%; border-collapse: collapse; }
        .totals th { text-align: left; padding: 8px; color: #64748b; border-bottom: 1px solid #e2e8f0; }
        .totals td { text-align: right; padding: 8px; font-weight: bold; border-bottom: 1px solid #e2e8f0; }
        .totals tr.grand-total th { color: #0f766e; font-size: 16px; border-bottom: none; padding-top: 15px; }
        .totals tr.grand-total td { color: #0f766e; font-size: 16px; border-bottom: none; padding-top: 15px; }
        .footer { text-align: center; color: #94a3b8; font-size: 10px; margin-top: 80px; border-top: 1px solid #e2e8f0; padding-top: 20px; clear: both; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .badge-attente { background-color: #fef3c7; color: #92400e; }
        .badge-payee { background-color: #dcfce7; color: #166534; }
        .badge-retard { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="title">FACTURE</div>
                <div class="invoice-details">
                    <table>
                        <tr>
                            <th>N° Facture</th>
                            <th>Date d'émission</th>
                            <th>Échéance</th>
                        </tr>
                        <tr>
                            <td>{{ $facture->reference }}</td>
                            <td>{{ $facture->date_emission->format('d/m/Y') }}</td>
                            <td style="{{ $facture->isOverdue() ? 'color: red;' : '' }}">{{ $facture->date_echeance->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </div>
                <div style="margin-top: 15px;">
                    Réf. Commande : {{ $facture->commande->reference }}
                </div>
            </td>
            <td style="width: 50%; text-align: right; vertical-align: top;">
                <h2 style="margin:0; color:#0f766e; font-size: 24px;">PharmaApp</h2>
                <div style="color:#64748b; margin-top:5px;">Plateforme B2B de réassort</div>
                <div style="margin-top: 15px;">
                    @if($facture->status === 'payee')
                        <span class="badge badge-payee">Payée</span>
                    @elseif($facture->isOverdue())
                        <span class="badge badge-retard">En retard</span>
                    @else
                        <span class="badge badge-attente">En attente de paiement</span>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table style="width: 100%;">
        <tr>
            <td style="width: 48%; vertical-align: top;">
                <div class="info-box">
                    <div class="info-title">Émetteur (Fournisseur)</div>
                    <div class="company-name">{{ $facture->commande->fournisseur->name }}</div>
                    <div>{{ $facture->commande->fournisseur->email }}</div>
                </div>
            </td>
            <td style="width: 4%;"></td>
            <td style="width: 48%; vertical-align: top;">
                <div class="info-box">
                    <div class="info-title">Facturé à (Pharmacie)</div>
                    <div class="company-name">{{ $facture->commande->pharmacien->name }}</div>
                    <div>{{ $facture->commande->pharmacien->email }}</div>
                    @if($facture->commande->pharmacien->address)
                        <div>{{ $facture->commande->pharmacien->address }}</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 50%;">Désignation</th>
                <th style="width: 15%; text-align: center;">Quantité</th>
                <th style="width: 17%; text-align: right;">Prix Unitaire</th>
                <th style="width: 18%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facture->commande->items as $item)
                <tr>
                    <td><strong>{{ $item->nom_medicament }}</strong></td>
                    <td style="text-align: center;">{{ $item->quantite }}</td>
                    <td style="text-align: right;">{{ $item->prix_unitaire ? number_format($item->prix_unitaire, 0, ',', ' ') . ' XAF' : 'Sur devis' }}</td>
                    <td style="text-align: right;">{{ $item->prix_unitaire ? number_format($item->prix_unitaire * $item->quantite, 0, ',', ' ') . ' XAF' : 'Sur devis' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <th>Sous-total</th>
                <td>{{ number_format($facture->montant_total, 0, ',', ' ') }} XAF</td>
            </tr>
            <tr class="grand-total">
                <th>TOTAL À PAYER</th>
                <td>{{ number_format($facture->montant_total, 0, ',', ' ') }} XAF</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        En cas de retard de paiement, des pénalités pourront être appliquées conformément aux conditions générales de vente.<br>
        Document généré automatiquement par PharmaApp le {{ now()->format('d/m/Y à H:i') }}
    </div>

</body>
</html>
