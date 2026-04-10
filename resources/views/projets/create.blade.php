@extends('layouts.app')
@section('title', 'Nouveau Projet')
@section('page-title', 'Nouveau Projet')
@section('content')
    <div class="breadcrumb">
        <a href="{{ route('projets.index') }}">Projets</a>
        <span class="breadcrumb-sep">/</span><span>Nouveau</span>
    </div>
    <div style="padding: var(--spacing-lg) var(--spacing-xl);">
        <div class="form-card">
            <div class="form-card-header">
                <img src="{{ asset('assets/project.png') }}" alt="" style="width:28px" />
                <h2>Créer un nouveau projet</h2>
            </div>
            <form method="POST" action="{{ route('projets.store') }}">
                @csrf
                <div class="form-card-body">
                    <div class="form-group">
                        <label class="form-label required" for="nom">Nom du projet</label>
                        <input type="text" id="nom" name="nom"
                            class="form-input {{ $errors->has('nom') ? 'is-invalid' : '' }}" value="{{ old('nom') }}"
                            required />
                        @error('nom')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="description">Description</label>
                        <textarea id="description" name="description"
                            class="form-textarea">{{ old('description') }}</textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required" for="statut">Statut</label>
                            <select id="statut" name="statut" class="form-select" required>
                                @foreach(['actif' => 'Actif', 'archive' => 'Archivé'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('statut', 'actif') === $val ? 'selected' : '' }}>
                                        {{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="date_debut">Date de début</label>
                            <input type="date" id="date_debut" name="date_debut" class="form-input"
                                value="{{ old('date_debut') }}" />
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="date_fin_prevue">Date de fin prévue</label>
                            <input type="date" id="date_fin_prevue" name="date_fin_prevue" class="form-input"
                                value="{{ old('date_fin_prevue') }}" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Clients associés</label>
                        <div class="checkbox-list">
                            @foreach($clients as $client)
                                <div class="checkbox-item">
                                    <input type="checkbox" id="cl_{{ $client->id }}" name="clients[]" value="{{ $client->id }}"
                                        {{ in_array($client->id, old('clients', [])) ? 'checked' : '' }} />
                                    <label for="cl_{{ $client->id }}">{{ $client->full_name }} @if($client->company) ({{ $client->company }}) @endif</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Collaborateurs</label>
                        <div class="checkbox-list">
                            @foreach($collaborateurs as $collab)
                                <div class="checkbox-item">
                                    <input type="checkbox" id="co_{{ $collab->id }}" name="collaborateurs[]"
                                        value="{{ $collab->id }}" {{ in_array($collab->id, old('collaborateurs', [])) ? 'checked' : '' }} />
                                    <label for="co_{{ $collab->id }}">{{ $collab->name }} {{ $collab->surname }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Contrats associés</label>
                        <div class="checkbox-list">
                            @foreach($contrats as $contrat)
                                <div class="checkbox-item">
                                    <input type="checkbox" id="ct_{{ $contrat->id }}" name="contrats[]" value="{{ $contrat->id }}"
                                        {{ in_array($contrat->id, old('contrats', [])) ? 'checked' : '' }} />
                                    <label for="ct_{{ $contrat->id }}">{{ $contrat->nom }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="form-card-actions">
                    <a href="{{ route('projets.index') }}" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Créer le projet</button>
                </div>
            </form>
        </div>
    </div>
@endsection