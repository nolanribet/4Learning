<?php

function create_reponse($conn, $id_question, $id_auteur, $contenu) {
    $contenu = mysqli_real_escape_string($conn, $contenu);
    return mysqli_query($conn, "INSERT INTO reponses (id_question, id_auteur, contenu) VALUES ('$id_question', '$id_auteur', '$contenu')");
}

function delete_reponse($conn, $id) {
    return mysqli_query($conn, "DELETE FROM reponses WHERE id='$id'");
}

function voter_reponse($conn, $id_reponse, $id_voteur, $note) {
    # On vérifie que le user n'a pas déjà voté
    $check = mysqli_query($conn, "SELECT id FROM votes_reponses WHERE id_reponse='$id_reponse' AND id_voteur='$id_voteur'");
    if (mysqli_num_rows($check) > 0) return false;

    mysqli_query($conn, "INSERT INTO votes_reponses (id_reponse, id_voteur, note) VALUES ('$id_reponse', '$id_voteur', '$note')");

    # Recalcul de la note moyenne
    $avg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(note) as moy FROM votes_reponses WHERE id_reponse='$id_reponse'"));
    $note_moy = round((float)$avg['moy'], 2);
    mysqli_query($conn, "UPDATE reponses SET note_moy=$note_moy, nb_votes=nb_votes+1 WHERE id='$id_reponse'");

    # Mise à jour du score de fiabilité de l'auteur (moyenne de toutes ses réponses notées)
    $rep      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_auteur FROM reponses WHERE id='$id_reponse'"));
    $id_auteur = $rep['id_auteur'];
    $avg_glob = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(note_moy) as moy FROM reponses WHERE id_auteur='$id_auteur' AND nb_votes > 0"));
    $fiabilite = min(100, max(0, (int)round((float)$avg_glob['moy'] * 20)));
    mysqli_query($conn, "UPDATE users SET score_fiabilite=$fiabilite WHERE id='$id_auteur'");

    return $note_moy;
}

// Appelé directement (AJAX questions.js) → create ou rate, retourne JSON
if (realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    session_start();
    header('Content-Type: application/json');
    include '../db_connect.php';

    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        if (!isset($_SESSION['id'])) { echo json_encode(['success'=>false, 'message'=>'Connectez-vous.']); exit; }

        $id_question = (int)($_POST['question_id'] ?? 0);
        $contenu     = trim($_POST['contenu'] ?? '');
        if ($id_question < 1 || strlen($contenu) < 10) { echo json_encode(['success'=>false, 'message'=>'Données invalides.']); exit; }

        # Vérif question ouverte et niveau suffisant
        $q = mysqli_fetch_assoc(mysqli_query($conn, "SELECT niveau_requis, statut FROM questions WHERE id='$id_question'"));
        if (!$q)                           { echo json_encode(['success'=>false, 'message'=>'Question introuvable.']); exit; }
        if ($q['statut'] !== 'ouverte')    { echo json_encode(['success'=>false, 'message'=>'Question fermée.']); exit; }

        $u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT niveau_etude FROM users WHERE id='{$_SESSION['id']}'"));
        if ((int)$u['niveau_etude'] < (int)$q['niveau_requis']) {
            echo json_encode(['success'=>false, 'message'=>'Niveau insuffisant (requis : ' . $q['niveau_requis'] . ').']);
            exit;
        }

        $ok = create_reponse($conn, $id_question, $_SESSION['id'], $contenu);
        if ($ok) {
            mysqli_query($conn, "UPDATE users SET points_total = points_total + 5 WHERE id='{$_SESSION['id']}'");
            echo json_encode(['success'=>true, 'message'=>'Réponse publiée !']);
        } else {
            echo json_encode(['success'=>false, 'message'=>'Erreur : ' . mysqli_error($conn)]);
        }

    } elseif ($action === 'rate') {
        if (!isset($_SESSION['id'])) { echo json_encode(['success'=>false, 'message'=>'Connectez-vous.']); exit; }

        $id_reponse = (int)($_POST['answer_id'] ?? 0);
        $note       = (int)($_POST['note']      ?? 0);
        if ($id_reponse < 1 || $note < 1 || $note > 5) { echo json_encode(['success'=>false, 'message'=>'Note invalide.']); exit; }

        # On ne peut pas noter sa propre réponse
        $rep = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_auteur FROM reponses WHERE id='$id_reponse'"));
        if (!$rep)                                    { echo json_encode(['success'=>false, 'message'=>'Réponse introuvable.']); exit; }
        if ($rep['id_auteur'] == $_SESSION['id'])     { echo json_encode(['success'=>false, 'message'=>'Impossible de noter sa propre réponse.']); exit; }

        $note_moy = voter_reponse($conn, $id_reponse, $_SESSION['id'], $note);
        if ($note_moy === false) {
            echo json_encode(['success'=>false, 'message'=>'Vous avez déjà noté cette réponse.']);
        } else {
            echo json_encode(['success'=>true, 'message'=>'Note enregistrée.', 'note_moy'=>$note_moy]);
        }

    } else {
        echo json_encode(['success'=>false, 'message'=>'Action inconnue.']);
    }
}
?>
