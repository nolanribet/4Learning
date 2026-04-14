<?php
// Attribue le badge "Top 3" aux 3 premiers du classement
session_start();
header('Content-Type: application/json');
include '../db_connect.php';

if (!isset($_SESSION['id'])) { echo json_encode(['success'=>false, 'message'=>'Non autorisé.']); exit; }

$res  = mysqli_query($conn, "SELECT id FROM users ORDER BY points_total DESC LIMIT 3");
$top3 = mysqli_fetch_all($res, MYSQLI_ASSOC);

foreach ($top3 as $u) {
    $id_user = $u['id'];
    $check   = mysqli_query($conn, "SELECT id FROM user_badges WHERE id_user='$id_user' AND id_badge=3");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO user_badges (id_user, id_badge) VALUES ('$id_user', 3)");
    }
}

echo json_encode(['success'=>true, 'message'=>'Badge Top 3 attribué.']);
?>
