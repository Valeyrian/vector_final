@extends('layouts.app')
@section('title', 'Tickets')
@section('page-title', 'Gestion des Tickets')
@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Tickets</h2>
            <p>{{ $tickets->total() }} ticket(s)</p>
        </div>
        <div class="page-header-actions">
            <button class="btn btn-secondary" onclick="refreshTickets(event)"><img src="{{ asset('assets/oeil.png') }}" alt="Refresh" style="width:16px; margin-right:5px; filter:invert(1)"> Actualiser (API)</button>
            <button class="btn btn-primary" onclick="openModal()">+ Quick Add (API)</button>
            <a href="{{ route('tickets.create') }}" class="btn btn-primary">+ Nouveau Ticket</a>
        </div>
    </div>

    <form method="GET" action="{{ route('tickets.index') }}">
        <div class="filters-bar">
            <div class="filter-group">
                <label>Rechercher</label>
                <input type="text" name="search" class="filter-input" placeholder="Titre, description..."
                    value="{{ request('search') }}" />
            </div>
            <div class="filter-group">
                <label>Statut</label>
                <select name="statut" class="filter-input">
                    <option value="">Tous</option>
                    @foreach(['nouveau'=>'Nouveau', 'en_cours'=>'En cours', 'en_attente_client'=>'En attente client', 'termine'=>'Terminé', 'a_valider'=>'À valider', 'valide'=>'Validé', 'refuse'=>'Refusé', 'bloque' => 'Bloqué'] as $v => $l)
                        <option value="{{ $v }}" {{ request('statut') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Priorité</label>
                <select name="priorite" class="filter-input">
                    <option value="">Toutes</option>
                    @foreach(['haute', 'moyenne', 'basse'] as $p)
                        <option value="{{ $p }}" {{ request('priorite') === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label>Type</label>
                <select name="type" class="filter-input">
                    <option value="">Tous</option>
                    @foreach(['inclus', 'facturable'] as $c)
                        <option value="{{ $c }}" {{ request('type') === $c ? 'selected' : '' }}>{{ ucfirst($c) }}</option>
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
                            <th>Titre</th>
                            <th>Projet</th>
                            <th>Type</th>
                            <th>Statut</th>
                            <th>Priorité</th>
                            <th>Assigné à</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td style="color:var(--text-muted);font-size:var(--font-size-xs)">{{ $ticket->id }}</td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px">
                                        <div class="priority-dot {{ $ticket->priorite }}"></div>
                                        <a href="{{ route('tickets.show', $ticket) }}"
                                            style="font-weight:600;color:var(--text-primary)">{{ $ticket->titre }}</a>
                                    </div>
                                </td>
                                <td>{{ $ticket->projet?->nom ?? '—' }}</td>
                                <td><span class="badge badge-normale">{{ ucfirst($ticket->type) }}</span></td>
                                <td><span
                                        class="badge badge-{{ str_replace('_', '-', $ticket->statut) }}">{{ ucfirst(str_replace('_', ' ', $ticket->statut)) }}</span>
                                </td>
                                <td><span class="badge badge-{{ $ticket->priorite }}">{{ ucfirst($ticket->priorite) }}</span>
                                </td>
                                <td>{{ $ticket->collaborateurs->pluck('name')->join(', ') ?: '—' }}</td>
                                <td style="font-size:var(--font-size-xs);color:var(--text-muted)">
                                    {{ $ticket->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-secondary"><img
                                                src="{{ asset('assets/oeil.png') }}" alt="Voir" /></a>
                                        @if($ticket->statut !== 'bloque')
                                            <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-sm btn-warning"><img
                                                    src="{{ asset('assets/editer.png') }}" alt="Modifier"
                                                    style="filter:brightness(10)" /></a>
                                        @endif
                                        @if(auth()->user()->isAdmin())
                                            <form method="POST" action="{{ route('tickets.destroy', $ticket) }}"
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
                                <td colspan="9">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">🎫</div>
                                        <h3>Aucun ticket trouvé</h3><a href="{{ route('tickets.create') }}"
                                            class="btn btn-primary" style="margin-top:var(--spacing-md)">Créer un ticket</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($tickets->hasPages())
                <div class="pagination-wrapper">{{ $tickets->appends(request()->query())->links() }}</div>
            @endif
        </div>
    </div>
    </div>

    <!-- API Modal for Quick Add -->
    <div id="quickAddModal" class="modal">
        <div class="modal-content" style="width: 700px;">
            <div class="modal-header">
                <h3><img src="{{ asset('assets/ticket.png') }}" alt="" style="width:20px; vertical-align:middle; margin-right:8px;" /> Quick Add Ticket (API)</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form id="apiCreateForm">
                <div class="form-group">
                    <label class="form-label required">Titre</label>
                    <input type="text" name="titre" id="api_titre" class="form-input" placeholder="Décrivez brièvement le problème..." required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description détaillée</label>
                    <textarea name="description" id="api_description" class="form-textarea" style="min-height:100px" placeholder="Décrivez le problème en détail..."></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Type</label>
                        <select name="type" id="api_type" class="form-select" required>
                            <option value="inclus">Inclus</option>
                            <option value="facturable">Facturable</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Priorité</label>
                        <select name="priorite" id="api_priorite" class="form-select" required>
                            <option value="basse">Basse</option>
                            <option value="moyenne" selected>Moyenne</option>
                            <option value="haute">Haute</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Statut initial</label>
                        <select name="statut" id="api_statut" class="form-select" required>
                            @foreach(['nouveau'=>'Nouveau', 'en_cours'=>'En cours', 'en_attente_client'=>'En attente client', 'termine'=>'Terminé', 'a_valider'=>'À valider', 'valide'=>'Validé', 'refuse'=>'Refusé'] as $v => $l)
                                <option value="{{ $v }}">{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Projet associé</label>
                        <select name="projet_id" id="api_projet_id" class="form-select">
                            <option value="">— Aucun projet —</option>
                            @foreach(App\Models\Projet::all() as $p)
                                <option value="{{ $p->id }}">{{ $p->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Temps estimé (h)</label>
                        <input type="number" step="0.5" min="0" name="temps_estime" id="api_temps_estime" class="form-input" value="0">
                    </div>
                </div>

                @if(auth()->user()->isAdmin())
                    <div class="form-group">
                        <label class="form-label">Assigner à</label>
                        <select name="collaborateurs[]" id="api_collaborateurs" class="form-select" multiple style="height: auto; min-height: 80px;">
                            @foreach(App\Models\User::whereIn('role', ['admin', 'collaborateur'])->get() as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }} {{ $agent->surname }}</option>
                            @endforeach
                        </select>
                        <small style="color:var(--text-muted)">Maintenez Ctrl/Cmd pour sélectionner plusieurs.</small>
                    </div>
                @endif

                <div style="margin-top:20px; display:flex; justify-content:flex-end; gap:10px; border-top: 1px solid #eee; padding-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitApi">Créer le ticket</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .modal-content {
            background-color: var(--bg-card);
            margin: 2% auto;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease;
            border: 1px solid var(--border-color);
        }
        @keyframes slideIn { from { transform: translateY(-20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .close {
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            color: #aaa;
        }
        .close:hover { color: #000; }
        .refresh-flash { animation: flashHighlight 1s ease; }
        @keyframes flashHighlight { 0% { background-color: rgba(52, 152, 219, 0.2); } 100% { background-color: transparent; } }
    </style>

    <script>
        function openModal() { document.getElementById('quickAddModal').style.display = 'block'; }
        function closeModal() { document.getElementById('quickAddModal').style.display = 'none'; }

        async function refreshTickets(e) {
            const tableBody = document.querySelector('.data-table tbody');
            const refreshBtn = e ? e.target : null;
            if (refreshBtn) {
                refreshBtn.disabled = true;
                refreshBtn.innerHTML = 'Chargement...';
            }

            try {
                const response = await fetch('/api/tickets', {
                    headers: { 'Accept': 'application/json' }
                });
                const tickets = await response.json();
                
                tableBody.innerHTML = '';
                tickets.forEach(ticket => {
                    const row = document.createElement('tr');
                    row.className = 'refresh-flash';
                    const createdAt = new Date(ticket.created_at).toLocaleDateString('fr-FR');
                    const statutBadge = ticket.statut.replace('_', '-');
                    const statutLabel = ticket.statut.charAt(0).toUpperCase() + ticket.statut.slice(1).replace('_', ' ');
                    const prioriteLabel = ticket.priorite.charAt(0).toUpperCase() + ticket.priorite.slice(1);
                    const typeLabel = ticket.type.charAt(0).toUpperCase() + ticket.type.slice(1);

                    const colabs = ticket.collaborateurs ? ticket.collaborateurs.map(c => c.name).join(', ') : '—';

                    row.innerHTML = `
                        <td style="color:var(--text-muted);font-size:var(--font-size-xs)">${ticket.id}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px">
                                <div class="priority-dot ${ticket.priorite}"></div>
                                <a href="/tickets/${ticket.id}" style="font-weight:600;color:var(--text-primary)">${ticket.titre}</a>
                            </div>
                        </td>
                        <td>${ticket.projet ? ticket.projet.nom : '—'}</td>
                        <td><span class="badge badge-normale">${typeLabel}</span></td>
                        <td><span class="badge badge-${statutBadge}">${statutLabel}</span></td>
                        <td><span class="badge badge-${ticket.priorite}">${prioriteLabel}</span></td>
                        <td>${colabs || '—'}</td>
                        <td style="font-size:var(--font-size-xs);color:var(--text-muted)">${createdAt}</td>
                        <td>
                            <div class="table-actions">
                                <a href="/tickets/${ticket.id}" class="btn btn-sm btn-secondary">
                                    <img src="/assets/oeil.png" alt="Voir" style="width:16px" />
                                </a>
                            </div>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } catch (error) {
                alert('Erreur lors du chargement des tickets');
                console.error(error);
            } finally {
                if (refreshBtn) {
                    refreshBtn.disabled = false;
                    refreshBtn.innerHTML = 'Actualiser (API)';
                }
            }
        }

        document.getElementById('apiCreateForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitApi');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = 'Envoi...';

            const formData = new FormData(e.target);
            const data = {};
            
            // Handle multiple select logic for collaborators[]
            formData.forEach((value, key) => {
                if (key.endsWith('[]')) {
                    const cleanKey = key.slice(0, -2);
                    if (!data[cleanKey]) data[cleanKey] = [];
                    data[cleanKey].push(value);
                } else {
                    data[key] = value;
                }
            });

            try {
                const response = await fetch('/api/tickets', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    alert('Ticket créé via API !');
                    closeModal();
                    await refreshTickets();
                } else {
                    alert('Erreur : ' + JSON.stringify(result.errors || result.message));
                }
            } catch (error) {
                alert('Erreur réseau ou serveur');
                console.error(error);
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        });
    </script>
@endsection