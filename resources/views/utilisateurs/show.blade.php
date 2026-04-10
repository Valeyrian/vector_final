@extends('layouts.app')
@section('title', $utilisateur->name . ' ' . $utilisateur->surname)
@section('page-title', 'Fiche Utilisateur')
@section('content')
    <div class="breadcrumb"><a href="{{ route('utilisateurs.index') }}">Utilisateurs</a><span
            class="breadcrumb-sep">/</span><span>{{ $utilisateur->name }} {{ $utilisateur->surname }}</span></div>
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ $utilisateur->name }} {{ $utilisateur->surname }}</h2>
            <p><span class="badge badge-{{ $utilisateur->role }}">{{ ucfirst($utilisateur->role) }}</span> <span
                    class="badge badge-{{ $utilisateur->state === 'active' ? 'actif' : 'inactif' }}">{{ $utilisateur->state === 'active' ? 'Actif' : 'Inactif' }}</span>
            </p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('utilisateurs.edit', $utilisateur) }}" class="btn btn-warning">Modifier</a>
            @if($utilisateur->id !== auth()->id())
                <form method="POST" action="{{ route('utilisateurs.destroy', $utilisateur) }}"
                    onsubmit="return confirm('Supprimer ?')">
                    @csrf @method('DELETE')<button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            @endif
        </div>
    </div>
    <div class="detail-grid">
        <div class="detail-section">
            <div class="detail-header">👤 Informations</div>
            <div class="detail-body">
                <div class="detail-row"><span class="detail-label">Prénom</span><span
                        class="detail-value">{{ $utilisateur->name }}</span></div>
                <div class="detail-row"><span class="detail-label">Nom</span><span
                        class="detail-value">{{ $utilisateur->surname }}</span></div>
                <div class="detail-row"><span class="detail-label">Email</span><span class="detail-value"><a
                            href="mailto:{{ $utilisateur->email }}">{{ $utilisateur->email }}</a></span></div>
                <div class="detail-row"><span class="detail-label">Entreprise</span><span
                        class="detail-value">{{ $utilisateur->company ?: '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">Adresse</span><span
                        class="detail-value">{{ $utilisateur->adresse ?: '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">Ville</span><span
                        class="detail-value">{{ $utilisateur->code_postal ? $utilisateur->code_postal . ' ' : '' }}{{ $utilisateur->ville ?: '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">Pays</span><span
                        class="detail-value">{{ $utilisateur->pays ?: '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">Rôle</span><span class="detail-value"><span
                            class="badge badge-{{ $utilisateur->role }}">{{ ucfirst($utilisateur->role) }}</span></span>
                </div>
                <div class="detail-row"><span class="detail-label">État</span><span class="detail-value"><span
                            class="badge badge-{{ $utilisateur->state === 'active' ? 'actif' : 'inactif' }}">{{ $utilisateur->state === 'active' ? 'Actif' : 'Inactif' }}</span></span>
                </div>
                <div class="detail-row"><span class="detail-label">Membre depuis</span><span
                        class="detail-value">{{ $utilisateur->created_at->format('d/m/Y') }}</span></div>
            </div>
        </div>
        <div class="detail-section">
            <div class="detail-header">📊 Activité</div>
            <div class="detail-body">
                @if($utilisateur->role === 'client')
                    <div class="detail-row"><span class="detail-label">Projets associés</span><span
                            class="detail-value">{{ $utilisateur->projetsClient->count() }}</span></div>
                    <div class="detail-row"><span class="detail-label">Contrats</span><span
                            class="detail-value">{{ $utilisateur->contratsClient->count() }}</span></div>
                @else
                    {{-- On pourrait ajouter les tickets ici pour les collab --}}
                    <div class="detail-row"><span class="detail-label">Projets (collaborateur)</span><span
                            class="detail-value">{{ $utilisateur->projets->count() }}</span></div>
                @endif
            </div>
        </div>
    </div>
@endsection