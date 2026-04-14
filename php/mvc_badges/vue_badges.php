<?php
header('Content-Type: application/json');
include '../../db_connect.php';

$res    = mysqli_query($conn, "SELECT id, nom, description, icone FROM badges");
$badges = mysqli_fetch_all($res, MYSQLI_ASSOC);

echo json_encode(['success'=>true, 'badges'=>$badges]);
?>
