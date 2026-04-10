@extends('layouts.app')
@section('title', 'Nouvel Utilisateur')
@section('page-title', 'Nouvel Utilisateur')
@section('content')
    <div class="breadcrumb"><a href="{{ route('utilisateurs.index') }}">Utilisateurs</a><span
            class="breadcrumb-sep">/</span><span>Nouveau</span></div>
    <div style="padding: var(--spacing-lg) var(--spacing-xl);">
        <div class="form-card">
            <div class="form-card-header"><img src="{{ asset('assets/utilisateur.png') }}" alt="" style="width:28px" />
                <h2>Créer un utilisateur</h2>
            </div>
            <form method="POST" action="{{ route('utilisateurs.store') }}">
                @csrf
                <div class="form-card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required" for="name">Prénom</label>
                            <input type="text" id="name" name="name"
                                class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}"
                                required />
                            @error('name')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label required" for="surname">Nom</label>
                            <input type="text" id="surname" name="surname"
                                class="form-input {{ $errors->has('surname') ? 'is-invalid' : '' }}"
                                value="{{ old('surname') }}" required />
                            @error('surname')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="email">Email</label>
                        <input type="email" id="email" name="email"
                            class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}" value="{{ old('email') }}"
                            required />
                        @error('email')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="company">Entreprise / Nom Commercial</label>
                        <input type="text" id="company" name="company" class="form-input" value="{{ old('company') }}" />
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="adresse">Adresse</label>
                        <input type="text" id="adresse" name="adresse" class="form-input" value="{{ old('adresse') }}" />
                    </div>
                    <div class="form-row">
                        <div class="form-group" style="flex: 1;">
                            <label class="form-label" for="code_postal">Code Postal</label>
                            <input type="text" id="code_postal" name="code_postal" class="form-input" value="{{ old('code_postal') }}" />
                        </div>
                        <div class="form-group" style="flex: 2;">
                            <label class="form-label" for="ville">Ville</label>
                            <input type="text" id="ville" name="ville" class="form-input" value="{{ old('ville') }}" />
                        </div>
                        <div class="form-group" style="flex: 2;">
                            <label class="form-label" for="pays">Pays</label>
                            <input type="text" id="pays" name="pays" class="form-input" value="{{ old('pays', 'France') }}" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required" for="role">Rôle</label>
                            <select id="role" name="role" class="form-select" required>
                                @foreach(['admin', 'collaborateur', 'client'] as $r)
                                    <option value="{{ $r }}" {{ old('role', request('role', 'client')) === $r ? 'selected' : '' }}>
                                        {{ ucfirst($r) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label required" for="state">État</label>
                            <select id="state" name="state" class="form-select" required>
                                <option value="active" {{ old('state', 'active') === 'active' ? 'selected' : '' }}>Actif
                                </option>
                                <option value="inactive" {{ old('state') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required" for="password">Mot de passe</label>
                            <input type="password" id="password" name="password"
                                class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}" required />
                            @error('password')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label required" for="password_confirmation">Confirmer</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-input" required />
                        </div>
                    </div>
                </div>
                <div class="form-card-actions">
                    <a href="{{ route('utilisateurs.index') }}" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">Créer l'utilisateur</button>
                </div>
            </form>
        </div>
    </div>
@endsection