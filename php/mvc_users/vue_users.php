<?php

function form_inscription() {
    ?>
    <form action="admin_users.php" method="POST">
        <div class="form_username">
            <label for="username">Pseudo : </label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form_mail">
            <label for="mail">Email : </label>
            <input type="email" id="mail" name="mail" required>
        </div>
        <div class="form_niveau">
            <label for="niveau_etude">Vous êtes : </label>
            <select id="niveau_etude" name="niveau_etude" required>
                <option value="" disabled selected>-- Choisissez votre niveau --</option>
                <option value="1">Étudiant - L1</option>
                <option value="2">Étudiant - L2</option>
                <option value="3">Étudiant - L3</option>
                <option value="4">Étudiant - M1</option>
                <option value="5">Étudiant - M2</option>
                <option value="0">Professeur</option>
            </select>
        </div>
        <div class="form_password">
            <label for="password">Mot de passe : </label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form_confirm_password">
            <label for="confirm_password">Confirmer le mot de passe : </label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" name="btn_inscription">S'inscrire</button>
    </form>
    <?php
}

function form_modification_profil($username_actuel, $mail_actuel, $niveau_actuel) {
    ?>
    <form action="admin_users.php" method="POST">
        <div class="form_username">
            <label for="username">Pseudo : </label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username_actuel); ?>" required>
        </div>
        <div class="form_mail">
            <label for="mail">Email :</label>
            <input type="email" id="mail" name="mail" value="<?php echo htmlspecialchars($mail_actuel); ?>" required>
        </div>
        <div class="form_niveau">
            <label for="niveau_etude">Niveau d'étude :</label>
            <select id="niveau_etude" name="niveau_etude" required>
                <option value="1" <?php if($niveau_actuel == 1) echo 'selected'; ?>>Étudiant - L1</option>
                <option value="2" <?php if($niveau_actuel == 2) echo 'selected'; ?>>Étudiant - L2</option>
                <option value="3" <?php if($niveau_actuel == 3) echo 'selected'; ?>>Étudiant - L3</option>
                <option value="4" <?php if($niveau_actuel == 4) echo 'selected'; ?>>Étudiant - M1</option>
                <option value="5" <?php if($niveau_actuel == 5) echo 'selected'; ?>>Étudiant - M2</option>
                <option value="0" <?php if($niveau_actuel == 0) echo 'selected'; ?>>Professeur</option>
            </select>
        </div>
        <button type="submit" name="btn_modif_profil">Modifier les informations</button>
    </form>
    <?php
}

function form_modification_mdp() {
    ?>
    <form action="admin_users.php" method="POST">
        <div class="form_ancien_mdp">
            <label for="ancien_mdp">Mot de passe actuel :</label>
            <input type="password" id="ancien_mdp" name="ancien_mdp" required>
        </div>
        <div class="form_password">
            <label for="password">Nouveau mot de passe :</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form_confirm_password">
            <label for="confirm_password">Confirmer le nouveau mot de passe :</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" name="btn_modif_mdp">Modifier le mot de passe</button>
    </form>
    <?php
}

function form_connexion() {
    ?>
    <form action="admin_users.php" method="POST">
        <div class="form_username">
            <label for="username">Pseudo :</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form_password">
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" name="btn_connexion">Se connecter</button>
    </form>
    <?php
}

function form_deconnexion() {
    ?>
    <form action="admin_users.php" method="POST">
        <button type="submit" name="btn_deconnexion">Se déconnecter</button>
    </form>
    <?php
}

// Appelé directement (AJAX profil.js) → retourne les données du user en JSON
if (realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    header('Content-Type: application/json');
    include '../../db_connect.php';

    $user_id = (int)($_GET['user_id'] ?? 0);
    if ($user_id < 1) { echo json_encode(['success'=>false, 'message'=>'user_id manquant']); exit; }

    # Compteurs
    $nb_q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as n FROM questions WHERE id_auteur='$user_id'"))['n'];
    $nb_r = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as n FROM reponses WHERE id_auteur='$user_id'"))['n'];

    # Badges
    $res_b  = mysqli_query($conn, "SELECT b.icone, b.nom, b.description FROM user_badges ub JOIN badges b ON b.id = ub.id_badge WHERE ub.id_user='$user_id'");
    $badges = mysqli_fetch_all($res_b, MYSQLI_ASSOC);

    # Dernières questions
    $res_q = mysqli_query($conn,
        "SELECT q.id, q.titre, q.statut, q.created_at, t.name as matiere, t.color as matiere_couleur, COUNT(r.id) as nb_reponses
         FROM questions q
         LEFT JOIN tags_matiere t ON t.id = q.tag_matiere
         LEFT JOIN reponses r ON r.id_question = q.id
         WHERE q.id_auteur='$user_id'
         GROUP BY q.id
         ORDER BY q.created_at DESC
         LIMIT 10"
    );
    $questions = mysqli_fetch_all($res_q, MYSQLI_ASSOC);
    foreach ($questions as &$q) {
        $q['date'] = date('d M Y', strtotime($q['created_at']));
        unset($q['created_at']);
    }

    # Dernières réponses
    $res_r = mysqli_query($conn,
        "SELECT r.id, r.contenu, r.note_moy, r.nb_votes, r.created_at, q.titre as question_titre
         FROM reponses r
         JOIN questions q ON q.id = r.id_question
         WHERE r.id_auteur='$user_id'
         ORDER BY r.created_at DESC
         LIMIT 10"
    );
    $reponses = mysqli_fetch_all($res_r, MYSQLI_ASSOC);
    foreach ($reponses as &$r) {
        $r['date']     = date('d M Y', strtotime($r['created_at']));
        $r['note_moy'] = (float)$r['note_moy'];
        $r['contenu']  = $r['contenu']; // déjà un string
        unset($r['created_at']);
    }

    echo json_encode([
        'success'      => true,
        'nb_questions' => (int)$nb_q,
        'nb_answers'   => (int)$nb_r,
        'badges'       => $badges,
        'questions'    => $questions,
        'answers'      => $reponses,
    ]);
}
?>
