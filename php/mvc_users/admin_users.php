<?php
session_start();

include "../db_connect.php";
include "crud_users.php";
include "vue_users.php";

// Appelé en GET depuis le front pour les stats → JSON
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($_POST)) {
    header('Content-Type: application/json');
    $nb_users     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as n FROM users"))['n'];
    $nb_questions = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as n FROM questions"))['n'];
    $nb_reponses  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as n FROM reponses"))['n'];
    echo json_encode(['success'=>true, 'users'=>(int)$nb_users, 'questions'=>(int)$nb_questions, 'answers'=>(int)$nb_reponses]);
    exit;
}

function choix_avatar_niveau($niveau) {
    $n = (int)$niveau;
    if ($n === 0)                        return 'avatar_prof.png';
    elseif ($n >= 1 && $n <= 3)          return 'avatar_l' . $n . '.png';
    elseif ($n === 4 || $n === 5)        return 'avatar_m' . ($n - 3) . '.png';
    else                                 return 'avatar_defaut.png';
}

if (isset($_POST['btn_inscription'])) {
    $username      = $_POST['username'];
    $mail          = $_POST['mail'];
    $password      = $_POST['password'];
    $confirm       = $_POST['confirm_password'];
    $niveau_etude  = $_POST['niveau_etude'];
    $avatar        = choix_avatar_niveau($niveau_etude);

    if ($password === $confirm) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ok   = creer_user($conn, $username, $hash, $mail, $niveau_etude, $avatar);
        if ($ok) {
            echo "Création de compte réalisée avec succès !";
        } else {
            echo "Erreur SQL : " . mysqli_error($conn);
            form_inscription();
        }
    } else {
        echo "Les mots de passes sont différents !";
        form_inscription();
    }

} elseif (isset($_POST['btn_connexion'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user     = recup_user_username($conn, $username);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['id']           = $user['id'];
        $_SESSION['username']     = $user['username'];
        $_SESSION['niveau_etude'] = $user['niveau_etude'];
        echo "Connexion réussie";
    } else {
        echo "Pseudo ou mot de passe incorrect";
        form_connexion();
    }

} elseif (isset($_POST['btn_modif_profil'])) {
    $username     = $_POST['username'];
    $mail         = $_POST['mail'];
    $niveau_etude = $_POST['niveau_etude'];
    $avatar       = choix_avatar_niveau($niveau_etude);
    $ok = modifier_user($conn, $_SESSION['id'], $username, $mail, $niveau_etude, $avatar);
    echo $ok ? "Profil mis à jour avec succès !" : "Erreur lors de la mise à jour";

} elseif (isset($_POST['btn_modif_mdp'])) {
    $ancien   = $_POST['ancien_mdp'];
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if ($password === $confirm) {
        $user = recup_user_id($conn, $_SESSION['id']);
        if ($user && password_verify($ancien, $user['password'])) {
            $ok = modifier_mdp_user($conn, $_SESSION['id'], password_hash($password, PASSWORD_DEFAULT));
            echo $ok ? "Mot de passe mis à jour avec succès" : "Erreur lors de la mise à jour";
        } else {
            echo "Erreur : L'ancien mot de passe est incorrect !";
        }
    } else {
        echo "Erreur : Les deux nouveaux mots de passe ne sont pas identiques.";
    }

} elseif (isset($_POST['btn_deconnexion'])) {
    session_unset();
    session_destroy();
    echo "Tu as été déconnecté !";

} else {
    form_modification_mdp();
}
?>
