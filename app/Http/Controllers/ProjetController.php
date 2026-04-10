<?php

namespace App\Http\Controllers;

use App\Models\Projet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjetController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Projet::with('clients', 'collaborateurs');

        if ($user->isClient()) {
            $query->whereHas('clients', fn($q) => $q->where('users.id', $user->id));
        } elseif ($user->isCollaborateur()) {
            $query->whereHas('collaborateurs', fn($q) => $q->where('users.id', $user->id));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nom', 'like', "%{$search}%");
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }


        $projets = $query->orderBy('created_at', 'desc')->paginate(12);

        $clients = [];
        $collaborateurs = [];
        $contrats = [];
        if ($user->isAdmin() || $user->isCollaborateur()) {
            $clients = User::where('role', 'client')->where('state', 'active')->orderBy('name')->get();
            $collaborateurs = User::where('role', 'collaborateur')->where('state', 'active')->orderBy('name')->get();
            $contrats = \App\Models\Contrat::orderBy('nom')->get();
        }

        return view('projets.index', compact('projets', 'clients', 'collaborateurs', 'contrats'));
    }

    public function create()
    {
        $clients = User::where('role', 'client')->where('state', 'active')->orderBy('name')->get();
        $collaborateurs = User::where('role', 'collaborateur')->where('state', 'active')->orderBy('name')->get();
        $contrats = \App\Models\Contrat::orderBy('nom')->get();
        return view('projets.create', compact('clients', 'collaborateurs', 'contrats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'statut' => ['required', 'in:actif,archive'],
            'date_debut' => ['nullable', 'date'],
            'date_fin_prevue' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'clients' => ['nullable', 'array'],
            'clients.*' => ['exists:users,id'],
            'collaborateurs' => ['nullable', 'array'],
            'collaborateurs.*' => ['exists:users,id'],
            'contrats' => ['nullable', 'array'],
            'contrats.*' => ['exists:contrats,id'],
        ]);

        $projet = Projet::create([
            'nom' => $data['nom'],
            'description' => $data['description'] ?? null,
            'statut' => $data['statut'],
            'date_debut' => $data['date_debut'] ?? null,
            'date_fin_prevue' => $data['date_fin_prevue'] ?? null,
        ]);

        $projet->clients()->sync($data['clients'] ?? []);
        $projet->collaborateurs()->sync($data['collaborateurs'] ?? []);
        $projet->contrats()->sync($data['contrats'] ?? []);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Projet « {$projet->nom} » créé avec succès.",
                'redirect' => route('projets.index')
            ]);
        }

        return redirect()->route('projets.index')
            ->with('success', "Projet « {$projet->nom} » créé avec succès.");
    }

    public function show(Projet $projet)
    {
        $projet->load('clients', 'collaborateurs', 'contrats', 'tickets.collaborateurs');
        return view('projets.show', compact('projet'));
    }

    public function edit(Projet $projet)
    {
        $projet->load('clients', 'collaborateurs');
        $clients = User::where('role', 'client')->where('state', 'active')->orderBy('name')->get();
        $collaborateurs = User::where('role', 'collaborateur')->where('state', 'active')->orderBy('name')->get();
        $contrats = \App\Models\Contrat::orderBy('nom')->get();
        return view('projets.edit', compact('projet', 'clients', 'collaborateurs', 'contrats'));
    }

    public function update(Request $request, Projet $projet)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'statut' => ['required', 'in:actif,archive'],
            'date_debut' => ['nullable', 'date'],
            'date_fin_prevue' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'clients' => ['nullable', 'array'],
            'clients.*' => ['exists:users,id'],
            'collaborateurs' => ['nullable', 'array'],
            'collaborateurs.*' => ['exists:users,id'],
            'contrats' => ['nullable', 'array'],
            'contrats.*' => ['exists:contrats,id'],
        ]);

        $projet->update([
            'nom' => $data['nom'],
            'description' => $data['description'] ?? null,
            'statut' => $data['statut'],
            'date_debut' => $data['date_debut'] ?? null,
            'date_fin_prevue' => $data['date_fin_prevue'] ?? null,
        ]);

        $projet->clients()->sync($data['clients'] ?? []);
        $projet->collaborateurs()->sync($data['collaborateurs'] ?? []);
        $projet->contrats()->sync($data['contrats'] ?? []);

        // Automatiquement assigner les nouveaux collaborateurs à tous les tickets du projet
        $newCollabs = $data['collaborateurs'] ?? [];
        foreach ($projet->tickets as $ticket) {
            $ticket->collaborateurs()->sync($newCollabs);
        }

        return redirect()->route('projets.show', $projet)
            ->with('success', "Projet « {$projet->nom} » mis à jour avec succès.");
    }

    public function destroy(Projet $projet)
    {
        $nom = $projet->nom;
        $projet->delete();
        return redirect()->route('projets.index')
            ->with('success', "Projet « {$nom} » supprimé avec succès.");
    }
}
