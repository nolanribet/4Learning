<?php
// =============================================================
//  header.php — En-tête commun inclus dans chaque page
//  Variables attendues avant l'include :
//    $page_title  (string) — titre de la page
//    $active_page (string) — 'questions' | 'classement' | 'profil'
//    $base_path   (string) — chemin vers la racine ex: '../' ou ''
// =============================================================
$page_title  = $page_title  ?? '4Learning';
$active_page = $active_page ?? '';
$base        = $base_path   ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> — 4Learning</title>
    <link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
    <link rel="icon" type="image/png" href="<?= $base ?>assets/img/Logo 4Learn .png">
</head>
<body>

<header class="navbar">
    <a href="<?= $base ?>index.php" class="navbar__brand">
        <img src="<?= $base ?>assets/img/Logo Horizontale.png" alt="4Learning" class="navbar__logo">
    </a>
    <nav class="navbar__links">
        <a href="<?= $base ?>pages/questions.php"  class="nav-link <?= $active_page === 'questions'  ? 'active' : '' ?>">Questions</a>
        <a href="<?= $base ?>pages/classement.php" class="nav-link <?= $active_page === 'classement' ? 'active' : '' ?>">Classement</a>
        <a href="<?= $base ?>pages/profil.php"     class="nav-link <?= $active_page === 'profil'     ? 'active' : '' ?>">Profil</a>
    </nav>
    <div class="navbar__auth" id="navbar-auth">
        <button class="btn btn--outline" id="btn-login">Connexion</button>
        <button class="btn btn--primary" id="btn-register">Inscription</button>
    </div>
    <div class="navbar__user hidden" id="navbar-user">
        <span class="user-points" id="nav-points">0 pts</span>
        <a href="<?= $base ?>pages/profil.php" class="nav-username" id="nav-username">Utilisateur</a>
        <button class="btn btn--outline btn--sm" id="btn-logout">Déconnexion</button>
    </div>
</header>
