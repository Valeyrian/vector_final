<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContratController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Contrat::with('clients', 'projets');

        if ($user->isClient()) {
            $query->whereHas('clients', fn($q) => $q->where('users.id', $user->id));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nom', 'like', "%{$search}%");
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }



        $contrats = $query->orderBy('created_at', 'desc')->paginate(12);

        $clients = [];
        $projets = [];
        if ($user->isAdmin()) {
            $clients = \App\Models\User::where('role', 'client')->where('state', 'active')->orderBy('name')->get();
            $projets = Projet::orderBy('nom')->get();
        }

        return view('contrats.index', compact('contrats', 'clients', 'projets'));
    }

    public function create()
    {
        $clients = \App\Models\User::where('role', 'client')->where('state', 'active')->orderBy('name')->get();
        $projets = Projet::orderBy('nom')->get();
        return view('contrats.create', compact('clients', 'projets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'heures_totales' => ['nullable', 'numeric', 'min:0'],
            'heures_consommees' => ['nullable', 'numeric', 'min:0'],
            'taux_horaire' => ['nullable', 'numeric', 'min:0'],
            'montant_total' => ['nullable', 'numeric', 'min:0'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after_or_equal:date_debut'],
            'conditions' => ['nullable', 'string'],
            'statut' => ['required', 'in:actif,inactif,termine'],
            'clients' => ['nullable', 'array'],
            'clients.*' => ['exists:users,id'],
            'projets' => ['nullable', 'array'],
            'projets.*' => ['exists:projets,id'],
        ]);

        $contrat = Contrat::create([
            'nom' => $data['nom'],
            'heures_totales' => $data['heures_totales'] ?? 0,
            'heures_consommees' => $data['heures_consommees'] ?? 0,
            'taux_horaire' => $data['taux_horaire'] ?? 0,
            'montant_total' => $data['montant_total'] ?? 0,
            'date_debut' => $data['date_debut'],
            'date_fin' => $data['date_fin'],
            'conditions' => $data['conditions'] ?? null,
            'statut' => $data['statut'],
        ]);

        $contrat->clients()->sync($data['clients'] ?? []);
        $contrat->projets()->sync($data['projets'] ?? []);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Contrat « {$contrat->nom} » créé avec succès.",
                'redirect' => route('contrats.index')
            ]);
        }

        return redirect()->route('contrats.index')
            ->with('success', "Contrat « {$contrat->nom} » créé avec succès.");
    }

    public function show(Contrat $contrat)
    {
        $contrat->load('clients', 'projets');
        return view('contrats.show', compact('contrat'));
    }

    public function edit(Contrat $contrat)
    {
        $contrat->load('clients', 'projets');
        $clients = \App\Models\User::where('role', 'client')->where('state', 'active')->orderBy('name')->get();
        $projets = Projet::orderBy('nom')->get();
        return view('contrats.edit', compact('contrat', 'clients', 'projets'));
    }

    public function update(Request $request, Contrat $contrat)
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'heures_totales' => ['nullable', 'numeric', 'min:0'],
            'heures_consommees' => ['nullable', 'numeric', 'min:0'],
            'taux_horaire' => ['nullable', 'numeric', 'min:0'],
            'montant_total' => ['nullable', 'numeric', 'min:0'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after_or_equal:date_debut'],
            'conditions' => ['nullable', 'string'],
            'statut' => ['required', 'in:actif,inactif,termine'],
            'clients' => ['nullable', 'array'],
            'clients.*' => ['exists:users,id'],
            'projets' => ['nullable', 'array'],
            'projets.*' => ['exists:projets,id'],
        ]);

        $contrat->update([
            'nom' => $data['nom'],
            'heures_totales' => $data['heures_totales'] ?? 0,
            'heures_consommees' => $data['heures_consommees'] ?? 0,
            'taux_horaire' => $data['taux_horaire'] ?? 0,
            'montant_total' => $data['montant_total'] ?? 0,
            'date_debut' => $data['date_debut'],
            'date_fin' => $data['date_fin'],
            'conditions' => $data['conditions'] ?? null,
            'statut' => $data['statut'],
        ]);

        $contrat->clients()->sync($data['clients'] ?? []);
        $contrat->projets()->sync($data['projets'] ?? []);

        return redirect()->route('contrats.show', $contrat)
            ->with('success', "Contrat « {$contrat->nom} » mis à jour avec succès.");
    }

    public function destroy(Contrat $contrat)
    {
        $nom = $contrat->nom;
        $contrat->delete();
        return redirect()->route('contrats.index')
            ->with('success', "Contrat « {$nom} » supprimé avec succès.");
    }
}
