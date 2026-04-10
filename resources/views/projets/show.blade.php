@extends('layouts.app')
@section('title', $projet->nom)
@section('page-title', 'Fiche Projet')
@section('content')
    <div class="breadcrumb">
        <a href="{{ route('projets.index') }}">Projets</a>
        <span class="breadcrumb-sep">/</span><span>{{ $projet->nom }}</span>
    </div>
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ $projet->nom }}</h2>
            <p>
                <span
                    class="badge badge-{{ str_replace('_', '-', $projet->statut) }}">{{ ucfirst(str_replace('_', ' ', $projet->statut)) }}</span>
            </p>
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->isCollaborateur())
            <div class="page-header-actions">
                <a href="{{ route('projets.edit', $projet) }}" class="btn btn-warning">Modifier</a>
                @if(auth()->user()->isAdmin())
                    <form method="POST" action="{{ route('projets.destroy', $projet) }}"
                        onsubmit="return confirm('Supprimer ce projet ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                @endif
            </div>
        @endif
    </div>
    <div class="detail-grid">
        <div class="detail-section">
            <div class="detail-header">📋 Informations</div>
            <div class="detail-body">
                <div class="detail-row"><span class="detail-label">Nom</span><span
                        class="detail-value">{{ $projet->nom }}</span></div>
                <div class="detail-row"><span class="detail-label">Statut</span><span class="detail-value"><span
                            class="badge badge-{{ str_replace('_', '-', $projet->statut) }}">{{ ucfirst(str_replace('_', ' ', $projet->statut)) }}</span></span>
                </div>
                <div class="detail-row"><span class="detail-label">Date début</span><span
                        class="detail-value">{{ $projet->date_debut?->format('d/m/Y') ?? '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">Date fin prévue</span><span
                        class="detail-value">{{ $projet->date_fin_prevue?->format('d/m/Y') ?? '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">Créé le</span><span
                        class="detail-value">{{ $projet->created_at->format('d/m/Y') }}</span></div>
            </div>
        </div>
        <div class="detail-section">
            <div class="detail-header">📝 Description</div>
            <div class="detail-body">
                <p style="color:var(--text-secondary);font-size:var(--font-size-sm);line-height:1.6">
                    {{ $projet->description ?: 'Aucune description.' }}</p>
                <div style="margin-top:var(--spacing-lg)">
                    <div class="detail-header" style="border:none;padding:0 0 var(--spacing-sm) 0;">👥 Clients</div>
                    @forelse($projet->clients as $client)
                        <div class="detail-row"><span class="detail-label">{{ $client->full_name }}</span><span
                                class="detail-value"><a href="{{ route('utilisateurs.show', $client) }}">Voir</a></span></div>
                    @empty <p style="color:var(--text-muted);font-size:var(--font-size-sm)">Aucun client</p>@endforelse
                </div>
                <div style="margin-top:var(--spacing-md)">
                    <div class="detail-header" style="border:none;padding:0 0 var(--spacing-sm) 0;">🤝 Collaborateurs</div>
                    @forelse($projet->collaborateurs as $co)
                        <div class="detail-row"><span class="detail-label">{{ $co->name }} {{ $co->surname }}</span><span
                                class="detail-value">{{ $co->email }}</span></div>
                    @empty <p style="color:var(--text-muted);font-size:var(--font-size-sm)">Aucun collaborateur</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="detail-section full-width">
            <div class="detail-header">🎫 Tickets ({{ $projet->tickets->count() }})</div>
            <div class="detail-body" style="padding:0">
                @forelse($projet->tickets as $ticket)
                    <div class="section-item">
                        <div class="priority-dot {{ $ticket->priorite }}"></div>
                        <div class="section-item-info" style="margin-left:10px">
                            <div class="section-item-title"><a
                                    href="{{ route('tickets.show', $ticket) }}">{{ $ticket->titre }}</a></div>
                            <div class="section-item-sub">{{ $ticket->collaborateurs->pluck('name')->join(', ') ?: 'Non assigné' }} —
                                {{ $ticket->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="section-item-meta">
                            <span
                                class="badge badge-{{ str_replace('_', '-', $ticket->statut) }}">{{ ucfirst(str_replace('_', ' ', $ticket->statut)) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state" style="padding:var(--spacing-xl)">
                        <p>Aucun ticket pour ce projet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection