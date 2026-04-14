<?php

function create_question($conn, $sujet, $content, $matiere, $id_auteur, $niveau) {
    $sujet    = mysqli_real_escape_string($conn, $sujet);
    $content  = mysqli_real_escape_string($conn, $content);
    $sql = "INSERT INTO questions (titre, content, tag_matiere, id_auteur, niveau_requis)
            VALUES ('$sujet', '$content', '$matiere', '$id_auteur', '$niveau')";
    $ret = mysqli_query($conn, $sql);
    return $ret;
}

function readQuestion($conn, $id) {
    $sql = "SELECT * FROM questions WHERE id='$id'";
    $ret = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($ret);
}

function updateQuestion($conn, $id, $nouveau_titre, $nouveau_contenu, $nouvelle_matiere) {
    $nouveau_titre   = mysqli_real_escape_string($conn, $nouveau_titre);
    $nouveau_contenu = mysqli_real_escape_string($conn, $nouveau_contenu);
    $sql = "UPDATE questions SET titre='$nouveau_titre', content='$nouveau_contenu', tag_matiere='$nouvelle_matiere' WHERE id='$id'";
    $ret = mysqli_query($conn, $sql);
    return $ret;
}

function deleteQuestion($conn, $id) {
    return mysqli_query($conn, "DELETE FROM questions WHERE id='$id'");
}

// Appelé directement (AJAX questions.js) → crée une question, retourne JSON
if (realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    session_start();
    header('Content-Type: application/json');
    include '../db_connect.php';

    if (!isset($_SESSION['id'])) { echo json_encode(['success'=>false, 'message'=>'Connectez-vous.']); exit; }
    if (($_POST['action'] ?? '') !== 'create') { echo json_encode(['success'=>false, 'message'=>'Action inconnue.']); exit; }

    $titre   = trim($_POST['titre']   ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $tag_id  = (int)($_POST['tag_id'] ?? 0);
    $niveau  = (int)($_POST['niveau_requis'] ?? 1);

    if (strlen($titre) < 10)   { echo json_encode(['success'=>false, 'message'=>'Titre trop court (10 min).']); exit; }
    if (strlen($contenu) < 20) { echo json_encode(['success'=>false, 'message'=>'Description trop courte (20 min).']); exit; }
    if ($tag_id < 1)           { echo json_encode(['success'=>false, 'message'=>'Sélectionnez une matière.']); exit; }

    $ok = create_question($conn, $titre, $contenu, $tag_id, $_SESSION['id'], $niveau);
    if ($ok) {
        $id = mysqli_insert_id($conn);
        mysqli_query($conn, "UPDATE users SET points_total = points_total + 2 WHERE id='{$_SESSION['id']}'");
        echo json_encode(['success'=>true, 'message'=>'Question publiée !', 'question_id'=>$id]);
    } else {
        echo json_encode(['success'=>false, 'message'=>'Erreur : ' . mysqli_error($conn)]);
    }
}
?>
