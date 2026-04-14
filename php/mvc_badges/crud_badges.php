<?php

function attribuer_badges($conn, $id_user) {
    $nb_rep  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as n FROM reponses WHERE id_auteur='$id_user'"))['n'];
    $nb_q    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as n FROM questions WHERE id_auteur='$id_user'"))['n'];
    $avg_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(note_moy) as moy FROM reponses WHERE id_auteur='$id_user' AND nb_votes > 0"));
    $avg     = (float)($avg_row['moy'] ?? 0);
    $user    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT score_fiabilite FROM users WHERE id='$id_user'"));

    # Badges déjà obtenus
    $res_existing = mysqli_query($conn, "SELECT id_badge FROM user_badges WHERE id_user='$id_user'");
    $existants    = array_column(mysqli_fetch_all($res_existing, MYSQLI_ASSOC), 'id_badge');

    $nouveaux = [];
    if ($nb_rep  >= 1    && !in_array(1, $existants)) { mysqli_query($conn, "INSERT INTO user_badges (id_user, id_badge) VALUES ('$id_user', 1)"); $nouveaux[] = 'Première réponse'; }
    if ($avg     >= 4.5  && !in_array(2, $existants)) { mysqli_query($conn, "INSERT INTO user_badges (id_user, id_badge) VALUES ('$id_user', 2)"); $nouveaux[] = 'Expert'; }
    if ($user && $user['score_fiabilite'] >= 100 && !in_array(4, $existants)) { mysqli_query($conn, "INSERT INTO user_badges (id_user, id_badge) VALUES ('$id_user', 4)"); $nouveaux[] = 'Fiable'; }
    if ($nb_q    >= 10   && !in_array(5, $existants)) { mysqli_query($conn, "INSERT INTO user_badges (id_user, id_badge) VALUES ('$id_user', 5)"); $nouveaux[] = 'Curieux'; }

    return $nouveaux;
}

// Appelé directement (AJAX profil.js) → vérifie et attribue les badges, retourne JSON
if (realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    header('Content-Type: application/json');
    include '../../db_connect.php';

    $id_user = (int)($_GET['user_id'] ?? 0);
    if ($id_user < 1) { echo json_encode(['success'=>false, 'message'=>'user_id manquant']); exit; }

    $nouveaux = attribuer_badges($conn, $id_user);
    echo json_encode(['success'=>true, 'badges_awarded'=>$nouveaux]);
}
?>
