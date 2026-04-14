<?php
include "../db_connect.php";

function creer_user($conn, $username, $password_hash, $mail, $niveau_etude, $avatar) {
    $sql = "INSERT into users (username, email, password, niveau_etude, points_total, score_fiabilite, avatar) values ('$username', '$mail', '$password_hash', '$niveau_etude', 0, 0, '$avatar')";
    $res=mysqli_query($conn, $sql) ;
    return $res ; # Booléen qui retourne si la requete est réussi
}

function modifier_user($conn, $id, $username, $mail,  $niveau_etude, $avatar) {
    $sql="UPDATE users SET username='$username', email='$mail', niveau_etude=$niveau_etude, avatar='$avatar'  WHERE id = '$id'" ;  
	$res=mysqli_query($conn, $sql) ; 
	return $res ; 
}

function modifier_mdp_user($conn, $id, $new_password_hash) {
    $sql="UPDATE users SET password='$new_password_hash'  WHERE id = '$id'" ;  
	$res=mysqli_query($conn, $sql) ; 
	return $res ;
}

function supprimer_user($conn, $id) {
    $sql="DELETE FROM users WHERE id = '$id'" ;  
	$res=mysqli_query($conn, $sql) ; 
	return $res ; 
}   

function recup_user_id($conn, $id) {
    $sql="SELECT * FROM users WHERE id = '$id'" ;  
	$res=mysqli_query($conn, $sql) ; 
    if (mysqli_num_rows($res) == 1) { # Si on a une réponse à la requête SQL
        return mysqli_fetch_assoc($res); # On renvoie les infos du user sous forme de tableau 
    } else {
        return false; 
    } 
}

function recup_user_mail($conn, $mail) {
    $sql="SELECT * FROM users WHERE email = '$mail'" ;  
	$res=mysqli_query($conn, $sql) ; 
    if (mysqli_num_rows($res) == 1) { # Si on a une réponse à la requête SQL
        return mysqli_fetch_assoc($res); # On renvoie les infos du user sous forme de tableau associatif
    } else {
        return false; 
    } 
}

function recup_user_username($conn, $username) {
    $sql="SELECT * FROM users WHERE username = '$username'" ;  
	$res=mysqli_query($conn, $sql) ; 
    if (mysqli_num_rows($res) == 1) { # Si on a une réponse à la requête SQL
        return mysqli_fetch_assoc($res); # On renvoie les infos du user sous forme de tableau associatif
    } else {
        return false; 
    } 
}

?>