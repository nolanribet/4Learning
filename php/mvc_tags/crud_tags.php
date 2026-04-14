<?php

function createTags($conn, $name, $color){
    $sql = "INSERT INTO `tags_matiere`(`name`,`color`) VALUES('$name','$color')";
    $req = mysqli_query($conn, $sql);
    return $req;
}

function updateTags($conn, $name, $color, $id){
    $sql = "UPDATE `tags_matiere` SET `name`='$name', `couleur`='$color' WHERE `id`='$id'";
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
