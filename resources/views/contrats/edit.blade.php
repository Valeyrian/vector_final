@extends('layouts.app')
@section('title', 'Modifier ' . $contrat->titre)
@section('page-title', 'Modifier le Contrat')
@section('content')
    <div class="breadcrumb"><a href="{{ route('contrats.index') }}">Contrats</a><span class="breadcrumb-sep">/</span><a
            href="{{ route('contrats.show', $contrat) }}">{{ $contrat->nom }}</a><span
            class="breadcrumb-sep">/</span><span>Modifier</span></div>
    <div style="padding: var(--spacing-lg) var(--spacing-xl);">
        <div class="form-card">
            <div class="form-card-header"><img src="{{ asset('assets/editer.png') }}" alt="" style="width:28px" />
                <h2>Modifier : {{ $contrat->nom }}</h2>
            </div>
            <form method="POST" action="{{ route('contrats.update', $contrat) }}">
                @csrf @method('PUT')
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label required" for="nom">Nom du contrat</label>
                        <input type="text" id="nom" name="nom"
                            class="form-input {{ $errors->has('nom') ? 'is-invalid' : '' }}"
                            value="{{ old('nom', $contrat->nom) }}" required />
                        @error('nom')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="conditions">Conditions</label>
                        <textarea id="conditions" name="conditions"
                            class="form-textarea">{{ old('conditions', $contrat->conditions) }}</textarea>
                    </div>
                        <div class="form-group">
                            <label class="form-label required" for="statut">Statut</label>
                            <select id="statut" name="statut" class="form-select" required>
                                @foreach(['actif' => 'Actif', 'inactif' => 'Inactif', 'termine' => 'Terminé'] as $v => $l)
                                    <option value="{{ $v }}" {{ old('statut', $contrat->statut) === $v ? 'selected' : '' }}>
                                        {{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="date_debut">Date de début</label>
                            <input type="date" id="date_debut" name="date_debut" class="form-input"
                                value="{{ old('date_debut', $contrat->date_debut?->format('Y-m-d')) }}" />
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="date_fin">Date de fin</label>
                            <input type="date" id="date_fin" name="date_fin" class="form-input"
                                value="{{ old('date_fin', $contrat->date_fin?->format('Y-m-d')) }}" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="heures_totales">Heures Totales</label>
                            <input type="number" id="heures_totales" name="heures_totales" class="form-input" value="{{ old('heures_totales', $contrat->heures_totales) }}" min="0" step="0.5" />
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="heures_consommees">Heures Consommées</label>
                            <input type="number" id="heures_consommees" name="heures_consommees" class="form-input" value="{{ old('heures_consommees', $contrat->heures_consommees) }}" min="0" step="0.5" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="taux_horaire">Taux Horaire (€)</label>
                            <input type="number" id="taux_horaire" name="taux_horaire" class="form-input" value="{{ old('taux_horaire', $contrat->taux_horaire) }}" min="0" step="0.01" />
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="montant_total">Montant Total (€)</label>
                            <input type="number" id="montant_total" name="montant_total" class="form-input"
                                value="{{ old('montant_total', $contrat->montant_total) }}" min="0" step="0.01" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Clients associés</label>
                        <div class="checkbox-list">
                            @foreach($clients as $client)
                                <div class="checkbox-item">
                                    <input type="checkbox" id="cl_{{ $client->id }}" name="clients[]" value="{{ $client->id }}"
                                        {{ $contrat->clients->contains($client->id) ? 'checked' : '' }} />
                                    <label for="cl_{{ $client->id }}">{{ $client->full_name }} @if($client->company) ({{ $client->company }}) @endif</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Projets associés</label>
                        <div class="checkbox-list">
                            @foreach($projets as $projet)
                                <div class="checkbox-item">
                                    <input type="checkbox" id="pr_{{ $projet->id }}" name="projets[]" value="{{ $projet->id }}"
                                        {{ $contrat->projets->contains($projet->id) ? 'checked' : '' }} />
                                    <label for="pr_{{ $projet->id }}">{{ $projet->nom }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-card-actions">
                    <a href="{{ route('contrats.show', $contrat) }}" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
@endsection