@extends('layouts.app')
@section('title', $contrat->nom)
@section('page-title', 'Fiche Contrat')
@section('content')
    <div class="breadcrumb"><a href="{{ route('contrats.index') }}">Contrats</a><span
            class="breadcrumb-sep">/</span><span>{{ $contrat->nom }}</span></div>
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ $contrat->nom }}</h2>
            <p><span
                    class="badge badge-{{ $contrat->statut }}">{{ ucfirst(str_replace('_', ' ', $contrat->statut)) }}</span>
            </p>
        </div>
        @if(auth()->user()->isAdmin())
            <div class="page-header-actions">
                <a href="{{ route('contrats.edit', $contrat) }}" class="btn btn-warning">Modifier</a>
                <form method="POST" action="{{ route('contrats.destroy', $contrat) }}"
                    onsubmit="return confirm('Supprimer ce contrat ?')">
                    @csrf @method('DELETE')<button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        @endif
    </div>
    <div class="detail-grid">
        <div class="detail-section">
            <div class="detail-header">📄 Informations</div>
            <div class="detail-body">
                <div class="detail-row"><span class="detail-label">Nom</span><span
                        class="detail-value">{{ $contrat->nom }}</span></div>

                <div class="detail-row"><span class="detail-label">Statut</span><span class="detail-value"><span
                            class="badge badge-{{ $contrat->statut }}">{{ ucfirst(str_replace('_', ' ', $contrat->statut)) }}</span></span>
                </div>
                <div class="detail-row"><span class="detail-label">Montant Total</span><span
                        class="detail-value">{{ $contrat->montant_total ? number_format($contrat->montant_total, 2) . ' €' : '0.00 €' }}</span>
                </div>
                <div class="detail-row"><span class="detail-label">Heures Totales</span><span
                        class="detail-value">{{ $contrat->heures_totales }} h</span>
                </div>
                <div class="detail-row"><span class="detail-label">Heures Consommées</span><span
                        class="detail-value">{{ $contrat->heures_consommees }} h</span>
                </div>
                <div class="detail-row"><span class="detail-label">Taux Horaire</span><span
                        class="detail-value">{{ $contrat->taux_horaire ? number_format($contrat->taux_horaire, 2) . ' €/h' : '0.00 €/h' }}</span>
                </div>
                <div class="detail-row"><span class="detail-label">Date début</span><span
                        class="detail-value">{{ $contrat->date_debut?->format('d/m/Y') ?? '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">Date fin</span><span
                        class="detail-value">{{ $contrat->date_fin?->format('d/m/Y') ?? '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">Créé le</span><span
                        class="detail-value">{{ $contrat->created_at->format('d/m/Y') }}</span></div>
            </div>
        </div>
        <div class="detail-section">
            <div class="detail-header">📝 Conditions</div>
            <div class="detail-body">
                <p style="color:var(--text-secondary);font-size:var(--font-size-sm);line-height:1.6">
                    {{ $contrat->conditions ?: 'Aucune condition.' }}</p>
                <div style="margin-top:var(--spacing-lg)">
                    <strong>Clients :</strong>
                    @foreach($contrat->clients as $c)<a href="{{ route('utilisateurs.show', $c) }}" class="badge badge-actif"
                    style="margin:2px">{{ $c->full_name }}</a>@endforeach
                </div>
                <div style="margin-top:var(--spacing-md)">
                    <strong>Projets :</strong>
                    @foreach($contrat->projets as $p)<a href="{{ route('projets.show', $p) }}" class="badge badge-normale"
                    style="margin:2px">{{ $p->nom }}</a>@endforeach
                </div>
            </div>
        </div>
    </div>
@endsection