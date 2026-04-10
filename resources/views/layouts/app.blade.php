<!DOCTYPE html>
<html lang="fr" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Vector') - Vector</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- CSS Global --}}
    <link rel="stylesheet" href="{{ asset('css/variables.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}" />

    {{-- CSS page spécifique --}}
    @stack('styles')
</head>

<body class="oswald-font1 role-{{ auth()->user()?->role ?? 'guest' }}">

    {{-- Header --}}
    <header class="dashboard-header">
        <div class="header-container">
            {{-- Logo --}}
            <div class="logo-section">
                <a href="{{ route('home') }}">
                    <img id="logoH" src="{{ asset('assets/logo.png') }}" alt="Logo Vector" />
                </a>
                <a href="{{ route('home') }}">
                    <img src="{{ asset('assets/name.png') }}" alt="Vector" />
                </a>
            </div>

            <div class="header-separator"></div>

            {{-- Titre de la page --}}
            <h1 class="dashboard-title">@yield('page-title', 'Dashboard')</h1>

            {{-- Navigation principale --}}
            @auth
                <nav class="header-nav">
                    <a href="{{ route('dashboard') }}"
                        class="nav-btn {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        Dashboard
                    </a>

                    {{-- Clients supprimés car intégrés aux utilisateurs --}}

                    <a href="{{ route('projets.index') }}"
                        class="nav-btn {{ request()->routeIs('projets.*') ? 'active' : '' }}">
                        Projets
                    </a>

                    <a href="{{ route('tickets.index') }}"
                        class="nav-btn {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
                        Tickets
                    </a>

                    @if(auth()->user()->isAdmin() || auth()->user()->isClient())
                        <a href="{{ route('contrats.index') }}"
                            class="nav-btn {{ request()->routeIs('contrats.*') ? 'active' : '' }}">
                            Contrats
                        </a>
                    @endif

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('utilisateurs.index') }}"
                            class="nav-btn {{ request()->routeIs('utilisateurs.*') ? 'active' : '' }}">
                            Utilisateurs
                        </a>
                    @endif
                </nav>

                {{-- Menu utilisateur --}}
                <div class="user-menu">
                    <div class="user-info">
                        <img src="{{ asset('assets/utilisateur.png') }}" alt="Utilisateur" class="user-avatar" />
                        <div class="user-details">
                            <span class="user-name">{{ auth()->user()->name }} {{ auth()->user()->surname }}</span>
                            <span
                                class="user-role role-badge-{{ auth()->user()->role }}">{{ ucfirst(auth()->user()->role) }}</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button type="submit" class="logout-btn" title="Se déconnecter">
                            <img src="{{ asset('assets/oeil.png') }}" alt="Déconnexion" />
                            Déconnexion
                        </button>
                    </form>
                </div>
            @endauth

            {{-- Toggle thème --}}
            <button class="theme-toggle" id="themeToggle" title="Changer le thème" aria-label="Changer le thème">
                <span class="theme-icon">🌙</span>
            </button>
        </div>
    </header>

    {{-- Messages flash --}}
    @if(session('success'))
        <div class="alert alert-success" role="alert">
            <span class="alert-icon">✅</span>
            {{ session('success') }}
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error" role="alert">
            <span class="alert-icon">❌</span>
            {{ session('error') }}
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error" role="alert">
            <span class="alert-icon">⚠️</span>
            <ul class="error-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
    @endif

    {{-- Contenu principal --}}
    <main class="main-content">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="app-footer">
        <p>&copy; {{ date('Y') }} Vector — Système de gestion de tickets & projets</p>
    </footer>

    {{-- JS --}}
    <script src="{{ asset('js/theme.js') }}"></script>
    @stack('scripts')
</body>

</html>