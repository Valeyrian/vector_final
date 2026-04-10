<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contrat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContratApiController extends Controller
{
    public function index()
    {
        $contrats = Contrat::with(['clients', 'projets'])->latest()->get();
        return response()->json($contrats);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'heures_totales' => 'required|numeric|min:0',
            'taux_horaire' => 'nullable|numeric|min:0',
            'montant_total' => 'nullable|numeric|min:0',
            'statut' => 'required|string',
            'date_debut' => 'nullable|date',
            'clients' => 'nullable|array',
            'clients.*' => 'exists:users,id',
            'projets' => 'nullable|array',
            'projets.*' => 'exists:projets,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $contrat = Contrat::create($request->all());

        if ($request->has('clients')) {
            $contrat->clients()->sync($request->input('clients'));
        }
        if ($request->has('projets')) {
            $contrat->projets()->sync($request->input('projets'));
        }

        return response()->json([
            'message' => 'Contrat créé avec succès',
            'contrat' => $contrat->load(['clients', 'projets'])
        ], 201);
    }

    public function show($id)
    {
        $contrat = Contrat::with(['clients', 'projets'])->find($id);
        if (!$contrat) return response()->json(['message' => 'Contrat non trouvé'], 404);
        return response()->json($contrat);
    }
}
