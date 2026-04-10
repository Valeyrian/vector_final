@extends('layouts.app')
@section('title', 'Modifier ' . $utilisateur->name)
@section('page-title', 'Modifier Utilisateur')
@section('content')
    <div class="breadcrumb"><a href="{{ route('utilisateurs.index') }}">Utilisateurs</a><span
            class="breadcrumb-sep">/</span><a
            href="{{ route('utilisateurs.show', $utilisateur) }}">{{ $utilisateur->name }}</a><span
            class="breadcrumb-sep">/</span><span>Modifier</span></div>
    <div style="padding: var(--spacing-lg) var(--spacing-xl);">
        <div class="form-card">
            <div class="form-card-header"><img src="{{ asset('assets/editer.png') }}" alt="" style="width:28px" />
                <h2>Modifier : {{ $utilisateur->name }} {{ $utilisateur->surname }}</h2>
            </div>
            <form method="POST" action="{{ route('utilisateurs.update', $utilisateur) }}">
                @csrf @method('PUT')
                <div class="form-card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required" for="name">Prénom</label>
                            <input type="text" id="name" name="name" class="form-input"
                                value="{{ old('name', $utilisateur->name) }}" required />
                        </div>
                        <div class="form-group">
                            <label class="form-label required" for="surname">Nom</label>
                            <input type="text" id="surname" name="surname" class="form-input"
                                value="{{ old('surname', $utilisateur->surname) }}" required />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="email">Email</label>
                        <input type="email" id="email" name="email"
                            class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            value="{{ old('email', $utilisateur->email) }}" required />
                        @error('email')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="company">Entreprise / Nom Commercial</label>
                        <input type="text" id="company" name="company" class="form-input"
                            value="{{ old('company', $utilisateur->company) }}" />
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="adresse">Adresse</label>
                        <input type="text" id="adresse" name="adresse" class="form-input"
                            value="{{ old('adresse', $utilisateur->adresse) }}" />
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="flex: 1;">
                            <label class="form-label" for="code_postal">Code Postal</label>
                            <input type="text" id="code_postal" name="code_postal" class="form-input"
                                value="{{ old('code_postal', $utilisateur->code_postal) }}" />
                        </div>
                        <div class="form-group" style="flex: 2;">
                            <label class="form-label" for="ville">Ville</label>
                            <input type="text" id="ville" name="ville" class="form-input"
                                value="{{ old('ville', $utilisateur->ville) }}" />
                        </div>
                        <div class="form-group" style="flex: 2;">
                            <label class="form-label" for="pays">Pays</label>
                            <input type="text" id="pays" name="pays" class="form-input"
                                value="{{ old('pays', $utilisateur->pays ?? 'France') }}" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required" for="role">Rôle</label>
                            <select id="role" name="role" class="form-select" required>
                                @foreach(['admin', 'collaborateur', 'client'] as $r)
                                    <option value="{{ $r }}" {{ old('role', $utilisateur->role) === $r ? 'selected' : '' }}>
                                        {{ ucfirst($r) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label required" for="state">État</label>
                            <select id="state" name="state" class="form-select" required>
                                <option value="active" {{ old('state', $utilisateur->state) === 'active' ? 'selected' : '' }}>
                                    Actif</option>
                                <option value="inactive" {{ old('state', $utilisateur->state) === 'inactive' ? 'selected' : '' }}>Inactif</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="password">Nouveau mot de passe <span
                                    style="color:var(--text-muted);font-size:var(--font-size-xs)">(laisser vide pour ne pas
                                    changer)</span></label>
                            <input type="password" id="password" name="password"
                                class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}" />
                            @error('password')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="password_confirmation">Confirmer</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-input" />
                        </div>
                    </div>
                </div>
                <div class="form-card-actions">
                    <a href="{{ route('utilisateurs.show', $utilisateur) }}" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
@endsection