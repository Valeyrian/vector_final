@extends('layouts.app')
@section('title', 'Projets')
@section('page-title', 'Gestion des Projets')
@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Projets</h2>
            <p>{{ $projets->total() }} projet(s)</p>
        </div>
        <div class="page-header-actions">
            <button class="btn btn-secondary" id="refreshProjetsBtn"><img src="{{ asset('assets/oeil.png') }}" alt="Refresh"
                    style="width:16px; margin-right:5px; filter:invert(1)"> Actualiser (API)</button>
            @if(auth()->user()->isAdmin() || auth()->user()->isCollaborateur())
                <button type="button" class="btn btn-primary" onclick="openQuickAdd('Projet')">+ Quick Add (API)</button>
                <button type="button" class="btn btn-primary" id="openProjectModal">+ Nouveau Projet</button>
            @endif
        </div>
    </div>

    <form method="GET" action="{{ route('projets.index') }}">
        <div class="filters-bar">
            <div class="filter-group">
                <label>Rechercher</label>
                <input type="text" name="search" class="filter-input" placeholder="Nom du projet..."
                    value="{{ request('search') }}" />
            </div>
            <div class="filter-group">
                <label>Statut</label>
                <select name="statut" class="filter-input">
                    <option value="">Tous</option>
                    @foreach(['actif' => 'Actif', 'archive' => 'Archivé'] as $val => $label)
                        <option value="{{ $val }}" {{ request('statut') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group" style="justify-content:flex-end;">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
            </div>
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
                            <th>Clients</th>
                            <th>Statut</th>
                            <th>Date début</th>
                            <th>Date fin prévue</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projets as $projet)
                            <tr>
                                <td style="color:var(--text-muted);font-size:var(--font-size-xs)">{{ $projet->id }}</td>
                                <td><a href="{{ route('projets.show', $projet) }}"
                                        style="font-weight:600;color:var(--text-primary)">{{ $projet->nom }}</a></td>
                                <td>{{ $projet->clients->map->full_name->join(', ') ?: '—' }}</td>
                                <td><span
                                        class="badge badge-{{ str_replace('_', '-', $projet->statut) }}">{{ ucfirst(str_replace('_', ' ', $projet->statut)) }}</span>
                                </td>
                                <td>{{ $projet->date_debut?->format('d/m/Y') ?? '—' }}</td>
                                <td>{{ $projet->date_fin_prevue?->format('d/m/Y') ?? '—' }}</td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('projets.show', $projet) }}" class="btn btn-sm btn-secondary"><img
                                                src="{{ asset('assets/oeil.png') }}" alt="Voir" /></a>
                                        @if(auth()->user()->isAdmin() || auth()->user()->isCollaborateur())
                                            <a href="{{ route('projets.edit', $projet) }}" class="btn btn-sm btn-warning"><img
                                                    src="{{ asset('assets/editer.png') }}" alt="Modifier"
                                                    style="filter:brightness(10)" /></a>
                                        @endif
                                        @if(auth()->user()->isAdmin())
                                            <form method="POST" action="{{ route('projets.destroy', $projet) }}"
                                                onsubmit="return confirm('Supprimer ce projet ?')" style="display:inline">
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
                                <td colspan="8">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">📁</div>
                                        <h3>Aucun projet trouvé</h3>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($projets->hasPages())
                <div class="pagination-wrapper">{{ $projets->appends(request()->query())->links() }}</div>
            @endif
        </div>
    </div>

    {{-- Modal Nouveau Projet --}}
    @if(auth()->user()->isAdmin() || auth()->user()->isCollaborateur())
        <div class="modal-overlay" id="projectModal">
            <div class="modal-container">
                <div class="modal-header">
                    <div class="modal-title">
                        <img src="{{ asset('assets/project.png') }}" alt="" style="width:24px" />
                        Nouveau Projet
                    </div>
                    <button class="modal-close" id="closeProjectModal">&times;</button>
                </div>
                <form id="projectForm" method="POST" action="{{ route('projets.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label required" for="nom">Nom du projet</label>
                            <input type="text" id="nom" name="nom" class="form-input" required />
                            <span class="form-error" id="error_nom"></span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="description">Description</label>
                            <textarea id="description" name="description" class="form-textarea"></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required" for="statut">Statut</label>
                                <select id="statut" name="statut" class="form-select" required>
                                    <option value="actif">Actif</option>
                                    <option value="archive">Archivé</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="date_debut">Date de début</label>
                                <input type="date" id="date_debut" name="date_debut" class="form-input" />
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="date_fin_prevue">Date de fin prévue</label>
                                <input type="date" id="date_fin_prevue" name="date_fin_prevue" class="form-input" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Clients associés</label>
                            <div class="checkbox-list">
                                @foreach($clients as $client)
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="modal_cl_{{ $client->id }}" name="clients[]"
                                            value="{{ $client->id }}" />
                                        <label for="modal_cl_{{ $client->id }}">{{ $client->full_name }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Collaborateurs</label>
                            <div class="checkbox-list">
                                @foreach($collaborateurs as $collab)
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="modal_co_{{ $collab->id }}" name="collaborateurs[]"
                                            value="{{ $collab->id }}" />
                                        <label for="modal_co_{{ $collab->id }}">{{ $collab->name }} {{ $collab->surname }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="form-group">
                                <label class="form-label">Contrats associés</label>
                                <div class="checkbox-list">
                                    @foreach($contrats as $contrat)
                                        <div class="checkbox-item">
                                            <input type="checkbox" id="modal_ct_{{ $contrat->id }}" name="contrats[]"
                                                value="{{ $contrat->id }}" />
                                            <label for="modal_ct_{{ $contrat->id }}">{{ $contrat->nom }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="cancelProjectModal">Annuler</button>
                        <button type="submit" class="btn btn-primary" id="submitProject">Créer le projet</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    @include('dashboard.partials.modals-api')
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Classic Modal Logic
            const modal = document.getElementById('projectModal');
            const openBtn = document.getElementById('openProjectModal');
            const closeBtn = document.getElementById('closeProjectModal');
            const cancelBtn = document.getElementById('cancelProjectModal');
            const form = document.getElementById('projectForm');
            const refreshBtn = document.getElementById('refreshProjetsBtn');

            if (refreshBtn) refreshBtn.addEventListener('click', refreshProjets);

            if (modal) {
                const toggleModal = (show) => {
                    if (show) {
                        modal.classList.add('active');
                        document.body.style.overflow = 'hidden';
                        form.reset();
                        form.querySelectorAll('.form-error').forEach(el => el.textContent = '');
                        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                    } else {
                        modal.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                };

                if (openBtn) openBtn.addEventListener('click', () => toggleModal(true));
                if (closeBtn) closeBtn.addEventListener('click', () => toggleModal(false));
                if (cancelBtn) cancelBtn.addEventListener('click', () => toggleModal(false));
                modal.addEventListener('click', (e) => { if (e.target === modal) toggleModal(false); });

                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const submitBtn = document.getElementById('submitProject');
                    const originalText = submitBtn.textContent;
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Création...';
                    const formData = new FormData(form);

                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'Accept': 'application/json' }
                    })
                        .then(response => response.json().then(data => ({ status: response.status, body: data })))
                        .then(({ status, body }) => {
                            if (status === 200 || status === 201) {
                                window.location.reload();
                            } else if (status === 422) {
                                Object.keys(body.errors).forEach(key => {
                                    const input = form.querySelector(`[name="${key}"]`) || form.querySelector(`[name="${key}[]"]`);
                                    if (input) {
                                        input.classList.add('is-invalid');
                                        const errorLabel = document.getElementById(`error_${key}`);
                                        if (errorLabel) errorLabel.textContent = body.errors[key][0];
                                    }
                                });
                                submitBtn.disabled = false;
                                submitBtn.textContent = originalText;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            submitBtn.disabled = false;
                            submitBtn.textContent = originalText;
                        });
                });
            }

            // JS Refresh Projets
            async function refreshProjets(e) {
                const tableBody = document.querySelector('.data-table tbody');
                const refreshBtn = e ? e.target : null;
                if (refreshBtn) {
                    refreshBtn.disabled = true;
                    refreshBtn.innerHTML = 'Chargement...';
                }

                try {
                    const response = await fetch('/api/projets', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const projets = await response.json();

                    tableBody.innerHTML = '';
                    projets.forEach(p => {
                        const row = document.createElement('tr');
                        row.className = 'refresh-flash';
                        const debut = p.date_debut ? new Date(p.date_debut).toLocaleDateString('fr-FR') : '—';
                        const fin = p.date_fin_prevue ? new Date(p.date_fin_prevue).toLocaleDateString('fr-FR') : '—';
                        const statusBadge = p.statut.replace('_', '-');
                        const statusLabel = p.statut.charAt(0).toUpperCase() + p.statut.slice(1).replace('_', ' ');
                        const clients = p.clients ? p.clients.map(c => c.name + ' ' + (c.surname || '')).join(', ') : '—';

                        row.innerHTML = `
                            <td style="color:var(--text-muted);font-size:var(--font-size-xs)">${p.id}</td>
                            <td><a href="/projets/${p.id}" style="font-weight:600;color:var(--text-primary)">${p.nom}</a></td>
                            <td>${clients || '—'}</td>
                            <td><span class="badge badge-${statusBadge}">${statusLabel}</span></td>
                            <td>${debut}</td>
                            <td>${fin}</td>
                            <td>
                                <div class="table-actions">
                                    <a href="/projets/${p.id}" class="btn btn-sm btn-secondary"><img src="/assets/oeil.png" alt="Voir" style="width:16px" /></a>
                                </div>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                } catch (error) {
                    alert('Erreur lors du chargement des projets');
                    console.error(error);
                } finally {
                    if (refreshBtn) {
                        refreshBtn.disabled = false;
                        refreshBtn.innerHTML = 'Actualiser (API)';
                    }
                }
            }
        });
    </script>
@endpush