@extends('layouts.app')
@section('title', 'Utilisateurs')
@section('page-title', 'Gestion des Utilisateurs')
@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Utilisateurs</h2>
            <p>{{ $utilisateurs->total() }} utilisateur(s)</p>
        </div>
        <div class="page-header-actions">
            <button class="btn btn-secondary" onclick="refreshUsers(event)"><img src="{{ asset('assets/oeil.png') }}" alt="Refresh" style="width:16px; margin-right:5px; filter:invert(1)"> Actualiser (API)</button>
            <button class="btn btn-primary" onclick="openQuickAdd('User')">+ Quick Add (API)</button>
            <a href="{{ route('utilisateurs.create') }}" class="btn btn-primary">+ Nouvel Utilisateur</a>
        </div>
    </div>
    <form method="GET" action="{{ route('utilisateurs.index') }}">
        <div class="filters-bar">
            <div class="filter-group"><label>Rechercher</label><input type="text" name="search" class="filter-input"
                    placeholder="Nom, email..." value="{{ request('search') }}" /></div>
            <div class="filter-group">
                <label>Rôle</label>
                <select name="role" class="filter-input">
                    <option value="">Tous</option>
                    @foreach(['admin', 'collaborateur', 'client'] as $r)
                        <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>État</label>
                <select name="state" class="filter-input">
                    <option value="">Tous</option>
                    <option value="active" {{ request('state') === 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="inactive" {{ request('state') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div class="filter-group" style="justify-content:flex-end;"><label>&nbsp;</label><button type="submit"
                    class="btn btn-primary btn-sm">Filtrer</button></div>
        </div>
    </form>
    <div style="padding: 0 var(--spacing-xl);">
        <div class="card">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Entreprise</th>
                            <th>Rôle</th>
                            <th>État</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($utilisateurs as $user)
                            <tr>
                                <td style="color:var(--text-muted);font-size:var(--font-size-xs)">{{ $user->id }}</td>
                                <td><a href="{{ route('utilisateurs.show', $user) }}"
                                        style="font-weight:600;color:var(--text-primary)">{{ $user->name }}
                                        {{ $user->surname }}</a></td>
                                <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                                <td>{{ $user->company ?: '—' }}</td>
                                <td><span class="badge badge-{{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                                <td><span
                                        class="badge badge-{{ $user->state === 'active' ? 'actif' : 'inactif' }}">{{ $user->state === 'active' ? 'Actif' : 'Inactif' }}</span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('utilisateurs.show', $user) }}" class="btn btn-sm btn-secondary"><img
                                                src="{{ asset('assets/oeil.png') }}" alt="Voir" /></a>
                                        <a href="{{ route('utilisateurs.edit', $user) }}" class="btn btn-sm btn-warning"><img
                                                src="{{ asset('assets/editer.png') }}" alt="Modifier"
                                                style="filter:brightness(10)" /></a>
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('utilisateurs.destroy', $user) }}"
                                                onsubmit="return confirm('Supprimer cet utilisateur ?')" style="display:inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"><img
                                                        src="{{ asset('assets/supprimer.png') }}" alt="Supprimer"
                                                        style="filter:brightness(10)" /></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">👥</div>
                                        <h3>Aucun utilisateur trouvé</h3>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($utilisateurs->hasPages())
            <div class="pagination-wrapper">{{ $utilisateurs->appends(request()->query())->links() }}</div>@endif
        </div>
    </div>
    @include('dashboard.partials.modals-api')
@endsection

@push('scripts')
<script>
    async function refreshUsers(e) {
        const tableBody = document.querySelector('.data-table tbody');
        const refreshBtn = e ? e.target : null;
        if (refreshBtn) {
            refreshBtn.disabled = true;
            refreshBtn.innerHTML = 'Chargement...';
        }

        try {
            const response = await fetch('/api/utilisateurs', {
                headers: { 'Accept': 'application/json' }
            });
            const users = await response.json();
            
            tableBody.innerHTML = '';
            users.forEach(u => {
                const row = document.createElement('tr');
                row.className = 'refresh-flash';
                const stateBadge = u.state === 'active' ? 'actif' : 'inactif';
                const stateLabel = u.state === 'active' ? 'Actif' : 'Inactif';

                row.innerHTML = `
                    <td style="color:var(--text-muted);font-size:var(--font-size-xs)">${u.id}</td>
                    <td><a href="/utilisateurs/${u.id}" style="font-weight:600;color:var(--text-primary)">${u.name} ${u.surname}</a></td>
                    <td><a href="mailto:${u.email}">${u.email}</a></td>
                    <td>${u.company || '—'}</td>
                    <td><span class="badge badge-${u.role}">${u.role.charAt(0).toUpperCase() + u.role.slice(1)}</span></td>
                    <td><span class="badge badge-${stateBadge}">${stateLabel}</span></td>
                    <td>
                        <div class="table-actions">
                            <a href="/utilisateurs/${u.id}" class="btn btn-sm btn-secondary"><img src="/assets/oeil.png" alt="Voir" style="width:16px" /></a>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        } catch (error) {
            alert('Erreur lors du chargement des utilisateurs');
            console.error(error);
        } finally {
            if (refreshBtn) {
                refreshBtn.disabled = false;
                refreshBtn.innerHTML = 'Actualiser (API)';
            }
        }
    }
</script>
@endpush