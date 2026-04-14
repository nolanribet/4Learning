// =============================================================
//  questions.js — JavaScript de la page questions.html
//  Gestion : affichage, filtres, nouvelle question, détail
// =============================================================

/* ===== SESSION ===== */
function getSession() {
    const data = localStorage.getItem('4learning_user');
    return data ? JSON.parse(data) : null;
}

function clearSession() {
    localStorage.removeItem('4learning_user');
}

/* ===== NAVBAR ===== */
function updateNavbar() {
    const user     = getSession();
    const authEl   = document.getElementById('navbar-auth');
    const userEl   = document.getElementById('navbar-user');
    const usernameEl = document.getElementById('nav-username');
    const pointsEl = document.getElementById('nav-points');

    if (user) {
        authEl.classList.add('hidden');
        userEl.classList.remove('hidden');
        usernameEl.textContent = user.username;
        pointsEl.textContent   = user.points + ' pts';
    } else {
        authEl.classList.remove('hidden');
        userEl.classList.add('hidden');
    }
}

document.getElementById('btn-logout').addEventListener('click', async () => {
    const fd = new FormData();
    fd.append('action', 'logout');
    try { await fetch('../php/mvc_users/crud_users.php', { method: 'POST', body: fd }); } finally {
        clearSession();
        updateNavbar();
    }
});

/* ===== MODALS ===== */
function openModal(id)  { document.getElementById(id).classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); document.body.style.overflow = ''; }

document.getElementById('close-question').addEventListener('click', () => closeModal('modal-question'));
document.getElementById('overlay-question').addEventListener('click', () => closeModal('modal-question'));
document.getElementById('close-detail').addEventListener('click',    () => closeModal('modal-detail'));
document.getElementById('overlay-detail').addEventListener('click',  () => closeModal('modal-detail'));

// Bouton nouvelle question : exige la connexion
document.getElementById('btn-new-question').addEventListener('click', () => {
    if (!getSession()) {
        alert('Vous devez être connecté pour poser une question.');
        return;
    }
    openModal('modal-question');
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeModal('modal-question');
        closeModal('modal-detail');
    }
});

/* ===== CHARGEMENT DES MATIÈRES ===== */
async function loadMatieres() {
    try {
        const res  = await fetch('../php/mvc_tags/crud_tags.php');
        const data = await res.json();
        if (!data.success) return;

        const selFilter  = document.getElementById('filter-matiere');
        const selQuestion = document.getElementById('q-matiere');

        data.tags.forEach(m => {
            const opt1 = new Option(m.nom, m.id);
            const opt2 = new Option(m.nom, m.id);
            selFilter.appendChild(opt1);
            selQuestion.appendChild(opt2);
        });
    } catch { /* PHP pas encore prêt */ }
}

/* ===== DONNÉES LOCALES DE DÉMO ===== */
// Utilisées quand le backend PHP n'est pas encore configuré
const DEMO_QUESTIONS = [
    {
        id: 1,
        titre: 'Comment résoudre une équation du second degré ?',
        contenu: 'Je n\'arrive pas à comprendre la méthode pour trouver les racines. J\'ai essayé le discriminant mais je bloque sur les cas avec Δ < 0.',
        matiere: 'Mathématiques',
        matiere_couleur: '#134611',
        statut: 'ouverte',
        auteur: 'Alice',
        niveau_requis: 1,
        nb_reponses: 3,
        date: '28 mars 2026'
    },
    {
        id: 2,
        titre: 'Différence entre un tableau et une liste chaînée ?',
        contenu: 'En algorithmique, quand utilise-t-on l\'un plutôt que l\'autre ? Quelles sont les complexités respectives ?',
        matiere: 'Algorithmique',
        matiere_couleur: '#1a6b3c',
        statut: 'resolue',
        auteur: 'Bob',
        niveau_requis: 2,
        nb_reponses: 5,
        date: '27 mars 2026'
    },
    {
        id: 3,
        titre: 'Qu\'est-ce qu\'une clé étrangère en SQL ?',
        contenu: 'Je comprends les clés primaires mais les clés étrangères me posent problème. Comment les définir et à quoi servent-elles exactement ?',
        matiere: 'Bases de données',
        matiere_couleur: '#3d8b5e',
        statut: 'ouverte',
        auteur: 'Carol',
        niveau_requis: 1,
        nb_reponses: 2,
        date: '26 mars 2026'
    },
    {
        id: 4,
        titre: 'Comment calculer la variance d\'un échantillon ?',
        contenu: 'Le cours parle de variance corrigée et de variance empirique, quelle est la différence ?',
        matiere: 'Statistiques',
        matiere_couleur: '#2a7d4f',
        statut: 'ouverte',
        auteur: 'Dave',
        niveau_requis: 1,
        nb_reponses: 0,
        date: '30 mars 2026'
    }
];

/* ===== AFFICHAGE DES QUESTIONS ===== */
let allQuestions = [];

