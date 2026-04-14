<?php
session_start();
header('Content-Type: application/json');
include '../../db_connect.php';
include 'crud_tags.php';

if (!isset($_SESSION['id'])) { echo json_encode(['success'=>false, 'message'=>'Non autorisé.']); exit; }

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $nom     = trim($_POST['nom']     ?? '');
    $couleur = trim($_POST['couleur'] ?? '#5c6672');
    if (strlen($nom) < 2) { echo json_encode(['success'=>false, 'message'=>'Nom trop court.']); exit; }
    createTags($conn, $nom, $couleur);
    echo json_encode(['success'=>true, 'message'=>'Tag créé.']);

} elseif ($action === 'delete') {
    $id = (int)($_POST['tag_id'] ?? 0);
    deleteTags($conn, $id);
    echo json_encode(['success'=>true, 'message'=>'Tag supprimé.']);

} else {
    echo json_encode(['success'=>false, 'message'=>'Action inconnue.']);
}
?>
