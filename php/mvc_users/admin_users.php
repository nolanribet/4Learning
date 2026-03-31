<?php
include "../db_connect.php";
include "crud_users.php";
include "vue_users.php";

session_start();

function choix_avatar_niveau($niveau) { 
    if ($niveau === 0) {
        return 'avatar_prof.png';
    } 
    elseif ($niveau === 1) {
        return 'avatar_l1.png';
    } 
    elseif ($niveau === 2) {
        return 'avatar_l2.png';
    } 
    elseif ($niveau === 3) {
        return 'avatar_l3.png';
    } 
    elseif ($niveau === 4) {
        return 'avatar_m1.png';
    } 
    elseif ($niveau === 5) {
        return 'avatar_m2.png';
    } 
    else { # Sécurité pour avoir un avatar de base
        return 'avatar_default.png';
    }
}

if(isset($_POST['btn_inscription'])) { # Création d'un profil
    $username = $_POST['username'];
    $mail = $_POST['mail'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $niveau_etude = $_POST['niveau_etude'];
    $avatar_auto = choix_avatar_niveau($niveau_etude);

    if ($password === $confirm_password) { # On vérifie que les mots de passes correspondent 
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $creation_reussie = creer_user($conn, $username, $password_hash, $mail, $niveau_etude, $avatar_auto);

        if ($creation_reussie) {
            echo "Création de compte réalisée avec succès !";
            # Rajouter un lien vers la page de profil
        }
        else {
            echo "Erreur SQL : " . mysqli_error($conn);
            // echo "Mail ou pseudo déjà utilisé !";
            form_inscription();
        }
        
    }
    else {
        echo "Les mots de passes sont différents !";
        form_inscription();
    }
}

elseif (isset($_POST['btn_connexion'])) { # Connexion à un profil existant
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = recup_user_username($conn, $username);

    if ($user && password_verify($password, $user['password'])) { # On vérifie si le pseudo existe et que le password est bon
    
        # Gestion des sessions
        $_SESSION['id']           = $user['id'];
        $_SESSION['username']     = $user['username']; 
        $_SESSION['niveau_etude'] = $user['niveau_etude'];
        
        echo "Connexion réussie";
        
    } else {
        echo "Pseudo ou mot de passe incorrect";
        form_connexion(); 
    }
}


elseif (isset($_POST['btn_modif_profil'])) { # Modification des infos de profil

    $username     = $_POST['username'];
    $mail         = $_POST['mail'];
    $niveau_etude = $_POST['niveau_etude'];
    $avatar       = choix_avatar_niveau($niveau_etude);

    $id_utilisateur = $_SESSION['id'];

    $modif_reussie = modifier_user($conn, $id_utilisateur, $username, $mail, $niveau_etude, $avatar);

    if ($modif_reussie) {
        echo "Profil mis à jour avec succès !";
    } else {
        echo "Erreur lors de la mise à jour";
    }
}

elseif (isset($_POST['btn_modif_mdp'])) { # Modification du mot de passe

    $ancien_mdp       = $_POST['ancien_mdp'];
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $id_utilisateur = $_SESSION['id'];

    if ($password === $confirm_password) { # On vérifie que les deux nouveaux mdp sont identiques
        $user_actuel = recup_user_id($conn, $id_utilisateur);
        if ($user_actuel && password_verify($ancien_mdp, $user_actuel['password'])) { # On vérifie que l'ancien mdp est correcte en allant cherche dans la db
            
            $new_password_hash = password_hash($password, PASSWORD_DEFAULT);

            $modif_reussie = modifier_mdp_user($conn, $id_utilisateur, $new_password_hash);

            if ($modif_reussie) {
                echo "Mot de passe mis à jour avec succès";
            } else {
                echo "Erreur lors de la mise à jour";
            }

        } else { # Si l'ancien mot de passe n'est pas correcte
            echo "Erreur : L'ancien mot de passe est incorrect !";
        }

    } else {
        echo "Erreur : Les deux nouveaux mots de passe ne sont pas identiques.";
    }
}

elseif (isset($_POST['btn_deconnexion'])){
    session_unset();
    session_destroy();
    echo "Tu as été déconnecté !";
    }

// else {
//     form_modification_mdp();
// }

echo "<h1>🛠️ Zone de Test Admin Users</h1>";
echo "<hr>";

if (isset($_SESSION['id'])) {
    // ---- MODE CONNECTÉ ----
    echo "<h2>Bienvenue, " . htmlspecialchars($_SESSION['username']) . " !</h2>";
    
    // On récupère les infos fraîches pour pré-remplir le formulaire
    $user_actuel = recup_user_id($conn, $_SESSION['id']);
    
    if ($user_actuel) {
        echo "<h3>1. Modifier mon profil</h3>";
        // On affiche le formulaire avec ses infos actuelles
        form_modification_profil($user_actuel['username'], $user_actuel['email'], $user_actuel['niveau_etude']);
        
        echo "<hr>";
        
        echo "<h3>2. Changer mon mot de passe</h3>";
        form_modification_mdp();
        
        echo "<hr>";
        
        echo "<h3>3. Quitter</h3>";
        form_deconnexion();
    }
} 
else {
    // ---- MODE DÉCONNECTÉ (Visiteur) ----
    echo "<h2>🔑 Se connecter</h2>";
    form_connexion();
    
    echo "<hr>";
    
    echo "<h2>📝 Créer un nouveau compte</h2>";
    form_inscription();
}

?>


