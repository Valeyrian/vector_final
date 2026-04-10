<!DOCTYPE html>
<html lang="fr" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Connexion — Vector</title>
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
                <h1>Connexion</h1>
                <p>Accédez à votre espace Vector</p>
            </div>

            {{-- Erreurs --}}
            @if($errors->any())
                <div class="auth-alert auth-alert-error">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if(session('success'))
                <div class="auth-alert auth-alert-success">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="auth-form">
                @csrf

                <div class="auth-form-group">
                    <label for="email" class="auth-label">
                        <img src="{{ asset('assets/enveloppe.png') }}" alt="" />
                        Adresse email
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="auth-input {{ $errors->has('email') ? 'is-invalid' : '' }}" placeholder="votre@email.com"
                        required autofocus />
                </div>

                <div class="auth-form-group">
                    <label for="password" class="auth-label">
                        <img src="{{ asset('assets/avertissement.png') }}" alt="" />
                        Mot de passe
                    </label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" class="auth-input" placeholder="••••••••"
                            required />
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <img src="{{ asset('assets/oeil.png') }}" alt="Voir" />
                        </button>
                    </div>
                </div>

                <div class="auth-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" />
                        Se souvenir de moi
                    </label>
                </div>

                <button type="submit" class="auth-btn">
                    Se connecter
                </button>
            </form>

            <div class="auth-footer">
                <p>Pas encore de compte ?
                    <a href="{{ route('register') }}">Créer un compte</a>
                </p>
            </div>
        </div>

        <div class="auth-theme-toggle">
            <button id="themeToggle" class="theme-toggle-auth">
                <span class="theme-icon">🌙</span>
            </button>
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