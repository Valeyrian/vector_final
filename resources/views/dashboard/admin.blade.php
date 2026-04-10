@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@section('content')
    {{-- Stats Grid --}}
    <div class="stats-grid" style="padding-top: var(--spacing-xl);">
        {{-- Clients --}}
        <a href="{{ route('utilisateurs.index', ['role' => 'client']) }}" class="stat-card" style="text-decoration:none;">
            <div class="stat-icon blue">
                <img src="{{ asset('assets/client.png') }}" alt="Clients" />
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['total_clients'] }}</div>
                <div class="stat-label">Clients</div>
            </div>
        </a>

        {{-- Projets --}}
        <a href="{{ route('projets.index') }}" class="stat-card" style="text-decoration:none;">
            <div class="stat-icon green">
                <img src="{{ asset('assets/project.png') }}" alt="Projets" />
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['total_projets'] }}</div>
                <div class="stat-label">Projets</div>
                <div class="stat-sub">{{ $stats['projets_en_cours'] }} en cours</div>
            </div>
        </a>

        {{-- Tickets --}}
        <a href="{{ route('tickets.index') }}" class="stat-card" style="text-decoration:none;">
            <div class="stat-icon orange">
                <img src="{{ asset('assets/ticket.png') }}" alt="Tickets" />
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['total_tickets'] }}</div>
                <div class="stat-label">Tickets</div>
                <div class="stat-sub">{{ $stats['tickets_ouverts'] }} ouverts</div>
            </div>
        </a>

        {{-- Tickets prioritaires --}}
        <div class="stat-card">
            <div class="stat-icon red">
                <img src="{{ asset('assets/urgent.png') }}" alt="Urgents" />
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['tickets_urgents'] }}</div>
                <div class="stat-label">Prioritaires</div>
                <div class="stat-sub">Priorité haute</div>
            </div>
        </div>

        {{-- Contrats --}}
        <a href="{{ route('contrats.index') }}" class="stat-card" style="text-decoration:none;">
            <div class="stat-icon purple">
                <img src="{{ asset('assets/contrat.png') }}" alt="Contrats" />
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['total_contrats'] }}</div>
                <div class="stat-label">Contrats</div>
                <div class="stat-sub">{{ $stats['contrats_actifs'] }} actifs</div>
            </div>
        </a>

        {{-- Utilisateurs --}}
        <a href="{{ route('utilisateurs.index') }}" class="stat-card" style="text-decoration:none;">
            <div class="stat-icon blue">
                <img src="{{ asset('assets/utilisateur.png') }}" alt="Utilisateurs" />
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['total_utilisateurs'] }}</div>
                <div class="stat-label">Utilisateurs</div>
                <div class="stat-sub">{{ $stats['collaborateurs'] }} collaborateurs</div>
            </div>
        </a>
    </div>

    {{-- Sections récentes --}}
    <div class="dashboard-sections">
        {{-- Tickets récents --}}
        <div class="section-card">
            <div class="section-header">
                <div class="section-title">
                    <img src="{{ asset('assets/ticket.png') }}" alt="" />
                    Tickets récents
                </div>
                <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-secondary">Voir tout</a>
            </div>
            <div class="section-body">
                @forelse($recentTickets as $ticket)
                    <div class="section-item">
                        <div class="priority-dot {{ $ticket->priorite }}"></div>
                        <div class="section-item-info" style="margin-left:10px;">
                            <div class="section-item-title">
                                <a href="{{ route('tickets.show', $ticket) }}">{{ $ticket->titre }}</a>
                            </div>
                            <div class="section-item-sub">
                                {{ $ticket->projet?->nom ?? 'Hors projet' }} —
                                {{ $ticket->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="section-item-meta">
                            <span class="badge badge-{{ str_replace('_', '-', $ticket->statut) }}">
                                {{ ucfirst(str_replace('_', ' ', $ticket->statut)) }}
                            </span>
                            <span class="badge badge-{{ $ticket->priorite }}">
                                {{ ucfirst($ticket->priorite) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="padding: var(--spacing-xl);">
                        <p>Aucun ticket pour le moment.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Projets récents --}}
        <div class="section-card">
            <div class="section-header">
                <div class="section-title">
                    <img src="{{ asset('assets/project.png') }}" alt="" />
                    Projets récents
                </div>
                <a href="{{ route('projets.index') }}" class="btn btn-sm btn-secondary">Voir tout</a>
            </div>
            <div class="section-body">
                @forelse($recentProjets as $projet)
                    <div class="section-item">
                        <div class="section-item-info">
                            <div class="section-item-title">
                                <a href="{{ route('projets.show', $projet) }}">{{ $projet->nom }}</a>
                            </div>
                            <div class="section-item-sub">
                                {{ $projet->clients->map->full_name->join(', ') ?: 'Aucun client' }}
                            </div>
                        </div>
                        <div class="section-item-meta">
                            <span class="badge badge-{{ str_replace('_', '-', $projet->statut) }}">
                                {{ ucfirst(str_replace('_', ' ', $projet->statut)) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="padding: var(--spacing-xl);">
                        <p>Aucun projet pour le moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Accès rapides --}}
    <div style="padding: 0 var(--spacing-xl) var(--spacing-xl);">
        <div class="section-card">
            <div class="section-header">
                <div class="section-title">⚡ Accès rapides</div>
            </div>
            <div style="display:flex; gap: var(--spacing-sm); padding: var(--spacing-lg); flex-wrap: wrap;">
                <button onclick="openQuickAdd('Ticket')" class="btn btn-primary">
                    <img src="{{ asset('assets/ticket.png') }}" alt="" style="filter:brightness(10);" />
                    Quick Ticket (API)
                </button>
                <button onclick="openQuickAdd('Projet')" class="btn btn-warning">
                    <img src="{{ asset('assets/project.png') }}" alt="" style="filter:brightness(10);" />
                    Quick Projet (API)
                </button>
                <button onclick="openQuickAdd('Contrat')" class="btn btn-secondary">
                    <img src="{{ asset('assets/contrat.png') }}" alt="" />
                    Quick Contrat (API)
                </button>
                <button onclick="openQuickAdd('User')" class="btn btn-secondary">
                    <img src="{{ asset('assets/utilisateur.png') }}" alt="" />
                    Quick User (API)
                </button>
                
                <div style="width:100%; height:1px; background:#eee; margin:10px 0;"></div>
                
                <a href="{{ route('tickets.create') }}" class="btn btn-sm btn-secondary">Classic Ticket</a>
                <a href="{{ route('utilisateurs.create', ['role' => 'client']) }}" class="btn btn-sm btn-secondary">Classic Client</a>
                <a href="{{ route('projets.create') }}" class="btn btn-sm btn-secondary">Classic Projet</a>
                <a href="{{ route('contrats.create') }}" class="btn btn-sm btn-secondary">Classic Contrat</a>
            </div>
        </div>
    </div>
    {{-- Modals API --}}
    @include('dashboard.partials.modals-api')
@endsection