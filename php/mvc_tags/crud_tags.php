<?php

function createTags($conn, $name, $color) {
    $name  = mysqli_real_escape_string($conn, $name);
    $color = mysqli_real_escape_string($conn, $color);
    return mysqli_query($conn, "INSERT INTO tags_matiere (name, color) VALUES ('$name', '$color')");
}

function updateTags($conn, $name, $color, $id) {
    $name  = mysqli_real_escape_string($conn, $name);
    $color = mysqli_real_escape_string($conn, $color);
    return mysqli_query($conn, "UPDATE tags_matiere SET name='$name', color='$color' WHERE id='$id'");
}

function readTags($conn, $id) {
    $res = mysqli_query($conn, "SELECT * FROM tags_matiere WHERE id='$id'");
    return mysqli_fetch_assoc($res);
}

function deleteTags($conn, $id) {
    return mysqli_query($conn, "DELETE FROM tags_matiere WHERE id='$id'");
}

function listTags($conn) {
    return mysqli_query($conn, "SELECT * FROM tags_matiere ORDER BY name");
}

// Appelé directement (AJAX questions.js) → liste des tags en JSON
if (realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    header('Content-Type: application/json');
    include '../db_connect.php';
    $res  = listTags($conn);
    $tags = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $tags[] = ['id' => $row['id'], 'nom' => $row['name'], 'couleur' => $row['color']];
    }
    echo json_encode(['success'=>true, 'tags'=>$tags]);
}
?>
