<?php

function create_question($conn, $sujet, $content, $matiere, $id_auteur, $niveau){

    $sql = "INSERT INTO `questions`(`titre`, `content`, `tag_matiere`, ìd_auteur`, `niveau_requis`) value($sujet, $content, $matiere, $id_auteur, $niveau)";
    $ret = mysqli_query($conn, $sql);
    return $ret;
}

function readQuestion($conn, $id){

    $sql = "SELECT * from questions WHERE ̀id`='$id'";
    if($ret=mysqli_query($conn, $sql)){
        $ret=$mysqli_fetch_assoc($ret)
    }
    return $ret;

}

function updateQuestion($conn, $id, $nouveau_titre, $nouveau_contenu, $nouvelle_matiere){
    $sql = "UPDATE questions SET `titre`='$nouveau_titre', `content`='$nouveau_contenu', `tag_matiere`='$nouvelle_matiere' WHERE `id`=''$id";
    $ret = mysqli_query($conn, $sql);
    return $ret;
}

function deleteQuestion($conn, $id){
    $sql = "DELETE from questions WHERE `id`='$id'";
    $ret = mysqli_query($conn, $sql);
    return $ret;
}

?>