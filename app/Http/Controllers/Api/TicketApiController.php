<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketTemps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TicketApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Ticket::with(['projet', 'collaborateurs'])->latest()->get();
        return response()->json($tickets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'projet_id' => 'nullable|exists:projets,id',
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'statut' => 'required|string',
            'priorite' => 'nullable|string',
            'type' => 'required|string',
            'temps_estime' => 'nullable|numeric',
            'collaborateurs' => 'nullable|array',
            'collaborateurs.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $ticket = Ticket::create($request->all());

        if ($request->has('collaborateurs')) {
            $ticket->collaborateurs()->sync($request->input('collaborateurs'));
        }

        return response()->json([
            'message' => 'Ticket créé avec succès',
            'ticket' => $ticket->load(['projet', 'collaborateurs'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $ticket = Ticket::with(['projet', 'collaborateurs', 'commentaires.auteur', 'tempsEnregistres.utilisateur'])->find($id);
        if (!$ticket) return response()->json(['message' => 'Ticket non trouvé'], 404);
        return response()->json($ticket);
    }

    public function storeTemps(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        if (!$ticket) return response()->json(['message' => 'Ticket non trouvé'], 404);

        $validator = Validator::make($request->all(), [
            'duree' => 'required|integer|min:1',
            'description' => 'nullable|string|max:500',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $temps = TicketTemps::create([
            'ticket_id' => $ticket->id,
            'collaborateur_id' => $request->user()->id,
            'duree' => $request->input('duree'),
            'commentaire' => $request->input('description'),
            'date_travail' => $request->input('date'),
        ]);

        if ($ticket->projet) {
            $heuresAdd = $request->input('duree') / 60;
            $ticket->projet->increment('heures_consommees', $heuresAdd);
            
            $contrats = $ticket->projet->contrats()->where('statut', 'actif')->get();
            foreach ($contrats as $contrat) {
                $contrat->increment('heures_consommees', $heuresAdd);
                
                if ($contrat->taux_horaire > 0) {
                    $contrat->montant_total = $contrat->heures_consommees * $contrat->taux_horaire;
                    $contrat->save();
                }
            }
        }

        return response()->json([
            'message' => 'Temps enregistré avec succès',
            'temps' => $temps
        ], 201);
    }
}
