// =============================================================
//  script.js — JavaScript de la page d'accueil (index.php)
//  Gestion : modals connexion/inscription, session, stats
// =============================================================

/* ===== SÉLECTEURS ===== */
const modalLogin      = document.getElementById('modal-login');
const modalRegister   = document.getElementById('modal-register');
const btnLogin        = document.getElementById('btn-login');
const btnRegister     = document.getElementById('btn-register');
const heroBtnRegister = document.getElementById('hero-btn-register');
const btnLogout       = document.getElementById('btn-logout');
const closeLogin      = document.getElementById('close-login');
const closeRegister   = document.getElementById('close-register');
const overlayLogin    = document.getElementById('overlay-login');
const overlayRegister = document.getElementById('overlay-register');
const switchToRegister = document.getElementById('switch-to-register');
const switchToLogin   = document.getElementById('switch-to-login');
const formLogin       = document.getElementById('form-login');
const formRegister    = document.getElementById('form-register');
const navbarAuth      = document.getElementById('navbar-auth');
const navbarUser      = document.getElementById('navbar-user');
const navUsername     = document.getElementById('nav-username');
const navPoints       = document.getElementById('nav-points');

/* ===== GESTION DE SESSION (localStorage) ===== */

function getSession() {
    const data = localStorage.getItem('4learning_user');
    return data ? JSON.parse(data) : null;
}

function saveSession(user) {
    localStorage.setItem('4learning_user', JSON.stringify(user));
}

function clearSession() {
    localStorage.removeItem('4learning_user');
}

function updateNavbar() {
    const user = getSession();
    if (user) {
        navbarAuth.classList.add('hidden');
        navbarUser.classList.remove('hidden');
        navUsername.textContent = user.username;
        navPoints.textContent   = user.points + ' pts';
    } else {
        navbarAuth.classList.remove('hidden');
        navbarUser.classList.add('hidden');
    }
}

/* ===== OUVERTURE / FERMETURE DES MODALS ===== */

function openModal(modal) {
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeModal(modal) {
    modal.classList.add('hidden');
    document.body.style.overflow = '';
}

btnLogin.addEventListener('click',    () => openModal(modalLogin));
btnRegister.addEventListener('click', () => openModal(modalRegister));
if (heroBtnRegister) heroBtnRegister.addEventListener('click', () => openModal(modalRegister));

closeLogin.addEventListener('click',      () => closeModal(modalLogin));
closeRegister.addEventListener('click',   () => closeModal(modalRegister));
overlayLogin.addEventListener('click',    () => closeModal(modalLogin));
overlayRegister.addEventListener('click', () => closeModal(modalRegister));

switchToRegister.addEventListener('click', () => { closeModal(modalLogin);    openModal(modalRegister); });
switchToLogin.addEventListener('click',    () => { closeModal(modalRegister); openModal(modalLogin);    });

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') { closeModal(modalLogin); closeModal(modalRegister); }
});

/* ===== FORMULAIRE CONNEXION ===== */

formLogin.addEventListener('submit', async (e) => {
    e.preventDefault();
    const errorEl = document.getElementById('login-error');
    errorEl.classList.add('hidden');

    const formData = new FormData(formLogin);
    formData.append('action', 'login');

    try {
        const res  = await fetch('php/mvc_users/crud_users.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
            saveSession(data.user);
            closeModal(modalLogin);
            updateNavbar();
            formLogin.reset();
        } else {
            errorEl.textContent = data.message;
            errorEl.classList.remove('hidden');
        }
    } catch {
        errorEl.textContent = 'Erreur réseau. Vérifiez votre connexion.';
        errorEl.classList.remove('hidden');
    }
});

/* ===== FORMULAIRE INSCRIPTION ===== */

formRegister.addEventListener('submit', async (e) => {
    e.preventDefault();
    const errorEl   = document.getElementById('register-error');
    const successEl = document.getElementById('register-success');
    errorEl.classList.add('hidden');
    successEl.classList.add('hidden');

    const formData = new FormData(formRegister);
    formData.append('action', 'register');

    try {
        const res  = await fetch('php/mvc_users/crud_users.php', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.success) {
            successEl.textContent = data.message;
            successEl.classList.remove('hidden');
            formRegister.reset();
            setTimeout(() => {
                closeModal(modalRegister);
                openModal(modalLogin);
                successEl.classList.add('hidden');
            }, 2000);
        } else {
            errorEl.textContent = data.message;
            errorEl.classList.remove('hidden');
        }
    } catch {
        errorEl.textContent = 'Erreur réseau. Vérifiez votre connexion.';
        errorEl.classList.remove('hidden');
    }
});

/* ===== DÉCONNEXION ===== */

btnLogout.addEventListener('click', async () => {
    const fd = new FormData();
    fd.append('action', 'logout');
    try { await fetch('php/mvc_users/crud_users.php', { method: 'POST', body: fd }); } finally {
        clearSession();
        updateNavbar();
    }
});

/* ===== STATS DE LA PAGE D'ACCUEIL ===== */

async function loadStats() {
    try {
        const res  = await fetch('php/mvc_users/admin_users.php');
        const data = await res.json();
        if (data.success) {
            animateCount('stat-users',     data.users);
            animateCount('stat-questions', data.questions);
            animateCount('stat-answers',   data.answers);
        }
    } catch { /* Silencieux si PHP non configuré */ }
}

function animateCount(id, end) {
    const el = document.getElementById(id);
    if (!el || !end) return;
    const duration = 1200;
    const step     = end / (duration / 16);
    let current    = 0;
    const timer = setInterval(() => {
        current += step;
        if (current >= end) { current = end; clearInterval(timer); }
        el.textContent = Math.floor(current).toLocaleString('fr-FR');
    }, 16);
}

/* ===== INITIALISATION ===== */

updateNavbar();
loadStats();
