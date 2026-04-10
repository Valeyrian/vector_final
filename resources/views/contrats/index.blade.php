@extends('layouts.app')
@section('title', 'Contrats')
@section('page-title', 'Gestion des Contrats')
@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Contrats</h2>
            <p>{{ $contrats->total() }} contrat(s)</p>
        </div>
        <div class="page-header-actions">
            <button class="btn btn-secondary" id="refreshContratsBtn"><img src="{{ asset('assets/oeil.png') }}"
                    alt="Refresh" style="width:16px; margin-right:5px; filter:invert(1)"> Actualiser (API)</button>
            @if(auth()->user()->isAdmin())
                <button type="button" class="btn btn-primary" onclick="openQuickAdd('Contrat')">+ Quick Add (API)</button>
                <button type="button" class="btn btn-primary" id="openContratModal">+ Nouveau Contrat</button>
            @endif
        </div>
    </div>
    <form method="GET" action="{{ route('contrats.index') }}">
        <div class="filters-bar">
            <div class="filter-group">
                <label>Rechercher</label>
                <input type="text" name="search" class="filter-input" placeholder="Nom..."
                    value="{{ request('search') }}" />
            </div>
            <div class="filter-group">
                <label>Statut</label>
                <select name="statut" class="filter-input">
                    <option value="">Tous</option>
                    @foreach(['actif' => 'Actif', 'inactif' => 'Inactif', 'termine' => 'Terminé'] as $v => $l)
                        <option value="{{ $v }}" {{ request('statut') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group" style="justify-content:flex-end;"><label>&nbsp;</label>
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
                            <th>Montant</th>
                            <th>Date fin</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contrats as $contrat)
                            <tr>
                                <td style="color:var(--text-muted);font-size:var(--font-size-xs)">{{ $contrat->id }}</td>
                                <td><a href="{{ route('contrats.show', $contrat) }}"
                                        style="font-weight:600;color:var(--text-primary)">{{ $contrat->nom }}</a></td>

                                <td>{{ $contrat->clients->map->full_name->join(', ') ?: '—' }}</td>
                                <td><span
                                        class="badge badge-{{ $contrat->statut }}">{{ ucfirst(str_replace('_', ' ', $contrat->statut)) }}</span>
                                </td>
                                <td>{{ $contrat->montant_total ? number_format($contrat->montant_total, 2) . ' €' : '—' }}</td>
                                <td>{{ $contrat->date_fin?->format('d/m/Y') ?? '—' }}</td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('contrats.show', $contrat) }}" class="btn btn-sm btn-secondary"><img
                                                src="{{ asset('assets/oeil.png') }}" alt="Voir" /></a>
                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('contrats.edit', $contrat) }}" class="btn btn-sm btn-warning"><img
                                                    src="{{ asset('assets/editer.png') }}" alt="Modifier"
                                                    style="filter:brightness(10)" /></a>
                                            <form method="POST" action="{{ route('contrats.destroy', $contrat) }}"
                                                onsubmit="return confirm('Supprimer ?')" style="display:inline">
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
                                        <div class="empty-state-icon">📄</div>
                                        <h3>Aucun contrat trouvé</h3>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($contrats->hasPages())
            <div class="pagination-wrapper">{{ $contrats->appends(request()->query())->links() }}</div>@endif
        </div>
    </div>
    </div>

    {{-- Modal Nouveau Contrat --}}
    @if(auth()->user()->isAdmin())
        <div class="modal-overlay" id="contratModal">
            <div class="modal-container">
                <div class="modal-header">
                    <div class="modal-title">
                        <img src="{{ asset('assets/contrat.png') }}" alt="" style="width:24px" />
                        Nouveau Contrat
                    </div>
                    <button class="modal-close" id="closeContratModal">&times;</button>
                </div>
                <form id="contratForm" method="POST" action="{{ route('contrats.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label required" for="nom">Nom du contrat</label>
                            <input type="text" id="nom" name="nom" class="form-input" required />
                            <span class="form-error" id="error_nom"></span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="conditions">Conditions</label>
                            <textarea id="conditions" name="conditions" class="form-textarea"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label required" for="statut">Statut</label>
                            <select id="statut" name="statut" class="form-select" required>
                                <option value="actif">Actif</option>
                                <option value="inactif">Inactif</option>
                                <option value="termine">Terminé</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label required" for="date_debut">Date de début</label>
                                <input type="date" id="date_debut" name="date_debut" class="form-input" required />
                                <span class="form-error" id="error_date_debut"></span>
                            </div>
                            <div class="form-group">
                                <label class="form-label required" for="date_fin">Date de fin</label>
                                <input type="date" id="date_fin" name="date_fin" class="form-input" required />
                                <span class="form-error" id="error_date_fin"></span>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="heures_totales">Heures Totales</label>
                                <input type="number" id="heures_totales" name="heures_totales" class="form-input" value="0"
                                    min="0" step="0.5" />
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="taux_horaire">Taux Horaire (€)</label>
                                <input type="number" id="taux_horaire" name="taux_horaire" class="form-input" value="0" min="0"
                                    step="0.01" />
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
                            <label class="form-label">Projets associés</label>
                            <div class="checkbox-list">
                                @foreach($projets as $projet)
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="modal_pr_{{ $projet->id }}" name="projets[]"
                                            value="{{ $projet->id }}" />
                                        <label for="modal_pr_{{ $projet->id }}">{{ $projet->nom }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="cancelContratModal">Annuler</button>
                        <button type="submit" class="btn btn-primary" id="submitContrat">Créer le contrat</button>
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
            const modal = document.getElementById('contratModal');
            const openBtn = document.getElementById('openContratModal');
            const closeBtn = document.getElementById('closeContratModal');
            const cancelBtn = document.getElementById('cancelContratModal');
            const form = document.getElementById('contratForm');
            const refreshBtn = document.getElementById('refreshContratsBtn');

            if (refreshBtn) refreshBtn.addEventListener('click', refreshContrats);

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
                    const submitBtn = document.getElementById('submitContrat');
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

            // JS Refresh Contrats
            async function refreshContrats(e) {
                const tableBody = document.querySelector('.data-table tbody');
                const refreshBtn = e ? e.target : null;
                if (refreshBtn) {
                    refreshBtn.disabled = true;
                    refreshBtn.innerHTML = 'Chargement...';
                }

                try {
                    const response = await fetch('/api/contrats', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const contrats = await response.json();

                    tableBody.innerHTML = '';
                    contrats.forEach(c => {
                        const row = document.createElement('tr');
                        row.className = 'refresh-flash';
                        const fin = c.date_fin ? new Date(c.date_fin).toLocaleDateString('fr-FR') : '—';
                        const montant = c.montant_total ? new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(c.montant_total) : '—';
                        const clients = c.clients ? c.clients.map(cl => cl.name + ' ' + (cl.surname || '')).join(', ') : '—';

                        row.innerHTML = `
                            <td style="color:var(--text-muted);font-size:var(--font-size-xs)">${c.id}</td>
                            <td><a href="/contrats/${c.id}" style="font-weight:600;color:var(--text-primary)">${c.nom}</a></td>
                            <td>${clients || '—'}</td>
                            <td><span class="badge badge-${c.statut}">${c.statut.charAt(0).toUpperCase() + c.statut.slice(1)}</span></td>
                            <td>${montant}</td>
                            <td>${fin}</td>
                            <td>
                                <div class="table-actions">
                                    <a href="/contrats/${c.id}" class="btn btn-sm btn-secondary"><img src="/assets/oeil.png" alt="Voir" style="width:16px" /></a>
                                </div>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                } catch (error) {
                    alert('Erreur lors du chargement des contrats');
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