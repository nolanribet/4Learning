// =============================================================
//  profil.js — JavaScript de la page profil.html
// =============================================================

function getSession() {
    const data = localStorage.getItem('4learning_user');
    return data ? JSON.parse(data) : null;
}

function clearSession() {
    localStorage.removeItem('4learning_user');
}

function updateNavbar() {
    const user     = getSession();
    const authEl   = document.getElementById('navbar-auth');
    const userEl   = document.getElementById('navbar-user');
    if (!user) {
        authEl.classList.remove('hidden');
        userEl.classList.add('hidden');
    } else {
        authEl.classList.add('hidden');
        userEl.classList.remove('hidden');
        document.getElementById('nav-username').textContent = user.username;
        document.getElementById('nav-points').textContent   = user.points + ' pts';
    }
}

document.getElementById('btn-logout').addEventListener('click', async () => {
    const fd = new FormData();
    fd.append('action', 'logout');
    try { await fetch('../php/mvc_users/crud_users.php', { method: 'POST', body: fd }); } finally {
        clearSession();
        updateNavbar();
        showNotConnected();
    }
});

/* ===== AFFICHAGE SELON CONNEXION ===== */

function showNotConnected() {
    document.getElementById('not-connected').classList.remove('hidden');
    document.getElementById('profil-content').classList.add('hidden');
}

function showProfil() {
    document.getElementById('not-connected').classList.add('hidden');
    document.getElementById('profil-content').classList.remove('hidden');
}

/* ===== CHARGEMENT DU PROFIL ===== */

const NIVEAUX = { 1: 'L1', 2: 'L2', 3: 'L3', 4: 'M1', 5: 'M2' };

async function loadProfil(user) {
    // Données de base depuis la session
    document.getElementById('profil-initiale').textContent  = user.username.charAt(0).toUpperCase();
    document.getElementById('profil-username').textContent  = user.username;
    document.getElementById('profil-niveau').textContent    = NIVEAUX[user.niveau] || 'L1';
    document.getElementById('profil-points').textContent    = user.points.toLocaleString('fr-FR');
    document.getElementById('profil-fiabilite-val').textContent = user.fiabilite + '%';

    // Animation de la jauge de fiabilité
    setTimeout(() => {
        document.getElementById('profil-fiabilite-bar').style.width = user.fiabilite + '%';
    }, 100);

    // Données complémentaires depuis le serveur
    try {
        const res  = await fetch(`../php/mvc_users/vue_users.php?user_id=${user.id}`);
        const data = await res.json();
        if (data.success) {
            document.getElementById('profil-questions-count').textContent = data.nb_questions;
            document.getElementById('profil-answers-count').textContent   = data.nb_answers;
            renderBadges(data.badges);
            renderUserQuestions(data.questions);
            renderUserAnswers(data.answers);
        }
    } catch {
        // Données de démo si le serveur n'est pas disponible
        document.getElementById('profil-questions-count').textContent = '0';
        document.getElementById('profil-answers-count').textContent   = '0';
    }
}

/* ===== BADGES ===== */
function renderBadges(badges) {
    const grid = document.getElementById('profil-badges-grid');
    if (!badges || badges.length === 0) {
        grid.innerHTML = '<p class="empty-state">Aucun badge pour l\'instant. Participez pour en gagner !</p>';
        return;
    }
    grid.innerHTML = '';
    badges.forEach(b => {
        const item = document.createElement('div');
        item.className = 'badge-item';
        item.innerHTML = `
            <div class="badge-item__icon">${b.icone}</div>
            <div>
                <div class="badge-item__name">${escapeHtml(b.nom)}</div>
                <div class="badge-item__desc">${escapeHtml(b.description)}</div>
            </div>
        `;
        grid.appendChild(item);
    });
}

/* ===== QUESTIONS DE L'UTILISATEUR ===== */
function renderUserQuestions(questions) {
    const list = document.getElementById('profil-questions-list');
    if (!questions || questions.length === 0) {
        list.innerHTML = '<p class="empty-state">Aucune question posée pour l\'instant.</p>';
        return;
    }
    list.innerHTML = '';
    questions.forEach(q => {
        const card = document.createElement('article');
        card.className = 'question-card';
        card.innerHTML = `
            <div class="question-card__header">
                <span class="question-card__badge" style="background:${q.matiere_couleur || '#5c6672'}">${escapeHtml(q.matiere)}</span>
                <span class="question-card__titre">${escapeHtml(q.titre)}</span>
                <span class="question-card__statut statut-${q.statut}">${q.statut}</span>
            </div>
            <div class="question-card__footer">
                <span>${q.nb_reponses} réponse${q.nb_reponses !== 1 ? 's' : ''}</span>
                <span>${q.date}</span>
            </div>
        `;
        list.appendChild(card);
    });
}

/* ===== RÉPONSES DE L'UTILISATEUR ===== */
function renderUserAnswers(answers) {
    const list = document.getElementById('profil-answers-list');
    if (!answers || answers.length === 0) {
        list.innerHTML = '<p class="empty-state">Aucune réponse donnée pour l\'instant.</p>';
        return;
    }
    list.innerHTML = '';
    answers.forEach(a => {
        const item = document.createElement('div');
        item.style.cssText = 'background:var(--blanc);border:2px solid var(--gris-clair);border-radius:var(--radius-lg);padding:1.25rem;margin-bottom:1rem';
        item.innerHTML = `
            <div style="display:flex;justify-content:space-between;margin-bottom:.5rem">
                <span style="font-weight:600;color:var(--vert-fonce)">${escapeHtml(a.question_titre)}</span>
                <span style="font-size:.82rem;color:var(--gris)">${a.date}</span>
            </div>
            <p style="color:var(--noir);font-size:.93rem;margin-bottom:.5rem">${escapeHtml(a.contenu)}</p>
            <span style="font-size:.85rem;color:var(--gris)">
                Note : ${a.note_moy > 0 ? '★ ' + a.note_moy.toFixed(1) + '/5' : 'Non noté'} (${a.nb_votes} vote${a.nb_votes !== 1 ? 's' : ''})
            </span>
        `;
        list.appendChild(item);
    });
}

/* ===== ONGLETS ===== */
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
        btn.classList.add('active');
        document.getElementById(btn.dataset.tab).classList.remove('hidden');
    });
});

/* ===== UTILITAIRES ===== */
function escapeHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(String(str || '')));
    return div.innerHTML;
}

/* ===== INITIALISATION ===== */
updateNavbar();

const session = getSession();
if (session) {
    showProfil();
    loadProfil(session);
} else {
    showNotConnected();
    // Bouton "Se connecter" redirige vers l'accueil
    document.getElementById('btn-login-redirect').addEventListener('click', () => {
        window.location.href = '../index.php';
    });
}
