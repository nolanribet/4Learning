<?php
function createTags($conn, $name, $color){
    $sql = "INSERT INTO `tags_matiere` (`name`, `couleur`) VALUES ('$name', '$color')";
    $req = mysqli_query($conn, $sql);
    return $req;
}

function updateTags($conn, $name, $color, $id){
    $sql = "UPDATE `tags_matiere` SET `name`='$name', `couleur`='$color' WHERE `id`='$id'";
    $req = mysqli_query($conn, $sql);
    return $req;
}

function readTags($conn, $id){
    $sql = "SELECT * FROM `tags_matiere` WHERE `id`='$id'";
    $req = mysqli_query($conn, $sql);
    return $req;
}

function deleteTags($conn, $id){
    $sql = "DELETE FROM `tags_matiere` WHERE `id`='$id'";
    $req = mysqli_query($conn, $sql);
    return $req;
}

function listTags($conn){
    $sql = "SELECT * FROM `tags_matiere` ORDER BY name ASC";
    $req = mysqli_query($conn, $sql);
    return $req;
}
?>