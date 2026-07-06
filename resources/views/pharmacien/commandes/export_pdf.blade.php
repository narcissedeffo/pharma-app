<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commande {{ $commande->reference }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            margin-bottom: 30px;
        }
        .header td {
            vertical-align: top;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #0f766e;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 14px;
            color: #64748b;
        }
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .info-title {
            font-weight: bold;
            color: #0f766e;
            margin-bottom: 10px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 5px;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.items th, table.items td {
            border: 1px solid #e2e8f0;
            padding: 10px;
            text-align: left;
        }
        table.items th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: bold;
        }
        table.items tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .notes {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .notes-title {
            font-weight: bold;
            color: #b45309;
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            color: #94a3b8;
            font-size: 10px;
            margin-top: 50px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            background-color: #e2e8f0;
            color: #475569;
        }
    </style>
</head>
<body>

    <table class="header">
        <tr>
            <td style="width: 50%;">
                <div class="title">Bon de Commande</div>
                <div class="subtitle">Réf: {{ $commande->reference }}</div>
                <div style="margin-top: 5px;">
                    <span class="badge">{{ $commande->statusLabel() }}</span>
                </div>
                <div style="margin-top: 10px;">
                    <strong>Date :</strong> {{ $commande->sent_at ? $commande->sent_at->format('d/m/Y H:i') : 'Brouillon' }}
                </div>
            </td>
            <td style="width: 50%; text-align: right;">
                <h2 style="margin:0; color:#0f766e;">PharmaApp</h2>
                <div style="color:#64748b; margin-top:5px;">Plateforme de gestion de réassort</div>
            </td>
        </tr>
    </table>

    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td style="width: 48%; vertical-align: top;">
                <div class="info-box">
                    <div class="info-title">De (Pharmacie)</div>
                    <div style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">{{ $commande->pharmacien->name }}</div>
                    <div>{{ $commande->pharmacien->email }}</div>
                    @if($commande->pharmacien->address)
                        <div>{{ $commande->pharmacien->address }}</div>
                    @endif
                </div>
            </td>
            <td style="width: 4%;"></td>
            <td style="width: 48%; vertical-align: top;">
                <div class="info-box">
                    <div class="info-title">À (Fournisseur)</div>
                    <div style="font-size: 14px; font-weight: bold; margin-bottom: 5px;">{{ $commande->fournisseur->name }}</div>
                    <div>{{ $commande->fournisseur->email }}</div>
                </div>
            </td>
        </tr>
    </table>

    @if($commande->notes)
        <div class="notes">
            <div class="notes-title">Notes / Instructions :</div>
            <div>{{ $commande->notes }}</div>
        </div>
    @endif

    <div style="font-weight: bold; font-size: 14px; margin-bottom: 10px; color: #1e293b;">Détail de la commande</div>
    
    <table class="items">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 75%;">Désignation du produit</th>
                <th style="width: 20%; text-align: center;">Quantité</th>
            </tr>
        </thead>
        <tbody>
            @forelse($commande->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><strong>{{ $item->nom_medicament }}</strong></td>
                    <td style="text-align: center; font-size: 14px; font-weight: bold;">{{ $item->quantite }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #94a3b8;">Aucun produit dans cette commande.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="text-align: right; font-weight: bold; font-size: 14px; padding: 10px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
        Total des articles : {{ $commande->items->count() }}
    </div>

    <div class="footer">
        Généré par PharmaApp le {{ now()->format('d/m/Y à H:i') }}
        <br>
        Ce document sert de bon de commande. Les conditions générales de vente du fournisseur s'appliquent.
    </div>

</body>
</html>
