<?php
// =============================================================
//  pages/profil.php — Page profil utilisateur
// =============================================================
$page_title  = 'Mon Profil';
$active_page = 'profil';
$base_path   = '../';
require_once __DIR__ . '/../php/template/header.php';
?>

    <main>

        <!-- Message si non connecté -->
        <section class="not-connected hidden" id="not-connected">
            <div class="container centered">
                <img src="../assets/img/Logo 4Learn .png" alt="4Learning" style="width:80px;opacity:.5">
                <h2>Vous n'êtes pas connecté</h2>
                <p>Connectez-vous pour accéder à votre profil.</p>
                <button class="btn btn--primary" id="btn-login-redirect">Se connecter</button>
            </div>
        </section>

        <!-- Contenu profil (si connecté) -->
        <div id="profil-content" class="hidden">

            <!-- Carte profil -->
            <section class="profil-header">
                <div class="container">
                    <div class="profil-card">
                        <div class="profil-avatar" id="profil-avatar">
                            <span id="profil-initiale">?</span>
                        </div>
                        <div class="profil-info">
                            <h1 id="profil-username">Utilisateur</h1>
                            <span class="niveau-badge" id="profil-niveau">L1</span>
                            <div class="profil-stats">
                                <div class="profil-stat">
                                    <strong id="profil-points">0</strong>
                                    <span>Points</span>
                                </div>
                                <div class="profil-stat">
                                    <strong id="profil-questions-count">0</strong>
                                    <span>Questions</span>
                                </div>
                                <div class="profil-stat">
                                    <strong id="profil-answers-count">0</strong>
                                    <span>Réponses</span>
                                </div>
                            </div>
                        </div>
                        <div class="profil-fiabilite">
                            <span class="fiabilite-label">Fiabilité</span>
                            <div class="fiabilite-gauge">
                                <div class="fiabilite-bar" id="profil-fiabilite-bar" style="width:0%"></div>
                            </div>
                            <span class="fiabilite-value" id="profil-fiabilite-val">0%</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Badges obtenus -->
            <section class="profil-badges">
                <div class="container">
                    <h2>Mes badges</h2>
                    <div class="badges-grid" id="profil-badges-grid">
                        <p class="empty-state">Aucun badge pour l'instant. Participez pour en gagner !</p>
                    </div>
                </div>
            </section>

            <!-- Activité récente -->
            <section class="profil-activity">
                <div class="container">
                    <div class="activity-tabs">
                        <button class="tab-btn active" data-tab="mes-questions">Mes questions</button>
                        <button class="tab-btn" data-tab="mes-reponses">Mes réponses</button>
                    </div>
                    <div class="tab-content" id="mes-questions">
                        <div class="questions-list" id="profil-questions-list">
                            <p class="empty-state">Aucune question posée pour l'instant.</p>
                        </div>
                    </div>
                    <div class="tab-content hidden" id="mes-reponses">
                        <div class="answers-list" id="profil-answers-list">
                            <p class="empty-state">Aucune réponse donnée pour l'instant.</p>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </main>

<?php
$page_script = '../assets/js/profil.js';
require_once __DIR__ . '/../php/template/footer.php';
?>