/**
 * Construit la carte HTML d'une question.
 * @param {object} q - Objet question
 * @returns {HTMLElement}
 */
function buildQuestionCard(q) {
    const card = document.createElement('article');
    card.className = 'question-card';
    card.dataset.id = q.id;
    card.style.borderLeftColor = q.matiere_couleur || '#14080E';

    const statutClass = `statut-${q.statut}`;
    const statutLabel = { ouverte: 'Ouverte', resolue: 'Résolue', fermee: 'Fermée' }[q.statut] || q.statut;

    card.innerHTML = `
        <div class="question-card__header">
            <span class="question-card__badge" style="background:${q.matiere_couleur || '#5c6672'}">${q.matiere}</span>
            <span class="question-card__titre">${escapeHtml(q.titre)}</span>
            <span class="question-card__statut ${statutClass}">${statutLabel}</span>
        </div>
        <p class="question-card__contenu">${escapeHtml(q.contenu)}</p>
        <div class="question-card__footer">
            <span>Par <strong>${escapeHtml(q.auteur)}</strong></span>
            <span>Niveau requis : <strong>L${q.niveau_requis}</strong></span>
            <span>${q.nb_reponses} réponse${q.nb_reponses !== 1 ? 's' : ''}</span>
            <span>${q.date}</span>
        </div>
    `;

    card.addEventListener('click', () => openQuestionDetail(q));
    return card;
}

/**
 * Affiche la liste de questions dans le DOM.
 * @param {Array} questions
 */
function renderQuestions(questions) {
    const list = document.getElementById('questions-list');
    list.innerHTML = '';

    if (questions.length === 0) {
        list.innerHTML = '<p class="empty-state">Aucune question ne correspond à vos filtres.</p>';
        return;
    }

    questions.forEach(q => list.appendChild(buildQuestionCard(q)));
}

/* ===== CHARGEMENT BACKEND (ou démo) ===== */
async function loadQuestions() {
    try {
        const res  = await fetch('../php/mvc_questions/vue_questions.php');
        const data = await res.json();
        allQuestions = data.success ? data.questions : DEMO_QUESTIONS;
    } catch {
        // Si le PHP n'est pas disponible, on utilise les données de démo
        allQuestions = DEMO_QUESTIONS;
    }
    applyFilters();
}

/* ===== FILTRES ===== */
function applyFilters() {
    const search  = document.getElementById('search-input').value.toLowerCase();
    const matiere = document.getElementById('filter-matiere').value;
    const statut  = document.getElementById('filter-statut').value;
    const tri     = document.getElementById('filter-tri').value;

    let result = allQuestions.filter(q => {
        const matchSearch  = q.titre.toLowerCase().includes(search) || q.contenu.toLowerCase().includes(search);
        const matchMatiere = !matiere || String(q.tag_id) === matiere;
        const matchStatut  = !statut  || q.statut === statut;
        return matchSearch && matchMatiere && matchStatut;
    });

    if (tri === 'populaire') {
        result.sort((a, b) => b.nb_reponses - a.nb_reponses);
    }

    renderQuestions(result);
}

document.getElementById('search-input').addEventListener('input', applyFilters);
document.getElementById('filter-matiere').addEventListener('change', applyFilters);
document.getElementById('filter-statut').addEventListener('change', applyFilters);
document.getElementById('filter-tri').addEventListener('change', applyFilters);

/* ===== DÉTAIL D'UNE QUESTION ===== */
function openQuestionDetail(q) {
    const container = document.getElementById('detail-content');
    container.innerHTML = `
        <span class="question-card__badge" style="background:${q.matiere_couleur || '#5c6672'};margin-bottom:.75rem;display:inline-block">${q.matiere}</span>
        <h2 style="margin-bottom:.75rem;color:var(--vert-fonce)">${escapeHtml(q.titre)}</h2>
        <p style="color:var(--gris);margin-bottom:1.5rem">${escapeHtml(q.contenu)}</p>
        <hr style="border-color:var(--gris-clair);margin-bottom:1.5rem">
        <h3 style="margin-bottom:1rem">Réponses (${q.nb_reponses})</h3>
        <div id="reponses-list"><em style="color:var(--gris)">Chargement des réponses...</em></div>
        <hr style="border-color:var(--gris-clair);margin:1.5rem 0">
        <h3 style="margin-bottom:1rem">Apporter une réponse</h3>
        <form id="form-answer" novalidate>
            <div class="form-group">
                <textarea name="contenu" rows="5" placeholder="Rédigez votre réponse..." required></textarea>
            </div>
            <p class="form-error hidden" id="answer-error"></p>
            <p class="form-success hidden" id="answer-success"></p>
            <button type="submit" class="btn btn--primary">Publier ma réponse</button>
        </form>
    `;
    openModal('modal-detail');
    loadReponses(q.id);

    document.getElementById('form-answer').addEventListener('submit', async (e) => {
        e.preventDefault();
        await submitAnswer(q.id, e.target);
    });
}

