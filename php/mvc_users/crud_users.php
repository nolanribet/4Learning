<?php
include "../db_connect.php";

function creer_user($conn, $username, $password_hash, $mail, $niveau_etude, $avatar) {
    $username      = mysqli_real_escape_string($conn, $username);
    $mail          = mysqli_real_escape_string($conn, $mail);
    $password_hash = mysqli_real_escape_string($conn, $password_hash);
    $avatar        = mysqli_real_escape_string($conn, $avatar);
    $sql = "INSERT INTO users (username, email, password, niveau_etude, points_total, score_fiabilite, avatar)
            VALUES ('$username', '$mail', '$password_hash', '$niveau_etude', 0, 0, '$avatar')";
    $res = mysqli_query($conn, $sql);
    return $res;
}

function modifier_user($conn, $id, $username, $mail, $niveau_etude, $avatar) {
    $username = mysqli_real_escape_string($conn, $username);
    $mail     = mysqli_real_escape_string($conn, $mail);
    $avatar   = mysqli_real_escape_string($conn, $avatar);
    $sql = "UPDATE users SET username='$username', email='$mail', niveau_etude=$niveau_etude, avatar='$avatar' WHERE id='$id'";
    $res = mysqli_query($conn, $sql);
    return $res;
}

function modifier_mdp_user($conn, $id, $new_password_hash) {
    $hash = mysqli_real_escape_string($conn, $new_password_hash);
    $res  = mysqli_query($conn, "UPDATE users SET password='$hash' WHERE id='$id'");
    return $res;
}

function supprimer_user($conn, $id) {
    $res = mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    return $res;
}

function recup_user_id($conn, $id) {
    $res = mysqli_query($conn, "SELECT * FROM users WHERE id='$id'");
    if (mysqli_num_rows($res) == 1) return mysqli_fetch_assoc($res);
    return false;
}

function recup_user_mail($conn, $mail) {
    $mail = mysqli_real_escape_string($conn, $mail);
    $res  = mysqli_query($conn, "SELECT * FROM users WHERE email='$mail'");
    if (mysqli_num_rows($res) == 1) return mysqli_fetch_assoc($res);
    return false;
}

function recup_user_username($conn, $username) {
    $username = mysqli_real_escape_string($conn, $username);
    $res      = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($res) == 1) return mysqli_fetch_assoc($res);
    return false;
}

// Appelé directement (AJAX depuis le front) → retourne du JSON
if (realpath($_SERVER['SCRIPT_FILENAME']) === __FILE__) {
    session_start();
    header('Content-Type: application/json');
    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        $username = trim($_POST['username'] ?? '');
        $mail     = trim($_POST['email']    ?? '');
        $password = $_POST['password']      ?? '';
        $niveau   = (int)($_POST['niveau']  ?? 1);

        if (strlen($username) < 3 || strlen($username) > 50) { echo json_encode(['success'=>false, 'message'=>'Pseudo : 3 à 50 caractères.']); exit; }
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL))        { echo json_encode(['success'=>false, 'message'=>'E-mail invalide.']); exit; }
        if (strlen($password) < 8)                            { echo json_encode(['success'=>false, 'message'=>'Mot de passe : 8 caractères minimum.']); exit; }

        if (recup_user_username($conn, $username) || recup_user_mail($conn, $mail)) {
            echo json_encode(['success'=>false, 'message'=>'Nom d\'utilisateur ou e-mail déjà utilisé.']);
            exit;
        }

        $avatar = ($niveau == 0) ? 'avatar_prof.png' : 'avatar_l' . $niveau . '.png';
        $ok = creer_user($conn, $username, password_hash($password, PASSWORD_DEFAULT), $mail, $niveau, $avatar);
        if ($ok) {
            echo json_encode(['success'=>true, 'message'=>'Compte créé ! Vous pouvez vous connecter.']);
        } else {
            echo json_encode(['success'=>false, 'message'=>'Erreur : ' . mysqli_error($conn)]);
        }

    } elseif ($action === 'login') {
        $mail     = trim($_POST['email']    ?? '');
        $password = $_POST['password']      ?? '';
        $user     = recup_user_mail($conn, $mail);

        if (!$user || !password_verify($password, $user['password'])) {
            echo json_encode(['success'=>false, 'message'=>'Email ou mot de passe incorrect.']);
            exit;
        }

        $_SESSION['id']           = $user['id'];
        $_SESSION['username']     = $user['username'];
        $_SESSION['niveau_etude'] = $user['niveau_etude'];

        echo json_encode(['success'=>true, 'message'=>'Connexion réussie.', 'user'=>[
            'id'        => (int)$user['id'],
            'username'  => htmlspecialchars($user['username']),
            'niveau'    => (int)$user['niveau_etude'],
            'points'    => (int)$user['points_total'],
            'fiabilite' => (int)$user['score_fiabilite'],
        ]]);

    } elseif ($action === 'logout') {
        session_unset();
        session_destroy();
        echo json_encode(['success'=>true]);

    } else {
        echo json_encode(['success'=>false, 'message'=>'Action inconnue.']);
    }
}
?>
