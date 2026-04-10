@extends('layouts.app')

@section('title', 'Mon Dashboard')
@section('page-title', 'Mes Missions')

@section('content')
    <div class="stats-grid" style="padding-top: var(--spacing-xl);">
        <div class="stat-card">
            <div class="stat-icon orange">
                <img src="{{ asset('assets/ticket.png') }}" alt="Tickets" />
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['mes_tickets_ouverts'] }}</div>
                <div class="stat-label">Tickets ouverts</div>
                <div class="stat-sub">Assignés à moi</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon blue">
                <img src="{{ asset('assets/heures-douverture.png') }}" alt="En cours" />
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['mes_tickets_en_cours'] }}</div>
                <div class="stat-label">En cours</div>
                <div class="stat-sub">Tickets actifs</div>
            </div>
        </div>

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

        <div class="stat-card">
            <div class="stat-icon green">
                <img src="{{ asset('assets/project.png') }}" alt="Projets" />
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['mes_projets'] }}</div>
                <div class="stat-label">Mes Projets</div>
                <div class="stat-sub">Actifs</div>
            </div>
        </div>
    </div>

    <div class="dashboard-sections">
        {{-- Mes tickets --}}
        <div class="section-card">
            <div class="section-header">
                <div class="section-title">
                    <img src="{{ asset('assets/ticket.png') }}" alt="" />
                    Mes Tickets assignés
                </div>
                <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-secondary">Voir tout</a>
            </div>
            <div class="section-body">
                @forelse($mesTickets as $ticket)
                    <div class="section-item">
                        <div class="priority-dot {{ $ticket->priorite }}"></div>
                        <div class="section-item-info" style="margin-left:10px;">
                            <div class="section-item-title">
                                <a href="{{ route('tickets.show', $ticket) }}">{{ $ticket->titre }}</a>
                            </div>
                            <div class="section-item-sub">
                                {{ $ticket->projet?->nom ?? 'Sans projet' }}
                            </div>
                        </div>
                        <div class="section-item-meta">
                            <span class="badge badge-{{ str_replace('_', '-', $ticket->statut) }}">
                                {{ ucfirst(str_replace('_', ' ', $ticket->statut)) }}
                            </span>
                            <span class="badge badge-{{ $ticket->priorite }}">{{ ucfirst($ticket->priorite) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="padding: var(--spacing-xl);">
                        <p>Aucun ticket assigné pour le moment. 🎉</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Mes projets --}}
        <div class="section-card">
            <div class="section-header">
                <div class="section-title">
                    <img src="{{ asset('assets/project.png') }}" alt="" />
                    Mes Projets
                </div>
                <a href="{{ route('projets.index') }}" class="btn btn-sm btn-secondary">Voir tout</a>
            </div>
            <div class="section-body">
                @forelse($mesProjets as $projet)
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
                            <span class="badge badge-{{ $projet->priorite }}">{{ ucfirst($projet->priorite) }}</span>
                            <span class="badge badge-{{ str_replace('_', '-', $projet->statut) }}">
                                {{ ucfirst(str_replace('_', ' ', $projet->statut)) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="padding: var(--spacing-xl);">
                        <p>Aucun projet associé.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection