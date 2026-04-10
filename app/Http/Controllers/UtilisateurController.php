<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UtilisateurController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('surname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('state')) {
            $query->where('state', $request->state);
        }

        $utilisateurs = $query->orderBy('name')->paginate(15);

        return view('utilisateurs.index', compact('utilisateurs'));
    }

    public function create()
    {
        return view('utilisateurs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'surname' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:191', 'unique:users'],
            'company' => ['nullable', 'string', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'code_postal' => ['nullable', 'string', 'max:20'],
            'ville' => ['nullable', 'string', 'max:100'],
            'pays' => ['nullable', 'string', 'max:100'],
            'role' => ['required', 'in:admin,collaborateur,client'],
            'state' => ['required', 'in:active,inactive'],
            'password' => ['required', Password::min(8), 'confirmed'],
        ]);

        $utilisateur = User::create([
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'company' => $data['company'] ?? '',
            'adresse' => $data['adresse'] ?? null,
            'code_postal' => $data['code_postal'] ?? null,
            'ville' => $data['ville'] ?? null,
            'pays' => $data['pays'] ?? null,
            'role' => $data['role'],
            'state' => $data['state'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('utilisateurs.index')
            ->with('success', "Utilisateur « {$utilisateur->full_name} » créé avec succès.");
    }

    public function show(User $utilisateur)
    {
        $utilisateur->load('projets', 'projetsClient', 'contratsClient');
        return view('utilisateurs.show', compact('utilisateur'));
    }

    public function edit(User $utilisateur)
    {
        return view('utilisateurs.edit', compact('utilisateur'));
    }

    public function update(Request $request, User $utilisateur)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'surname' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:191', 'unique:users,email,' . $utilisateur->id],
            'company' => ['nullable', 'string', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'code_postal' => ['nullable', 'string', 'max:20'],
            'ville' => ['nullable', 'string', 'max:100'],
            'pays' => ['nullable', 'string', 'max:100'],
            'role' => ['required', 'in:admin,collaborateur,client'],
            'state' => ['required', 'in:active,inactive'],
            'password' => ['nullable', Password::min(8), 'confirmed'],
        ]);

        $updateData = [
            'name' => $data['name'],
            'surname' => $data['surname'],
            'email' => $data['email'],
            'company' => $data['company'] ?? '',
            'adresse' => $data['adresse'] ?? null,
            'code_postal' => $data['code_postal'] ?? null,
            'ville' => $data['ville'] ?? null,
            'pays' => $data['pays'] ?? null,
            'role' => $data['role'],
            'state' => $data['state'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $utilisateur->update($updateData);

        return redirect()->route('utilisateurs.show', $utilisateur)
            ->with('success', "Utilisateur « {$utilisateur->full_name} » mis à jour avec succès.");
    }

    public function destroy(User $utilisateur)
    {
        $nom = $utilisateur->full_name;
        $utilisateur->delete();
        return redirect()->route('utilisateurs.index')
            ->with('success', "Utilisateur « {$nom} » supprimé avec succès.");
    }
}
