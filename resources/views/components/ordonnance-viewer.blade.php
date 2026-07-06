@props(['ordonnance'])

<div class="card overflow-hidden" style="border: 1px solid #e2e8f0; height: 600px; display: flex; flex-direction: column;">
    <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between" style="background: #f8fafc;">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <span class="font-semibold text-slate-700 text-sm">Aperçu du document</span>
        </div>
        <a href="{{ route('client.ordonnances.download', $ordonnance) }}" class="btn btn-ghost" style="padding: 0.4rem 0.75rem; font-size: 0.75rem;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Télécharger
        </a>
    </div>

    <div class="flex-1 bg-slate-100 relative" style="overflow: auto; display: flex; align-items: center; justify-content: center;">
        @if (in_array($ordonnance->mime_type, ['image/jpeg', 'image/png', 'image/jpg']))
            <img src="{{ route('client.ordonnances.preview', $ordonnance) }}" alt="Ordonnance" style="max-width: 100%; object-fit: contain;">
        @elseif ($ordonnance->mime_type === 'application/pdf')
            <iframe src="{{ route('client.ordonnances.preview', $ordonnance) }}" style="width: 100%; height: 100%; border: none;"></iframe>
        @else
            <div class="text-center p-8 text-slate-500">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p>Aperçu non disponible pour ce type de fichier.</p>
                <a href="{{ route('client.ordonnances.download', $ordonnance) }}" class="text-teal-600 hover:underline mt-2 inline-block">Télécharger le fichier</a>
            </div>
        @endif
    </div>
</div>
