<!-- Ticket Quick Add Modal -->
<div id="modalTicket" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">🎫 Quick Add Ticket (API)</h3>
            <button class="modal-close" onclick="closeQuickAdd('Ticket')">×</button>
        </div>
        <form id="formTicket">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label required">Titre</label>
                    <input type="text" name="titre" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description détaillée</label>
                    <textarea name="description" class="form-textarea" required></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Projet associé</label>
                        <select name="projet_id" class="form-select">
                            <option value="">— Aucun —</option>
                            @foreach(App\Models\Projet::all() as $p)
                                <option value="{{ $p->id }}">{{ $p->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="inclus">Inclus</option>
                            <option value="facturable">Facturable</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Priorité</label>
                        <select name="priorite" class="form-select">
                            <option value="basse">Basse</option>
                            <option value="moyenne" selected>Moyenne</option>
                            <option value="haute">Haute</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Temps estimé (h)</label>
                        <input type="number" step="0.5" min="0" name="temps_estime" class="form-input" value="0">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Assigner à</label>
                    <select name="collaborateurs[]" class="form-select" multiple style="height:80px;">
                        @foreach(App\Models\User::where('role', 'collaborateur')->get() as $collab)
                            <option value="{{ $collab->id }}">{{ $collab->name }} {{ $collab->surname }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="statut" value="nouveau">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeQuickAdd('Ticket')">Annuler</button>
                <button type="submit" class="btn btn-primary">Créer le ticket</button>
            </div>
        </form>
    </div>
</div>

<!-- Projet Quick Add Modal -->
<div id="modalProjet" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">📁 Quick Add Projet (API)</h3>
            <button class="modal-close" onclick="closeQuickAdd('Projet')">×</button>
        </div>
        <form id="formProjet">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label required">Nom du Projet</label>
                    <input type="text" name="nom" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Statut</label>
                        <select name="statut" class="form-select">
                            <option value="en_attente">En attente</option>
                            <option value="en_cours" selected>En cours</option>
                            <option value="termine">Terminé</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date de début</label>
                        <input type="date" name="date_debut" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date fin prévue</label>
                        <input type="date" name="date_fin_prevue" class="form-input">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Clients</label>
                        <select name="clients[]" class="form-select" multiple style="height:80px;">
                            @foreach(App\Models\User::where('role', 'client')->get() as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} {{ $c->surname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Collaborateurs</label>
                        <select name="collaborateurs[]" class="form-select" multiple style="height:80px;">
                            @foreach(App\Models\User::where('role', 'collaborateur')->get() as $collab)
                                <option value="{{ $collab->id }}">{{ $collab->name }} {{ $collab->surname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Contrats associés</label>
                        <select name="contrats[]" class="form-select" multiple style="height:80px;">
                            @foreach(App\Models\Contrat::all() as $contrat)
                                <option value="{{ $contrat->id }}">{{ $contrat->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeQuickAdd('Projet')">Annuler</button>
                <button type="submit" class="btn btn-primary">Créer le projet</button>
            </div>
        </form>
    </div>
</div>

<!-- Contrat Quick Add Modal -->
<div id="modalContrat" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">📄 Quick Add Contrat (API)</h3>
            <button class="modal-close" onclick="closeQuickAdd('Contrat')">×</button>
        </div>
        <form id="formContrat">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label required">Nom du Contrat</label>
                    <input type="text" name="nom" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Conditions</label>
                    <textarea name="conditions" class="form-textarea"></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Statut</label>
                        <select name="statut" class="form-select" required>
                            <option value="actif">Actif</option>
                            <option value="inactif">Inactif</option>
                            <option value="termine">Terminé</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Heures incluses</label>
                        <input type="number" step="0.5" name="heures_totales" class="form-input" value="50">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Taux horaire (€)</label>
                        <input type="number" step="0.01" name="taux_horaire" class="form-input" value="80">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Date début</label>
                        <input type="date" name="date_debut" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date fin</label>
                        <input type="date" name="date_fin" class="form-input">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Clients associés</label>
                        <select name="clients[]" class="form-select" multiple style="height:80px;">
                            @foreach(App\Models\User::where('role', 'client')->get() as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} {{ $c->surname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Projets associés</label>
                        <select name="projets[]" class="form-select" multiple style="height:80px;">
                            @foreach(App\Models\Projet::all() as $p)
                                <option value="{{ $p->id }}">{{ $p->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeQuickAdd('Contrat')">Annuler</button>
                <button type="submit" class="btn btn-primary">Créer le contrat</button>
            </div>
        </form>
    </div>
</div>

<!-- User Quick Add Modal -->
<div id="modalUser" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">👤 Quick Add User (API)</h3>
            <button class="modal-close" onclick="closeQuickAdd('User')">×</button>
        </div>
        <form id="formUser">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Prénom</label>
                        <input type="text" name="name" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Nom</label>
                        <input type="text" name="surname" class="form-input" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label required">Email</label>
                    <input type="email" name="email" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Entreprise (pour les clients)</label>
                    <input type="text" name="company" class="form-input">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label required">Role</label>
                        <select name="role" class="form-select">
                            <option value="client">Client</option>
                            <option value="collaborateur" selected>Collaborateur</option>
                            <option value="admin">Administrateur</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Password</label>
                        <input type="password" name="password" class="form-input" value="password123" required>
                    </div>
                </div>
                <input type="hidden" name="state" value="active">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeQuickAdd('User')">Annuler</button>
                <button type="submit" class="btn btn-primary">Créer l'utilisateur</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openQuickAdd(type) {
        document.getElementById('modal' + type).classList.add('active');
    }

    function closeQuickAdd(type) {
        document.getElementById('modal' + type).classList.remove('active');
    }

    // Generic API submit function
    async function submitQuickAdd(e, endpoint, type) {
        e.preventDefault();
        const form = e.target;
        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerText = 'Envoi...';

        const formData = new FormData(form);
        const data = {};
        formData.forEach((value, key) => {
            if (key.endsWith('[]')) {
                const k = key.slice(0,-2);
                if (!data[k]) data[k] = [];
                data[k].push(value);
            } else {
                data[key] = value;
            }
        });

        try {
            const response = await fetch('/api/' + endpoint, {
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
                alert(type + ' créé avec succès via API !');
                closeQuickAdd(type);
                location.reload(); // Refresh dashboard stats
            } else {
                alert('Erreur: ' + JSON.stringify(result.errors || result.message));
            }
        } catch (err) {
            alert('Erreur réseau');
            console.error(err);
        } finally {
            btn.disabled = false;
            btn.innerText = 'Créer';
        }
    }

    document.getElementById('formTicket').addEventListener('submit', (e) => submitQuickAdd(e, 'tickets', 'Ticket'));
    document.getElementById('formProjet').addEventListener('submit', (e) => submitQuickAdd(e, 'projets', 'Projet'));
    document.getElementById('formContrat').addEventListener('submit', (e) => submitQuickAdd(e, 'contrats', 'Contrat'));
    document.getElementById('formUser').addEventListener('submit', (e) => submitQuickAdd(e, 'utilisateurs', 'User'));
</script>
