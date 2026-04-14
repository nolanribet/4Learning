<?php
// Classement général (appelé par classement.js)
header('Content-Type: application/json');
include '../../db_connect.php';

$sql_users = "SELECT u.id, u.username, u.niveau_etude as niveau, u.points_total as points, u.score_fiabilite as fiabilite
              FROM users u
              ORDER BY u.points_total DESC
              LIMIT 20";

$res   = mysqli_query($conn, $sql_users);
$users = mysqli_fetch_all($res, MYSQLI_ASSOC);

$rang = 1;
foreach ($users as &$u) {
    $u['rang'] = $rang++;
    $id = $u['id'];
    $res_b     = mysqli_query($conn, "SELECT b.icone FROM user_badges ub JOIN badges b ON b.id = ub.id_badge WHERE ub.id_user='$id'");
    $u['badges'] = array_column(mysqli_fetch_all($res_b, MYSQLI_ASSOC), 'icone');
    unset($u['id']);
}

$res_badges = mysqli_query($conn, "SELECT icone, nom, description FROM badges");
$badges     = mysqli_fetch_all($res_badges, MYSQLI_ASSOC);

echo json_encode(['success'=>true, 'saison'=>'Saison 1 — Printemps 2026', 'users'=>$users, 'badges'=>$badges]);
?>
