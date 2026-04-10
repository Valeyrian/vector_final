@extends('layouts.app')
@section('title', '#' . $ticket->id . ' ' . $ticket->titre)
@section('page-title', 'Ticket #' . $ticket->id)
@section('content')
    <div class="breadcrumb">
        <a href="{{ route('tickets.index') }}">Tickets</a>
        <span class="breadcrumb-sep">/</span><span>#{{ $ticket->id }}</span>
    </div>
    <div class="page-header">
        <div class="page-header-left">
            <h2>{{ $ticket->titre }}</h2>
            <p>
                <span class="badge badge-{{ str_replace('_','-',$ticket->statut) }}">{{ ucfirst(str_replace('_',' ',$ticket->statut)) }}</span>
                <span class="badge badge-{{ $ticket->priorite }}">{{ ucfirst($ticket->priorite) }}</span>
                <span class="badge badge-normale">{{ ucfirst($ticket->type) }}</span>
            </p>
        </div>
        <div class="page-header-actions">
            @if($ticket->statut !== 'bloque')
                <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-warning">Modifier</a>
            @endif
            @if(auth()->user()->isAdmin())
                <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('Supprimer ce ticket ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            @endif
        </div>
    </div>

    @if(auth()->user()->isClient() && $ticket->type === 'facturable' && is_null($ticket->approuve_client))
        <div class="alert alert-info" style="margin-bottom:var(--spacing-lg);display:flex;justify-content:space-between;align-items:center;background:var(--color-bg-pending);border:1px solid var(--color-pending);border-radius:var(--border-radius-md);padding:var(--spacing-md) var(--spacing-lg)">
            <div>
                <strong style="color:var(--color-pending)">💳 Approbation requise</strong>
                <p style="margin:4px 0 0;font-size:var(--font-size-sm)">Ce ticket est facturable et nécessite votre validation pour commencer.</p>
            </div>
            <div style="display:flex;gap:10px">
                <form method="POST" action="{{ route('tickets.approuver', $ticket) }}">
                    @csrf
                    <button type="submit" class="btn btn-success">Approuver</button>
                </form>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('refus-form').style.display='block'">Refuser</button>
            </div>
        </div>
        <div id="refus-form" style="display:none;margin-bottom:var(--spacing-lg);padding:var(--spacing-lg);background:var(--bg-card);border:1px solid var(--color-urgente);border-radius:var(--border-radius-md)">
            <form method="POST" action="{{ route('tickets.refuser', $ticket) }}">
                @csrf
                <div class="form-group">
                    <label class="form-label required">Motif du refus</label>
                    <textarea name="motif_refus" class="form-textarea" placeholder="Indiquez pourquoi vous refusez ce ticket..." required></textarea>
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('refus-form').style.display='none'">Annuler</button>
                    <button type="submit" class="btn btn-danger">Confirmer le refus et bloquer le ticket</button>
                </div>
            </form>
        </div>
    @endif

    @if($ticket->statut === 'bloque')
        <div class="alert alert-danger" style="margin-bottom:var(--spacing-lg);background:var(--color-urgente-alpha);border:1px solid var(--color-urgente);border-radius:var(--border-radius-md);padding:var(--spacing-md) var(--spacing-lg)">
            <strong style="color:var(--color-urgente)">🚫 Ticket Bloqué</strong>
            <p style="margin:4px 0 0;font-size:var(--font-size-sm)">Le client a refusé ce ticket. Motif : <em>{{ $ticket->motif_refus }}</em></p>
        </div>
    @endif

    <div class="detail-grid">
        {{-- Infos --}}
        <div class="detail-section">
            <div class="detail-header">📋 Informations</div>
            <div class="detail-body">
                <div class="detail-row"><span class="detail-label">Statut</span><span class="detail-value"><span class="badge badge-{{ str_replace('_','-',$ticket->statut) }}">{{ ucfirst(str_replace('_',' ',$ticket->statut)) }}</span></span></div>
                <div class="detail-row"><span class="detail-label">Priorité</span><span class="detail-value"><span class="badge badge-{{ $ticket->priorite }}">{{ ucfirst($ticket->priorite) }}</span></span></div>
                <div class="detail-row"><span class="detail-label">Type</span><span class="detail-value">{{ ucfirst($ticket->type) }}</span></div>
                <div class="detail-row"><span class="detail-label">Projet</span><span class="detail-value">{{ $ticket->projet ? $ticket->projet->nom : '—' }}</span></div>
                <div class="detail-row"><span class="detail-label">Assigné à</span><span class="detail-value">{{ $ticket->collaborateurs->pluck('name')->join(', ') ?: 'Non assigné' }}</span></div>
                <div class="detail-row"><span class="detail-label">Créé le</span><span class="detail-value">{{ $ticket->created_at->format('d/m/Y H:i') }}</span></div>
                <div class="detail-row"><span class="detail-label">Temps estimé</span><span class="detail-value">{{ floatval($ticket->temps_estime) }}h</span></div>
                <div class="detail-row"><span class="detail-label">Temps total</span><span class="detail-value">{{ intdiv($ticket->total_temps, 60) }}h{{ str_pad($ticket->total_temps % 60, 2, '0') }}</span></div>
            </div>
        </div>

        {{-- Description --}}
        <div class="detail-section">
            <div class="detail-header">📝 Description</div>
            <div class="detail-body">
                <p style="color:var(--text-secondary);font-size:var(--font-size-sm);line-height:1.7;white-space:pre-wrap">{{ $ticket->description ?: 'Aucune description fournie.' }}</p>
            </div>
        </div>

        {{-- Changer statut rapide --}}
        @if((auth()->user()->isAdmin() || auth()->user()->isCollaborateur()) && $ticket->statut !== 'bloque')
        <div class="detail-section">
            <div class="detail-header">⚙️ Changer le statut</div>
            <div class="detail-body">
                <form method="POST" action="{{ route('tickets.update', $ticket) }}" style="display:flex;gap:8px;flex-wrap:wrap">
                    @csrf @method('PUT')
                    <input type="hidden" name="titre" value="{{ $ticket->titre }}" />
                    <input type="hidden" name="description" value="{{ $ticket->description }}" />
                    <input type="hidden" name="priorite" value="{{ $ticket->priorite }}" />
                    <input type="hidden" name="type" value="{{ $ticket->type }}" />
                    <input type="hidden" name="temps_estime" value="{{ $ticket->temps_estime }}" />
                    <input type="hidden" name="projet_id" value="{{ $ticket->projet_id }}" />
                    @foreach($ticket->collaborateurs as $c)
                        <input type="hidden" name="collaborateurs[]" value="{{ $c->id }}" />
                    @endforeach
                    @foreach(['nouveau'=>'Nouveau', 'en_cours'=>'En cours', 'en_attente_client'=>'En attente client', 'termine'=>'Terminé', 'a_valider'=>'À valider', 'valide'=>'Validé', 'refuse'=>'Refusé'] as $v => $l)
                        <button type="submit" name="statut" value="{{ $v }}" class="btn btn-sm {{ $ticket->statut === $v ? 'btn-primary' : 'btn-secondary' }}">{{ $l }}</button>
                    @endforeach
                </form>
            </div>
        </div>

        {{-- Enregistrer temps --}}
        <div class="detail-section">
            <div class="detail-header">⏱️ Enregistrer du temps</div>
            <div class="detail-body">
                <form method="POST" action="{{ route('tickets.temps.store', $ticket) }}" style="display:flex;gap:var(--spacing-md);flex-wrap:wrap;align-items:flex-end">
                    @csrf
                    <div class="form-group" style="margin:0;flex:1;min-width:100px">
                        <label class="form-label required">Durée (min)</label>
                        <input type="number" name="duree" class="form-input" min="1" placeholder="Ex: 30" required />
                    </div>
                    <div class="form-group" style="margin:0;flex:1;min-width:120px">
                        <label class="form-label required">Date</label>
                        <input type="date" name="date" class="form-input" value="{{ date('Y-m-d') }}" required />
                    </div>
                    <div class="form-group" style="margin:0;flex:2;min-width:200px">
                        <label class="form-label">Note</label>
                        <input type="text" name="description" class="form-input" placeholder="Travail effectué..." />
                    </div>
                    <button type="submit" class="btn btn-success" style="margin-bottom:0">Enregistrer</button>
                </form>
            </div>
        </div>
        @endif

        {{-- Historique temps --}}
        @if($ticket->tempsEnregistres->count() > 0)
        <div class="detail-section">
            <div class="detail-header">📊 Temps enregistrés</div>
            <div class="detail-body" style="padding:0">
                @foreach($ticket->tempsEnregistres as $temps)
                    <div class="section-item">
                        <div class="section-item-info">
                            <div class="section-item-title">{{ $temps->utilisateur?->name }} — {{ $temps->duree_formattee }}</div>
                            <div class="section-item-sub">{{ $temps->date_travail->format('d/m/Y') }} — {{ $temps->commentaire ?? '' }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Commentaires --}}
        <div class="detail-section full-width">
            <div class="detail-header">💬 Commentaires ({{ $ticket->commentaires->count() }})</div>
            <div class="detail-body" style="padding:0">
                @foreach($ticket->commentaires as $commentaire)
                    <div style="padding:var(--spacing-md) var(--spacing-lg);border-bottom:1px solid var(--border-color)">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                            <strong style="color:var(--text-primary);font-size:var(--font-size-sm)">
                                {{ $commentaire->auteur?->name }} {{ $commentaire->auteur?->surname }}
                            </strong>
                            <small style="color:var(--text-muted)">{{ $commentaire->created_at->diffForHumans() }}</small>
                        </div>
                        <p style="color:var(--text-secondary);font-size:var(--font-size-sm);line-height:1.6;white-space:pre-wrap">{{ $commentaire->contenu }}</p>
                    </div>
                @endforeach

                {{-- Formulaire commentaire --}}
                <div style="padding:var(--spacing-lg)">
                    <form method="POST" action="{{ route('tickets.commentaires.store', $ticket) }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label required">Ajouter un commentaire</label>
                            <textarea name="contenu" class="form-textarea {{ $errors->has('contenu') ? 'is-invalid' : '' }}" placeholder="Votre commentaire..." required>{{ old('contenu') }}</textarea>
                            @error('contenu')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Publier le commentaire</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
