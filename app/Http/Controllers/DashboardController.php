<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Projet;
use App\Models\Contrat;
use App\Models\Ticket;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isCollaborateur()) {
            return $this->collaborateurDashboard($user);
        } else {
            return $this->clientDashboard($user);
        }
    }

    private function adminDashboard()
    {
        $stats = [
            'total_clients' => User::where('role', 'client')->count(),
            'total_projets' => Projet::count(),
            'projets_en_cours' => Projet::where('statut', 'en_cours')->count(),
            'total_tickets' => Ticket::count(),
            'tickets_ouverts' => Ticket::where('statut', 'nouveau')->count(),
            'tickets_en_cours' => Ticket::where('statut', 'en_cours')->count(),
            'tickets_urgents' => Ticket::where('priorite', 'haute')->whereIn('statut', ['nouveau', 'en_cours', 'en_attente_client'])->count(),
            'total_contrats' => Contrat::count(),
            'contrats_actifs' => Contrat::where('statut', 'actif')->count(),
            'total_utilisateurs' => User::count(),
            'collaborateurs' => User::where('role', 'collaborateur')->count(),
        ];

        $recentTickets = Ticket::with(['projet', 'collaborateurs'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentProjets = Projet::with('clients')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.admin', compact('stats', 'recentTickets', 'recentProjets'));
    }

    private function collaborateurDashboard(User $user)
    {
        $mesTickets = Ticket::whereHas('collaborateurs', function($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->whereIn('statut', ['nouveau', 'en_cours', 'en_attente_client'])
            ->with(['projet', 'collaborateurs'])
            ->orderBy('created_at', 'desc')
            ->get();

        $mesProjets = $user->projets()
            ->with('clients')
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'mes_tickets_ouverts' => $mesTickets->where('statut', 'nouveau')->count(),
            'mes_tickets_en_cours' => $mesTickets->where('statut', 'en_cours')->count(),
            'mes_projets' => $mesProjets->count(),
            'tickets_urgents' => $mesTickets->where('priorite', 'haute')->count(),
        ];

        return view('dashboard.collaborateur', compact('stats', 'mesTickets', 'mesProjets'));
    }

    private function clientDashboard(User $user)
    {
        $mesProjets = $user->projetsClient()->with('clients', 'contrats')->get();
        $mesTickets = Ticket::whereIn('projet_id', $mesProjets->pluck('id'))
            ->with(['projet', 'collaborateurs'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $ticketsAApprouver = Ticket::whereIn('projet_id', $mesProjets->pluck('id'))
            ->where('type', 'facturable')
            ->whereNull('approuve_client')
            ->with(['projet', 'collaborateurs'])
            ->get();

        $stats = [
            'mes_projets' => $mesProjets->count(),
            'projets_en_cours' => $mesProjets->where('statut', 'actif')->count(),
            'mes_tickets' => Ticket::whereIn('projet_id', $mesProjets->pluck('id'))->count(),
            'tickets_ouverts' => Ticket::whereIn('projet_id', $mesProjets->pluck('id'))->where('statut', 'nouveau')->count(),
            'tickets_a_approuver' => $ticketsAApprouver->count(),
        ];

        return view('dashboard.client', compact('stats', 'mesTickets', 'mesProjets', 'ticketsAApprouver'));
    }
}
