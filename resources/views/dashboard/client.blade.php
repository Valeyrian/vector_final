@extends('layouts.app')

@section('title', 'Mon Dashboard')
@section('page-title', 'Mon Espace')

@section('content')
    <div class="stats-grid" style="padding-top: var(--spacing-xl);">
        <div class="stat-card">
            <div class="stat-icon blue">
                <img src="{{ asset('assets/project.png') }}" alt="Projets" />
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['mes_projets'] }}</div>
                <div class="stat-label">Mes Projets</div>
                <div class="stat-sub">{{ $stats['projets_en_cours'] }} en cours</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon orange">
                <img src="{{ asset('assets/ticket.png') }}" alt="Tickets" />
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['mes_tickets'] }}</div>
                <div class="stat-label">Mes Tickets</div>
                <div class="stat-sub">{{ $stats['tickets_ouverts'] }} ouverts</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon purple">
                <img src="{{ asset('assets/contrat.png') }}" alt="Contrats" />
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ auth()->user()->contratsClient->count() }}</div>
                <div class="stat-label">Mes Contrats</div>
                <div class="stat-sub">Contrats de service</div>
            </div>
        </div>
    </div>

    <div class="dashboard-sections" style="padding-top:0">
        {{-- Tickets à approuver --}}
        @if($ticketsAApprouver->count() > 0)
        <div class="section-card" style="border: 2px solid #f39c12; margin-bottom:var(--spacing-lg)">
            <div class="section-header" style="background: rgba(243, 156, 18, 0.1);">
                <div class="section-title">
                    <span style="font-size:1.2rem">💳</span>
                    Tickets facturables à approuver
                </div>
                <span class="badge" style="background:#f39c12;color:white">{{ $stats['tickets_a_approuver'] }} en attente</span>
            </div>
            <div class="section-body">
                @foreach($ticketsAApprouver as $ticket)
                    <div class="section-item">
                        <div class="section-item-info">
                            <div class="section-item-title">
                                <a href="{{ route('tickets.show', $ticket) }}">{{ $ticket->titre }}</a>
                            </div>
                            <div class="section-item-sub">
                                {{ $ticket->projet?->nom }} — Créé le {{ $ticket->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                        <div class="section-item-meta">
                            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-primary">Voir & Valider</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
        
        {{-- Mes tickets récents --}}
        <div class="section-card">
            <div class="section-header">
                <div class="section-title">
                    <img src="{{ asset('assets/ticket.png') }}" alt="" />
                    Mes derniers tickets
                </div>
                <div style="display:flex; gap:8px;">
                    <a href="{{ route('tickets.create') }}" class="btn btn-sm btn-primary">+ Nouveau</a>
                    <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-secondary">Voir tout</a>
                </div>
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
                                {{ $ticket->projet?->nom ?? 'Sans projet' }} —
                                {{ $ticket->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="section-item-meta">
                            <span class="badge badge-{{ str_replace('_', '-', $ticket->statut) }}">
                                {{ ucfirst(str_replace('_', ' ', $ticket->statut)) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="padding: var(--spacing-xl);">
                        <p>Aucun ticket pour le moment.</p>
                        <a href="{{ route('tickets.create') }}" class="btn btn-primary"
                            style="margin-top: var(--spacing-md);">Créer mon premier ticket</a>
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
                                @if($projet->date_debut && $projet->date_fin)
                                    {{ $projet->date_debut->format('d/m/Y') }} → {{ $projet->date_fin->format('d/m/Y') }}
                                @else
                                    Dates à définir
                                @endif
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
                        <p>Aucun projet associé.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection