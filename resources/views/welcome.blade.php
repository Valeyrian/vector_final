<!DOCTYPE html>
<html lang="fr" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Vector — Système de ticketing</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}" />
    <script src="{{ asset('js/theme.js') }}"></script>
    <style>
        body {
            font-family: var(--font-family);
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin: 0;
        }

        .welcome-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 48px;
            max-width: 600px;
            width: 90%;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
        }

        .welcome-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        .welcome-logo img {
            height: 60px;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 8px;
        }

        p {
            color: var(--text-secondary);
            margin: 0 0 32px;
            font-size: 1.1rem;
        }

        .btn-group {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: 6px;
            font-family: var(--font-family);
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 150ms ease;
            border: none;
        }

        .btn-primary {
            background: #3d7cf5;
            color: #fff;
        }

        .btn-primary:hover {
            background: #2563e8;
            color: #fff;
        }

        .btn-secondary {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--bg-primary);
        }

        .features {
            margin-top: 32px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            text-align: left;
        }

        .feature {
            padding: 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-primary);
        }

        .feature-icon {
            font-size: 1.5rem;
            margin-bottom: 8px;
        }

        .feature h3 {
            font-size: 0.875rem;
            font-weight: 600;
            margin: 0 0 4px;
            color: var(--text-primary);
        }

        .feature p {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="welcome-card">
        <div class="welcome-logo">
            <img src="{{ asset('assets/logo.png') }}" alt="Vector" />
            <img src="{{ asset('assets/name.png') }}" alt="Vector" style="height:36px" />
        </div>
        <h1>Bienvenue sur Vector</h1>
        <p>Système de gestion de tickets, projets et clients</p>
        <div class="btn-group">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Accéder au Dashboard →</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Se connecter</a>
                <a href="{{ route('register') }}" class="btn btn-secondary">Créer un compte</a>
            @endauth
        </div>
        <div class="features">
            <div class="feature">
                <div class="feature-icon">🎫</div>
                <h3>Tickets</h3>
                <p>Suivi complet des incidents et demandes</p>
            </div>
            <div class="feature">
                <div class="feature-icon">📁</div>
                <h3>Projets</h3>
                <p>Gestion de projets multi-clients</p>
            </div>
            <div class="feature">
                <div class="feature-icon">📄</div>
                <h3>Contrats</h3>
                <p>Suivi contractuel et financier</p>
            </div>
        </div>
    </div>
    <button id="themeToggle"
        style="position:fixed;bottom:16px;right:16px;background:var(--bg-card);border:1px solid var(--border-color);border-radius:8px;width:40px;height:40px;cursor:pointer;font-size:1.1rem;display:flex;align-items:center;justify-content:center;">
        <span class="theme-icon">🌙</span>
    </button>
</body>

</html>