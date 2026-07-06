<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Ordonnance;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrdonnanceController extends Controller
{
    /**
     * Liste des ordonnances du client connecté, avec filtres par statut.
     */
    public function index(Request $request): View
    {
        $status = $request->get('status', 'all');

        $query = $request->user()
            ->ordonnancesClient()
            ->with('pharmacien')
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $ordonnances = $query->paginate(10)->withQueryString();

        // Compter par statut pour les onglets
        $counts = $request->user()->ordonnancesClient()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('client.ordonnances.index', compact('ordonnances', 'status', 'counts'));
    }

    /**
     * Formulaire d'upload.
     */
    public function create(): View
    {
        return view('client.ordonnances.create');
    }

    /**
     * Enregistre le fichier uploadé (statut = brouillon, pas encore publié).
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'fichier' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $file = $request->file('fichier');
        $path = $file->store('ordonnances/' . $request->user()->id, 'local');

        $ordonnance = Ordonnance::create([
            'client_id'         => $request->user()->id,
            'original_filename' => $file->getClientOriginalName(),
            'file_path'         => $path,
            'mime_type'         => $file->getClientMimeType(),
            'file_size'         => $file->getSize(),
            'status'            => 'brouillon',
            'expires_at'        => now()->addMonths(3)->toDateString(), // 3 mois légaux
        ]);

        $ordonnance->histories()->create([
            'user_id'     => $request->user()->id,
            'from_status' => null,
            'to_status'   => 'brouillon',
            'comment'     => 'Ordonnance déposée.',
        ]);

        return redirect()->route('client.ordonnances.show', $ordonnance)
            ->with('status', 'Ordonnance déposée. Vous pouvez maintenant la publier vers une pharmacie.');
    }

    /**
     * Détail d'une ordonnance (avec historique).
     */
    public function show(Request $request, Ordonnance $ordonnance): View
    {
        abort_unless($ordonnance->isOwnedBy($request->user()), 403);

        $ordonnance->load('histories.user', 'pharmacien', 'items', 'pickupSlot');
        $pharmaciens = $this->pharmaciensDisponibles($request->user());

        return view('client.ordonnances.show', compact('ordonnance', 'pharmaciens'));
    }

    /**
     * Publie l'ordonnance vers la pharmacie choisie.
     */
    public function publish(Request $request, Ordonnance $ordonnance): RedirectResponse
    {
        abort_unless($ordonnance->isOwnedBy($request->user()), 403);

        if ($ordonnance->status !== 'brouillon') {
            return back()->withErrors(['pharmacien_id' => 'Cette ordonnance a déjà été publiée.']);
        }

        $validated = $request->validate([
            'pharmacien_id' => ['required', 'exists:users,id'],
        ]);

        $ordonnance->update([
            'pharmacien_id' => $validated['pharmacien_id'],
            'published_at'  => now(),
        ]);

        $ordonnance->moveTo('en_attente', $request->user(), 'Ordonnance publiée vers la pharmacie sélectionnée.');

        return redirect()->route('client.ordonnances.show', $ordonnance)
            ->with('status', 'Ordonnance publiée avec succès.');
    }

    /**
     * Réaffecte une ordonnance refusée vers une nouvelle pharmacie.
     * Remet le statut en brouillon et libère le pharmacien précédent.
     */
    public function reaffecter(Request $request, Ordonnance $ordonnance): RedirectResponse
    {
        abort_unless($ordonnance->isOwnedBy($request->user()), 403);
        abort_unless($ordonnance->status === 'refusee', 400);

        $ordonnance->update([
            'pharmacien_id' => null,
            'status'        => 'brouillon',
        ]);

        $ordonnance->histories()->create([
            'user_id'     => $request->user()->id,
            'from_status' => 'refusee',
            'to_status'   => 'brouillon',
            'comment'     => 'Réaffectation : le client a choisi de renvoyer l\'ordonnance vers une autre pharmacie.',
        ]);

        return redirect()->route('client.ordonnances.show', $ordonnance)
            ->with('status', 'Ordonnance réinitialisée. Vous pouvez maintenant choisir une autre pharmacie.');
    }

    /**
     * Confirmer un créneau de retrait proposé par le pharmacien.
     */
    public function confirmPickup(Request $request, Ordonnance $ordonnance): RedirectResponse
    {
        abort_unless($ordonnance->isOwnedBy($request->user()), 403);
        abort_unless($ordonnance->pickupSlot && !$ordonnance->pickupSlot->isConfirmed(), 400);

        $ordonnance->pickupSlot->update(['confirmed_at' => now()]);

        return back()->with('status', 'Créneau de retrait confirmé !');
    }

    /**
     * Soumettre une notation post-retrait (1 à 5 étoiles).
     */
    public function rate(Request $request, Ordonnance $ordonnance): RedirectResponse
    {
        abort_unless($ordonnance->isOwnedBy($request->user()), 403);
        abort_unless($ordonnance->canBeRated(), 403);

        $validated = $request->validate([
            'rating'         => ['required', 'integer', 'min:1', 'max:5'],
            'rating_comment' => ['nullable', 'string', 'max:500'],
        ]);

        $ordonnance->update([
            'rating'         => $validated['rating'],
            'rating_comment' => $validated['rating_comment'] ?? null,
            'rated_at'       => now(),
        ]);

        return back()->with('status', 'Merci pour votre évaluation !');
    }

    /**
     * Téléchargement sécurisé du fichier.
     */
    public function download(Request $request, Ordonnance $ordonnance): StreamedResponse
    {
        $user = $request->user();

        abort_unless(
            $ordonnance->isOwnedBy($user) || $ordonnance->isAssignedTo($user) || $user->hasRole('admin'),
            403
        );

        abort_unless(Storage::disk('local')->exists($ordonnance->file_path), 404);

        return Storage::disk('local')->download($ordonnance->file_path, $ordonnance->original_filename);
    }

    /**
     * Aperçu inline sécurisé du fichier (stream).
     */
    public function preview(Request $request, Ordonnance $ordonnance)
    {
        $user = $request->user();

        abort_unless(
            $ordonnance->isOwnedBy($user) || $ordonnance->isAssignedTo($user) || $user->hasRole('admin'),
            403
        );

        abort_unless(Storage::disk('local')->exists($ordonnance->file_path), 404);

        $content = Storage::disk('local')->get($ordonnance->file_path);
        $mimeType = $ordonnance->mime_type;

        return response($content, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $ordonnance->original_filename . '"')
            ->header('Cache-Control', 'private, no-store');
    }

    /**
     * Retourne les pharmaciens triés par proximité si coordonnées fournies, sinon par nom.
     */
    private function pharmaciensDisponibles(User $user)
    {
        return User::whereHas('role', fn ($q) => $q->where('slug', 'pharmacien'))
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
    }
}
