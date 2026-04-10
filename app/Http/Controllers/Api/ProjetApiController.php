<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjetApiController extends Controller
{
    public function index()
    {
        $projets = Projet::with(['clients', 'collaborateurs', 'contrats'])->latest()->get();
        return response()->json($projets);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'statut' => 'required|string',
            'date_debut' => 'nullable|date',
            'date_fin_prevue' => 'nullable|date',
            'clients' => 'nullable|array',
            'clients.*' => 'exists:users,id',
            'collaborateurs' => 'nullable|array',
            'collaborateurs.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $projet = Projet::create($request->all());

        if ($request->has('clients')) {
            $projet->clients()->sync($request->input('clients'));
        }
        if ($request->has('collaborateurs')) {
            $projet->collaborateurs()->sync($request->input('collaborateurs'));
        }
        if ($request->has('contrats')) {
            $projet->contrats()->sync($request->input('contrats'));
        }

        return response()->json([
            'message' => 'Projet créé avec succès',
            'projet' => $projet->load(['clients', 'collaborateurs', 'contrats'])
        ], 201);
    }

    public function show($id)
    {
        $projet = Projet::with(['clients', 'collaborateurs', 'contrats', 'tickets'])->find($id);
        if (!$projet) return response()->json(['message' => 'Projet non trouvé'], 404);
        return response()->json($projet);
    }
}
