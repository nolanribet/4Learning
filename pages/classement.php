<?php
// =============================================================
//  pages/classement.php — Page classement saisonnier
// =============================================================
$page_title  = 'Classement';
$active_page = 'classement';
$base_path   = '../';
require_once __DIR__ . '/../php/template/header.php';
?>

    <main>
        <section class="page-header">
            <div class="container">
                <h1>Classement</h1>
                <p id="saison-label">Saison en cours — chargement...</p>
            </div>
        </section>

        <!-- ===== PODIUM TOP 3 ===== -->
        <section class="podium-section">
            <div class="container">
                <div class="podium" id="podium"></div>
            </div>
        </section>

        <!-- ===== TABLEAU COMPLET ===== -->
        <section class="leaderboard-section">
            <div class="container">
                <h2>Tableau général</h2>
                <div class="leaderboard-table-wrapper">
                    <table class="leaderboard-table" id="leaderboard-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Étudiant</th>
                                <th>Niveau</th>
                                <th>Points</th>
                                <th>Fiabilité</th>
                                <th>Badges</th>
                            </tr>
                        </thead>
                        <tbody id="leaderboard-body">
                            <tr>
                                <td colspan="6" class="loading-placeholder">Chargement du classement...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ===== BADGES DISPONIBLES ===== -->
        <section class="badges-section">
            <div class="container">
                <h2>Badges de la saison</h2>
                <div class="badges-grid" id="badges-grid"></div>
            </div>
        </section>
    </main>

<?php
$page_script = '../assets/js/classement.js';
require_once __DIR__ . '/../php/template/footer.php';
?>
