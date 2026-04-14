<?php
// =============================================================
//  index.php — Page d'accueil de 4Learning
// =============================================================
$page_title  = 'Accueil';
$active_page = '';
$base_path   = '';
require_once __DIR__ . '/php/template/header.php';
?>

    <!-- ===== SECTION HERO ===== -->
    <main>
        <section class="hero">
            <div class="hero__content">
                <img src="assets/img/Logo Tampon.png" alt="Logo 4Learning" class="hero__logo">
                <h1 class="hero__title">L'entraide universitaire,<br><span class="text-green">réinventée.</span></h1>
                <p class="hero__subtitle">
                    Pose tes questions, partage tes connaissances, monte en niveau.<br>
                    La plateforme collaborative de l'Université Savoie Mont Blanc.
                </p>
                <div class="hero__actions">
                    <button class="btn btn--primary btn--lg" id="hero-btn-register">Rejoindre la communauté</button>
                    <a href="pages/questions.php" class="btn btn--outline btn--lg">Voir les questions</a>
                </div>
            </div>
        </section>

        <!-- ===== SECTION FEATURES ===== -->
        <section class="features">
            <div class="container">
                <h2 class="section-title">Comment ça marche ?</h2>
                <div class="features__grid">
                    <div class="feature-card">
                        <div class="feature-card__icon">❓</div>
                        <h3>Pose une question</h3>
                        <p>Sélectionne ta matière, décris ton problème et fixe le niveau de réponse attendu.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-card__icon">💡</div>
                        <h3>Reçois des réponses</h3>
                        <p>Seuls les étudiants du bon niveau peuvent répondre pour garantir la qualité.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-card__icon">⭐</div>
                        <h3>Note et sois noté</h3>
                        <p>Vote de 1 à 5 étoiles pour chaque réponse. Ta fiabilité évolue avec tes notes.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-card__icon">🏆</div>
                        <h3>Grimpe au classement</h3>
                        <p>Accumule des points, remporte des badges et domine le classement saisonnier.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== SECTION STATS RAPIDES ===== -->
        <section class="stats">
            <div class="container">
                <div class="stats__grid">
                    <div class="stat-item">
                        <span class="stat-item__number" id="stat-users">—</span>
                        <span class="stat-item__label">Étudiants</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-item__number" id="stat-questions">—</span>
                        <span class="stat-item__label">Questions posées</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-item__number" id="stat-answers">—</span>
                        <span class="stat-item__label">Réponses données</span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- ===== MODAL CONNEXION ===== -->
    <div class="modal hidden" id="modal-login" role="dialog" aria-modal="true" aria-labelledby="modal-login-title">
        <div class="modal__overlay" id="overlay-login"></div>
        <div class="modal__box">
            <button class="modal__close" id="close-login" aria-label="Fermer">✕</button>
            <h2 id="modal-login-title">Connexion</h2>
            <form id="form-login" novalidate>
                <div class="form-group">
                    <label for="login-email">Adresse e-mail</label>
                    <input type="email" id="login-email" name="email" placeholder="etudiant@univ-smb.fr" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Mot de passe</label>
                    <input type="password" id="login-password" name="password" placeholder="••••••••" required>
                </div>
                <p class="form-error hidden" id="login-error"></p>
                <button type="submit" class="btn btn--primary btn--full">Se connecter</button>
                <p class="form-switch">Pas encore de compte ? <button type="button" class="link-btn" id="switch-to-register">S'inscrire</button></p>
            </form>
        </div>
    </div>

    <!-- ===== MODAL INSCRIPTION ===== -->
    <div class="modal hidden" id="modal-register" role="dialog" aria-modal="true" aria-labelledby="modal-register-title">
        <div class="modal__overlay" id="overlay-register"></div>
        <div class="modal__box">
            <button class="modal__close" id="close-register" aria-label="Fermer">✕</button>
            <h2 id="modal-register-title">Créer un compte</h2>
            <form id="form-register" novalidate>
                <div class="form-group">
                    <label for="reg-username">Nom d'utilisateur</label>
                    <input type="text" id="reg-username" name="username" placeholder="pseudo (3-50 caractères)" required>
                </div>
                <div class="form-group">
                    <label for="reg-email">Adresse e-mail</label>
                    <input type="email" id="reg-email" name="email" placeholder="etudiant@univ-smb.fr" required>
                </div>
                <div class="form-group">
                    <label for="reg-password">Mot de passe</label>
                    <input type="password" id="reg-password" name="password" placeholder="8 caractères minimum" required>
                </div>
                <div class="form-group">
                    <label for="reg-niveau">Votre niveau d'études</label>
                    <select id="reg-niveau" name="niveau" required>
                        <option value="">-- Choisir --</option>
                        <option value="1">L1 — Licence 1ère année</option>
                        <option value="2">L2 — Licence 2ème année</option>
                        <option value="3">L3 — Licence 3ème année</option>
                        <option value="4">M1 — Master 1ère année</option>
                        <option value="5">M2 — Master 2ème année</option>
                    </select>
                </div>
                <p class="form-error hidden" id="register-error"></p>
                <p class="form-success hidden" id="register-success"></p>
                <button type="submit" class="btn btn--primary btn--full">Créer mon compte</button>
                <p class="form-switch">Déjà un compte ? <button type="button" class="link-btn" id="switch-to-login">Se connecter</button></p>
            </form>
        </div>
    </div>

<?php
$page_script = 'assets/js/script.js';
require_once __DIR__ . '/php/template/footer.php';
?>
