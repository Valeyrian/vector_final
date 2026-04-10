@extends('layouts.app')
@section('title', 'Nouveau Ticket')
@section('page-title', 'Nouveau Ticket')
@section('content')
    <div class="breadcrumb">
        <a href="{{ route('tickets.index') }}">Tickets</a>
        <span class="breadcrumb-sep">/</span><span>Nouveau</span>
    </div>
    <div style="padding: var(--spacing-lg) var(--spacing-xl);">
        <div class="form-card">
            <div class="form-card-header">
                <img src="{{ asset('assets/ticket.png') }}" alt="" style="width:28px" />
                <h2>Créer un nouveau ticket</h2>
            </div>
            <form method="POST" action="{{ route('tickets.store') }}">
                @csrf
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label required" for="titre">Titre</label>
                        <input type="text" id="titre" name="titre"
                            class="form-input {{ $errors->has('titre') ? 'is-invalid' : '' }}" value="{{ old('titre') }}"
                            required placeholder="Décrivez brièvement le problème..." />
                        @error('titre')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="description">Description détaillée</label>
                        <textarea id="description" name="description" class="form-textarea" style="min-height:120px"
                            placeholder="Décrivez le problème en détail, les étapes pour reproduire, etc.">{{ old('description') }}</textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required" for="type">Type</label>
                            <select id="type" name="type" class="form-select" required>
                                @foreach(['inclus' => 'Inclus', 'facturable' => 'Facturable'] as $v => $l)
                                    <option value="{{ $v }}" {{ old('type', 'inclus') === $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label required" for="priorite">Priorité</label>
                            <select id="priorite" name="priorite" class="form-select" required>
                                @foreach(['basse' => 'Basse', 'moyenne' => 'Moyenne', 'haute' => 'Haute'] as $p => $l)
                                    <option value="{{ $p }}" {{ old('priorite', 'moyenne') === $p ? 'selected' : '' }}>
                                        {{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required" for="statut">Statut initial</label>
                            <select id="statut" name="statut" class="form-select" required>
                                @foreach(['nouveau'=>'Nouveau', 'en_cours'=>'En cours', 'en_attente_client'=>'En attente client', 'termine'=>'Terminé', 'a_valider'=>'À valider', 'valide'=>'Validé', 'refuse'=>'Refusé'] as $v => $l)
                                    <option value="{{ $v }}" {{ old('statut', 'nouveau') === $v ? 'selected' : '' }}>{{ $l }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="projet_id">Projet associé</label>
                            <select id="projet_id" name="projet_id" class="form-select">
                                <option value="">— Aucun projet —</option>
                                @foreach($projets as $projet)
                                    <option value="{{ $projet->id }}" {{ old('projet_id') == $projet->id ? 'selected' : '' }}>
                                        {{ $projet->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="temps_estime">Temps estimé (h)</label>
                            <input type="number" step="0.5" min="0" max="9999.99" id="temps_estime" name="temps_estime"
                                class="form-input {{ $errors->has('temps_estime') ? 'is-invalid' : '' }}" value="{{ old('temps_estime', 0) }}" />
                        </div>
                    </div>
                    @if(auth()->user()->isAdmin())
                        <div class="form-group">
                            <label class="form-label" for="collaborateurs">Assigner à</label>
                            <select id="collaborateurs" name="collaborateurs[]" class="form-select" multiple style="height: auto; min-height: 80px;">
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" {{ in_array($agent->id, old('collaborateurs', [])) ? 'selected' : '' }}>
                                        {{ $agent->name }} {{ $agent->surname }}</option>
                                @endforeach
                            </select>
                            <small style="color:var(--text-muted)">Maintenez Ctrl/Cmd pour sélectionner plusieurs collaborateurs.</small>
                        </div>
                    @endif
                </div>
                <div class="form-card-actions">
                    <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Créer le ticket</button>
                </div>
            </form>
        </div>
    </div>
@endsection