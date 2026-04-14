// =============================================================
//  classement.js — JavaScript de la page classement.html
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
    }
});

/* ===== DONNÉES DE DÉMO ===== */
const DEMO_CLASSEMENT = [
    { rang: 1, username: 'Alice',   niveau: 3, points: 420, fiabilite: 92, badges: ['🏆', '⭐'] },
    { rang: 2, username: 'Bob',     niveau: 2, points: 310, fiabilite: 78, badges: ['✏️']       },
    { rang: 3, username: 'Carol',   niveau: 1, points: 280, fiabilite: 85, badges: ['⭐']        },
    { rang: 4, username: 'Dave',    niveau: 4, points: 210, fiabilite: 65, badges: []            },
    { rang: 5, username: 'Eve',     niveau: 2, points: 190, fiabilite: 70, badges: ['✏️']       },
    { rang: 6, username: 'Franck',  niveau: 1, points: 145, fiabilite: 50, badges: []            },
    { rang: 7, username: 'Grace',   niveau: 3, points: 120, fiabilite: 88, badges: ['🛡️']       },
    { rang: 8, username: 'Hugo',    niveau: 2, points: 95,  fiabilite: 42, badges: []            },
];

const DEMO_BADGES = [
    { icone: '✏️',  nom: 'Première réponse', description: 'A répondu pour la première fois' },
    { icone: '⭐',  nom: 'Expert',           description: 'Note moyenne > 4.5'              },
    { icone: '🏆',  nom: 'Top 3 Saison',     description: 'Podium d\'une saison'            },
    { icone: '🛡️', nom: 'Fiable',           description: 'Fiabilité à 100%'               },
    { icone: '🔍',  nom: 'Curieux',          description: '10 questions posées'             },
];

/* ===== PODIUM ===== */
function renderPodium(users) {
    const top3   = users.slice(0, 3);
    const order  = [1, 0, 2]; // Affichage : 2e, 1er, 3e (pyramide)
    const podium = document.getElementById('podium');
    podium.innerHTML = '';

    order.forEach(idx => {
        if (!top3[idx]) return;
        const u   = top3[idx];
        const pos = idx + 1;
        const initiale = u.username.charAt(0).toUpperCase();

        const item = document.createElement('div');
        item.className = 'podium-item';
        item.innerHTML = `
            <div class="podium-avatar">${initiale}</div>
            <div class="podium-name">${escapeHtml(u.username)}</div>
            <div class="podium-points">${u.points} pts</div>
            <div class="podium-block podium-block--${pos}">${pos}</div>
        `;
        podium.appendChild(item);
    });
}

/* ===== TABLEAU ===== */
function renderLeaderboard(users) {
    const tbody = document.getElementById('leaderboard-body');
    tbody.innerHTML = '';

    const NIVEAUX = { 1: 'L1', 2: 'L2', 3: 'L3', 4: 'M1', 5: 'M2' };
    const RANK_CLASS = { 1: 'rank-gold', 2: 'rank-silver', 3: 'rank-bronze' };

    users.forEach(u => {
        const tr = document.createElement('tr');
        const rankClass = RANK_CLASS[u.rang] || '';
        const badgesHtml = u.badges.map(b => `<span title="${b}">${b}</span>`).join(' ');

        tr.innerHTML = `
            <td><strong class="${rankClass}">#${u.rang}</strong></td>
            <td>${escapeHtml(u.username)}</td>
            <td><span class="niveau-badge">${NIVEAUX[u.niveau] || 'L1'}</span></td>
            <td><strong>${u.points.toLocaleString('fr-FR')}</strong></td>
            <td>
                <div style="display:flex;align-items:center;gap:.5rem">
                    <div class="fiabilite-gauge">
                        <div class="fiabilite-bar" style="width:${u.fiabilite}%"></div>
                    </div>
                    <span style="font-size:.85rem;color:var(--gris)">${u.fiabilite}%</span>
                </div>
            </td>
            <td style="font-size:1.1rem">${badgesHtml || '—'}</td>
        `;
        tbody.appendChild(tr);
    });
}

/* ===== BADGES ===== */
function renderBadges(badges) {
    const grid = document.getElementById('badges-grid');
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

/* ===== CHARGEMENT BACKEND (ou démo) ===== */
async function loadClassement() {
    try {
        const res  = await fetch('../php/mvc_reponses/admin_reponses.php');
        const data = await res.json();
        if (data.success) {
            document.getElementById('saison-label').textContent = data.saison || 'Saison en cours';
            renderPodium(data.users);
            renderLeaderboard(data.users);
            renderBadges(data.badges || DEMO_BADGES);
            return;
        }
    } catch { /* PHP non disponible */ }

    // Données de démo
    document.getElementById('saison-label').textContent = 'Saison 1 — Printemps 2026 (démo)';
    renderPodium(DEMO_CLASSEMENT);
    renderLeaderboard(DEMO_CLASSEMENT);
    renderBadges(DEMO_BADGES);
}

/* ===== UTILITAIRES ===== */
function escapeHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(String(str)));
    return div.innerHTML;
}

/* ===== INITIALISATION ===== */
updateNavbar();
loadClassement();
