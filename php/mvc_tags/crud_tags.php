<?php

function createTags($conn, $name, $color){
    $sql = "INSERT INTO `tags_matiere`(`name`,`color`) VALUE('$name','$color')";
    $req = mysqli_query($conn, $sql);
    return $req;
}

function updateTags($conn, $id, $name, $color){
    $sql = "UPDATE `tags_matiere` SET `id`='$id', `name`='$name', `couleur`='$id' WHERE `id`='$id'";
    $req = mysqli_query($conn, $sql);
    return $req;
}

function readTags($conn, $id){
    $sql = "SELECT * from `tags_matiere` WHERE `id`='$id'";
    $req = mysqli_query($conn, $sql);
    return $req;
}

function deleteTags($conn, $id){
    $sql = "DELETE FROM `tags_matiere` WHERE `id`='$id'";
    $req = mysqli_query($conn, $sql);
    return $req;
}

function listTags($conn){
    $sql = "SELECT * FROM `tags_matiere`";
    $req = mysqli_query($conn, $sql);
    return $req;
}

?>
