<!DOCTYPE html>
<html lang="fr" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inscription — Vector</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}" />
    <script src="{{ asset('js/theme.js') }}"></script>
</head>

<body class="oswald-font1 auth-page">
    <div class="auth-container">
        {{-- Logo --}}
        <div class="auth-logo">
            <a href="{{ route('home') }}">
                <img src="{{ asset('assets/logo.png') }}" alt="Vector Logo" />
                <img src="{{ asset('assets/name.png') }}" alt="Vector" class="logo-name" />
            </a>
        </div>

        <div class="auth-card">
            <div class="auth-card-header">
                <h1>Créer un compte</h1>
                <p>Rejoignez la plateforme Vector</p>
            </div>

            {{-- Erreurs --}}
            @if($errors->any())
                <div class="auth-alert auth-alert-error">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="auth-form">
                @csrf

                <div class="form-row-auth">
                    <div class="auth-form-group">
                        <label for="name" class="auth-label">
                            <img src="{{ asset('assets/utilisateur.png') }}" alt="" />
                            Prénom
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            class="auth-input {{ $errors->has('name') ? 'is-invalid' : '' }}" placeholder="Votre prénom"
                            required />
                    </div>

                    <div class="auth-form-group">
                        <label for="surname" class="auth-label">
                            <img src="{{ asset('assets/utilisateur.png') }}" alt="" />
                            Nom
                        </label>
                        <input type="text" id="surname" name="surname" value="{{ old('surname') }}"
                            class="auth-input {{ $errors->has('surname') ? 'is-invalid' : '' }}" placeholder="Votre nom"
                            required />
                    </div>
                </div>

                <div class="auth-form-group">
                    <label for="email" class="auth-label">
                        <img src="{{ asset('assets/enveloppe.png') }}" alt="" />
                        Adresse email
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="auth-input {{ $errors->has('email') ? 'is-invalid' : '' }}" placeholder="votre@email.com"
                        required />
                </div>

                <div class="auth-form-group">
                    <label for="company" class="auth-label">
                        <img src="{{ asset('assets/client.png') }}" alt="" />
                        Entreprise <span class="optional">(facultatif)</span>
                    </label>
                    <input type="text" id="company" name="company" value="{{ old('company') }}" class="auth-input"
                        placeholder="Nom de votre entreprise" />
                </div>

                <div class="auth-form-group">
                    <label for="password" class="auth-label">
                        <img src="{{ asset('assets/avertissement.png') }}" alt="" />
                        Mot de passe
                    </label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password"
                            class="auth-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            placeholder="Minimum 8 caractères" required />
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <img src="{{ asset('assets/oeil.png') }}" alt="Voir" />
                        </button>
                    </div>
                </div>

                <div class="auth-form-group">
                    <label for="password_confirmation" class="auth-label">
                        <img src="{{ asset('assets/avertissement.png') }}" alt="" />
                        Confirmer le mot de passe
                    </label>
                    <div class="password-wrapper">
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="auth-input" placeholder="Répétez votre mot de passe" required />
                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                            <img src="{{ asset('assets/oeil.png') }}" alt="Voir" />
                        </button>
                    </div>
                </div>

                <button type="submit" class="auth-btn">
                    Créer mon compte
                </button>
            </form>

            <div class="auth-footer">
                <p>Déjà un compte ?
                    <a href="{{ route('login') }}">Se connecter</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            field.type = field.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>

</html>