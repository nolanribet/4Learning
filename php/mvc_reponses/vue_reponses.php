<?php
header('Content-Type: application/json');
include '../../db_connect.php';

$id_question = (int)($_GET['question_id'] ?? 0);
if ($id_question < 1) { echo json_encode(['success'=>false, 'message'=>'question_id manquant']); exit; }

$sql = "SELECT r.id, r.contenu, r.note_moy, r.nb_votes, r.created_at, u.username as auteur
        FROM reponses r
        JOIN users u ON u.id = r.id_auteur
        WHERE r.id_question='$id_question'
        ORDER BY r.note_moy DESC, r.created_at ASC";

$res      = mysqli_query($conn, $sql);
$reponses = mysqli_fetch_all($res, MYSQLI_ASSOC);

foreach ($reponses as &$r) {
    $r['date']     = date('d M Y', strtotime($r['created_at']));
    $r['note_moy'] = (float)$r['note_moy'];
    unset($r['created_at']);
}

echo json_encode(['success'=>true, 'answers'=>$reponses]);
?>
