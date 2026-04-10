<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketCommentaire;
use App\Models\TicketTemps;
use App\Models\Projet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Ticket::with(['projet', 'collaborateurs']);

        if ($user->isClient()) {
            $query->whereHas('projet.clients', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        } elseif ($user->isCollaborateur()) {
            $query->whereHas('collaborateurs', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }



        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        $user = Auth::user();
        $projets = $this->getProjetsForUser($user);
        $agents = User::where('role', 'collaborateur')->orderBy('name')->get();
        return view('tickets.create', compact('projets', 'agents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'statut' => ['required', 'in:nouveau,en_cours,en_attente_client,termine,a_valider,valide,refuse'],
            'priorite' => ['required', 'in:basse,moyenne,haute'],
            'type' => ['required', 'in:inclus,facturable'],
            'temps_estime' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'projet_id' => ['nullable', 'exists:projets,id'],
            'collaborateurs' => ['nullable', 'array'],
            'collaborateurs.*' => ['exists:users,id'],
        ]);

        $ticket = Ticket::create([
            'titre' => $data['titre'],
            'description' => $data['description'] ?? null,
            'statut' => $data['statut'],
            'priorite' => $data['priorite'],
            'type' => $data['type'],
            'temps_estime' => $data['temps_estime'] ?? 0,
            'projet_id' => $data['projet_id'] ?? null,
        ]);

        $collaborateurIds = $data['collaborateurs'] ?? [];
        if ($ticket->projet_id) {
            $projetCollabs = $ticket->projet->collaborateurs->pluck('id')->toArray();
            $collaborateurIds = array_unique(array_merge($collaborateurIds, $projetCollabs));
        }
        $ticket->collaborateurs()->sync($collaborateurIds);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Ticket « {$ticket->titre} » créé avec succès.");
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['projet', 'collaborateurs', 'commentaires.auteur', 'tempsEnregistres.utilisateur']);
        $agents = User::where('role', 'collaborateur')->orderBy('name')->get();
        return view('tickets.show', compact('ticket', 'agents'));
    }

    public function edit(Ticket $ticket)
    {
        $user = Auth::user();
        $projets = $this->getProjetsForUser($user);
        $agents = User::where('role', 'collaborateur')->orderBy('name')->get();
        $ticket->load('collaborateurs');
        return view('tickets.edit', compact('ticket', 'projets', 'agents'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'statut' => ['required', 'in:nouveau,en_cours,en_attente_client,termine,a_valider,valide,refuse'],
            'priorite' => ['required', 'in:basse,moyenne,haute'],
            'type' => ['required', 'in:inclus,facturable'],
            'temps_estime' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'projet_id' => ['nullable', 'exists:projets,id'],
            'collaborateurs' => ['nullable', 'array'],
            'collaborateurs.*' => ['exists:users,id'],
        ]);

        $ticket->update([
            'titre' => $data['titre'],
            'description' => $data['description'] ?? null,
            'statut' => $data['statut'],
            'priorite' => $data['priorite'],
            'type' => $data['type'],
            'temps_estime' => $data['temps_estime'] ?? 0,
            'projet_id' => $data['projet_id'] ?? null,
        ]);

        $collaborateurIds = $data['collaborateurs'] ?? ($ticket->collaborateurs->pluck('id')->toArray());
        if ($ticket->projet_id) {
            $projetCollabs = $ticket->projet->collaborateurs->pluck('id')->toArray();
            $collaborateurIds = array_unique(array_merge($collaborateurIds, $projetCollabs));
        }
        $ticket->collaborateurs()->sync($collaborateurIds);

        if ($ticket->statut === 'refuse') {
            return back()->with('error', 'Ce ticket est refuse et ne peut pas être modifie.');
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Ticket « {$ticket->titre} » mis à jour avec succès.");
    }

    public function approuver(Ticket $ticket)
    {
        if (Auth::user()->isClient() && $ticket->type === 'facturable') {
            $ticket->update(['approuve_client' => true, 'statut' => 'nouveau']);
            return back()->with('success', 'Ticket approuvé avec succès.');
        }
        return back()->with('error', 'Action non autorisée.');
    }

    public function refuser(Request $request, Ticket $ticket)
    {
        if (Auth::user()->isClient() && $ticket->type === 'facturable') {
            $data = $request->validate([
                'motif_refus' => ['required', 'string', 'max:1000'],
            ]);

            $ticket->update([
                'approuve_client' => false,
                'statut' => 'refuse',
                'motif_refus' => $data['motif_refus']
            ]);
            return back()->with('success', 'Ticket refuse et verrouille.');
        }
        return back()->with('error', 'Action non autorisée.');
    }

    public function destroy(Ticket $ticket)
    {
        $titre = $ticket->titre;
        $ticket->delete();
        return redirect()->route('tickets.index')
            ->with('success', "Ticket « {$titre} » supprimé avec succès.");
    }

    // --- Commentaires ---
    public function storeCommentaire(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'contenu' => ['required', 'string', 'max:5000'],
        ]);

        TicketCommentaire::create([
            'ticket_id' => $ticket->id,
            'auteur_id' => Auth::id(),
            'contenu' => $data['contenu'],
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Commentaire ajouté avec succès.');
    }

    // --- Temps ---
    public function storeTemps(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'duree' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:500'],
            'date' => ['required', 'date'],
        ]);

        $temps = TicketTemps::create([
            'ticket_id' => $ticket->id,
            'collaborateur_id' => Auth::id(),
            'duree' => $data['duree'],
            'commentaire' => $data['description'] ?? null,
            'date_travail' => $data['date'],
        ]);

        // Mettre à jour le temps consommé du projet et des contrats
        if ($ticket->projet) {
            $heuresAdd = $data['duree'] / 60;

            // 1. Augmenter le temps du projet
            $ticket->projet->increment('heures_consommees', $heuresAdd);

            // 2. Augmenter le temps des contrats actifs associés
            $contrats = $ticket->projet->contrats()->where('statut', 'actif')->get();
            foreach ($contrats as $contrat) {
                $contrat->increment('heures_consommees', $heuresAdd);

                // Recalculer le montant total si un taux horaire est défini
                if ($contrat->taux_horaire > 0) {
                    $contrat->montant_total = $contrat->heures_consommees * $contrat->taux_horaire;
                    $contrat->save();
                }
            }
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Temps enregistré et reporté sur le projet/contrat avec succès.');
    }

    private function getProjetsForUser(User $user): \Illuminate\Database\Eloquent\Collection
    {
        if ($user->isAdmin()) {
            return Projet::orderBy('nom')->get();
        } elseif ($user->isCollaborateur()) {
            return $user->projets()->orderBy('nom')->get();
        } else {
            return $user->projetsClient()->orderBy('nom')->get();
        }
    }
}