async function loadReponses(questionId) {
    const list = document.getElementById('reponses-list');
    try {
        const res  = await fetch(`../php/mvc_reponses/vue_reponses.php?question_id=${questionId}`);
        const data = await res.json();
        if (data.success && data.answers.length > 0) {
            list.innerHTML = data.answers.map(a => buildAnswerHtml(a)).join('');
        } else {
            list.innerHTML = '<p class="empty-state">Aucune réponse pour l\'instant. Soyez le premier !</p>';
        }
    } catch {
        list.innerHTML = '<p class="empty-state">Impossible de charger les réponses (serveur PHP non démarré).</p>';
    }
}

function buildAnswerHtml(a) {
    const stars = buildStars(a.note_moy, a.id);
    return `
        <div style="background:var(--blanc);border:2px solid var(--gris-clair);border-radius:var(--radius-lg);padding:1.25rem;margin-bottom:1rem">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem">
                <strong style="color:var(--vert-fonce)">${escapeHtml(a.auteur)}</strong>
                <span style="font-size:.82rem;color:var(--gris)">${a.date}</span>
            </div>
            <p style="color:var(--noir);margin-bottom:.75rem">${escapeHtml(a.contenu)}</p>
            <div style="display:flex;align-items:center;gap:.75rem">
                <span class="stars-rating" data-answer="${a.id}">${stars}</span>
                <span style="font-size:.85rem;color:var(--gris)">${a.note_moy > 0 ? a.note_moy.toFixed(1) + '/5' : 'Pas encore noté'} (${a.nb_votes} vote${a.nb_votes !== 1 ? 's' : ''})</span>
            </div>
        </div>
    `;
}

/**
 * Construit le HTML des étoiles pour la notation.
 * @param {number} currentNote  - Note actuelle (0-5)
 * @param {number} answerId
 * @returns {string}
 */
function buildStars(currentNote, answerId) {
    let html = '';
    for (let i = 1; i <= 5; i++) {
        const active = i <= Math.round(currentNote) ? 'active' : '';
        html += `<span class="star ${active}" data-note="${i}" data-answer="${answerId}">★</span>`;
    }
    return html;
}

// Délégation d'événement pour la notation des étoiles
document.getElementById('detail-content').addEventListener('click', async (e) => {
    const star = e.target.closest('.star');
    if (!star) return;

    const note     = parseInt(star.dataset.note, 10);
    const answerId = parseInt(star.dataset.answer, 10);
    await rateAnswer(answerId, note);
});

async function rateAnswer(answerId, note) {
    if (!getSession()) {
        alert('Vous devez être connecté pour noter une réponse.');
        return;
    }
    const formData = new FormData();
    formData.append('answer_id', answerId);
    formData.append('note', note);
    formData.append('action', 'rate');

    try {
        const res  = await fetch('../php/mvc_reponses/crud_reponses.php', { method: 'POST', body: formData });
        const data = await res.json();
        alert(data.message);
    } catch {
        alert('Erreur réseau.');
    }
}

async function submitAnswer(questionId, form) {
    const errorEl   = document.getElementById('answer-error');
    const successEl = document.getElementById('answer-success');
    errorEl.classList.add('hidden');
    successEl.classList.add('hidden');

    if (!getSession()) {
        errorEl.textContent = 'Vous devez être connecté pour répondre.';
        errorEl.classList.remove('hidden');
        return;
    }

    const formData = new FormData(form);
    formData.append('question_id', questionId);
    formData.append('action', 'create');

    try {
        const res  = await fetch('../php/mvc_reponses/crud_reponses.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
            successEl.textContent = data.message;
            successEl.classList.remove('hidden');
            form.reset();
            loadReponses(questionId);
        } else {
            errorEl.textContent = data.message;
            errorEl.classList.remove('hidden');
        }
    } catch {
        errorEl.textContent = 'Erreur réseau.';
        errorEl.classList.remove('hidden');
    }
}

/* ===== FORMULAIRE NOUVELLE QUESTION ===== */
document.getElementById('form-question').addEventListener('submit', async (e) => {
    e.preventDefault();
    const errorEl   = document.getElementById('question-error');
    const successEl = document.getElementById('question-success');
    errorEl.classList.add('hidden');
    successEl.classList.add('hidden');

    const formData = new FormData(e.target);

    try {
        const res  = await fetch('../php/mvc_questions/crud_questions.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
            successEl.textContent = data.message;
            successEl.classList.remove('hidden');
            e.target.reset();
            setTimeout(() => { closeModal('modal-question'); loadQuestions(); }, 1500);
        } else {
            errorEl.textContent = data.message;
            errorEl.classList.remove('hidden');
        }
    } catch {
        errorEl.textContent = 'Erreur réseau.';
        errorEl.classList.remove('hidden');
    }
});

/* ===== UTILITAIRES ===== */
function escapeHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

/* ===== INITIALISATION ===== */
updateNavbar();
loadMatieres();
loadQuestions();
