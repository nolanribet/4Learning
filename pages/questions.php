<?php
// =============================================================
//  pages/questions.php — Page liste des questions
// =============================================================
$page_title  = 'Questions';
$active_page = 'questions';
$base_path   = '../';
require_once __DIR__ . '/../php/template/header.php';
?>

    <main>
        <section class="page-header">
            <div class="container">
                <h1>Questions de la communauté</h1>
                <p>Explore les questions ou pose la tienne.</p>
                <button class="btn btn--primary" id="btn-new-question">+ Nouvelle question</button>
            </div>
        </section>

        <!-- ===== FILTRES ===== -->
        <section class="filters">
            <div class="container">
                <div class="filters__bar">
                    <input type="text" id="search-input" class="search-input" placeholder="Rechercher une question...">
                    <select id="filter-matiere" class="filter-select">
                        <option value="">Toutes les matières</option>
                    </select>
                    <select id="filter-statut" class="filter-select">
                        <option value="">Tous les statuts</option>
                        <option value="ouverte">Ouvertes</option>
                        <option value="resolue">Résolues</option>
                    </select>
                    <select id="filter-tri" class="filter-select">
                        <option value="recent">Plus récentes</option>
                        <option value="populaire">Plus populaires</option>
                    </select>
                </div>
            </div>
        </section>

        <!-- ===== LISTE DES QUESTIONS ===== -->
        <section class="questions-section">
            <div class="container">
                <div class="questions-list" id="questions-list">
                    <div class="loading-placeholder">Chargement des questions...</div>
                </div>
            </div>
        </section>
    </main>

    <!-- ===== MODAL NOUVELLE QUESTION ===== -->
    <div class="modal hidden" id="modal-question" role="dialog" aria-modal="true">
        <div class="modal__overlay" id="overlay-question"></div>
        <div class="modal__box modal__box--wide">
            <button class="modal__close" id="close-question" aria-label="Fermer">✕</button>
            <h2>Poser une question</h2>
            <form id="form-question" novalidate>
                <div class="form-group">
                    <label for="q-titre">Titre de la question</label>
                    <input type="text" id="q-titre" name="titre" placeholder="Résumez votre problème en une phrase..." required>
                </div>
                <div class="form-group">
                    <label for="q-matiere">Matière</label>
                    <select id="q-matiere" name="tag_id" required>
                        <option value="">-- Choisir une matière --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="q-contenu">Description détaillée</label>
                    <textarea id="q-contenu" name="contenu" rows="6" placeholder="Décrivez votre question en détail..." required></textarea>
                </div>
                <div class="form-group">
                    <label for="q-niveau">Niveau minimum pour répondre</label>
                    <select id="q-niveau" name="niveau_requis">
                        <option value="1">L1</option>
                        <option value="2">L2</option>
                        <option value="3">L3</option>
                        <option value="4">M1</option>
                        <option value="5">M2</option>
                    </select>
                </div>
                <p class="form-error hidden" id="question-error"></p>
                <p class="form-success hidden" id="question-success"></p>
                <button type="submit" class="btn btn--primary btn--full">Publier la question</button>
            </form>
        </div>
    </div>

    <!-- ===== MODAL DÉTAIL QUESTION ===== -->
    <div class="modal hidden" id="modal-detail" role="dialog" aria-modal="true">
        <div class="modal__overlay" id="overlay-detail"></div>
        <div class="modal__box modal__box--wide">
            <button class="modal__close" id="close-detail" aria-label="Fermer">✕</button>
            <div id="detail-content"></div>
        </div>
    </div>

<?php
$page_script = '../assets/js/questions.js';
require_once __DIR__ . '/../php/template/footer.php';
?>
