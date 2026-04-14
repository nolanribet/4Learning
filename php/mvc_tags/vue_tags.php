<?php
header('Content-Type: application/json');
include '../../db_connect.php';

$id = (int)($_GET['tag_id'] ?? 0);
if ($id < 1) { echo json_encode(['success'=>false, 'message'=>'tag_id manquant']); exit; }

$tag = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id, name as nom, color as couleur FROM tags_matiere WHERE id='$id'"));
if (!$tag) { echo json_encode(['success'=>false, 'message'=>'Tag introuvable']); exit; }

$res       = mysqli_query($conn, "SELECT q.id, q.titre, q.statut, COUNT(r.id) as nb_reponses FROM questions q LEFT JOIN reponses r ON r.id_question = q.id WHERE q.tag_matiere='$id' GROUP BY q.id ORDER BY q.created_at DESC LIMIT 20");
$questions = mysqli_fetch_all($res, MYSQLI_ASSOC);

echo json_encode(['success'=>true, 'tag'=>$tag, 'questions'=>$questions]);
?>
