<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de Livraison {{ $facture->bl_reference }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #333; margin: 0; padding: 0; }
        .header { width: 100%; margin-bottom: 40px; }
        .title { font-size: 24px; font-weight: bold; color: #1e293b; text-transform: uppercase; letter-spacing: 1px; }
        .info-box { border: 1px solid #cbd5e1; border-radius: 4px; padding: 15px; min-height: 100px; }
        .info-title { font-weight: bold; color: #64748b; font-size: 10px; text-transform: uppercase; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
        .company-name { font-size: 14px; font-weight: bold; margin-bottom: 5px; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 30px; }
        table.items th { background-color: #f1f5f9; color: #333; text-align: left; padding: 10px; font-size: 11px; text-transform: uppercase; border: 1px solid #cbd5e1; }
        table.items td { border: 1px solid #cbd5e1; padding: 10px; font-size: 12px; }
        .signature-box { margin-top: 50px; border: 1px dashed #94a3b8; padding: 20px; height: 100px; width: 45%; float: right; border-radius: 4px; }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="title">BON DE LIVRAISON</div>
                <div style="margin-top: 15px; font-size: 14px;">
                    <strong>N° BL :</strong> {{ $facture->bl_reference }}<br>
                    <strong>Date :</strong> {{ $facture->date_emission->format('d/m/Y') }}<br>
                    <strong>Réf. Commande :</strong> {{ $facture->commande->reference }}
                </div>
            </td>
            <td style="width: 50%; text-align: right; vertical-align: top;">
                <h2 style="margin:0; color:#1e293b; font-size: 20px;">PharmaApp</h2>
            </td>
        </tr>
    </table>

    <table style="width: 100%;">
        <tr>
            <td style="width: 48%; vertical-align: top;">
                <div class="info-box">
                    <div class="info-title">Fournisseur (Expéditeur)</div>
                    <div class="company-name">{{ $facture->commande->fournisseur->name }}</div>
                    <div>{{ $facture->commande->fournisseur->email }}</div>
                </div>
            </td>
            <td style="width: 4%;"></td>
            <td style="width: 48%; vertical-align: top;">
                <div class="info-box">
                    <div class="info-title">Destinataire (Pharmacie)</div>
                    <div class="company-name">{{ $facture->commande->pharmacien->name }}</div>
                    @if($facture->commande->pharmacien->address)
                        <div>{{ $facture->commande->pharmacien->address }}</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    @if($facture->commande->notes)
        <div style="margin-top: 20px; padding: 10px; background-color: #f8fafc; border-left: 4px solid #94a3b8;">
            <strong>Instructions de livraison :</strong><br>
            {{ $facture->commande->notes }}
        </div>
    @endif

    <table class="items">
        <thead>
            <tr>
                <th style="width: 10%;">Cocher</th>
                <th style="width: 70%;">Désignation du produit</th>
                <th style="width: 20%; text-align: center;">Quantité livrée</th>
            </tr>
        </thead>
        <tbody>
            @foreach($facture->commande->items as $item)
                <tr>
                    <td style="text-align: center;"><div style="width: 15px; height: 15px; border: 1px solid #64748b; display: inline-block;"></div></td>
                    <td><strong>{{ $item->nom_medicament }}</strong></td>
                    <td style="text-align: center; font-size: 14px; font-weight: bold;">{{ $item->quantite }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-box">
        <div style="font-weight: bold; color: #64748b; font-size: 10px; text-transform: uppercase;">Cachet et Signature (Pharmacien)</div>
        <div style="font-size: 10px; color: #94a3b8; margin-top: 5px;">Date de réception : _____/_____/_________</div>
    </div>

</body>
</html>
