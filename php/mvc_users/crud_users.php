<?php
// =============================================================
//  crud_users.php — CRUD utilisateurs
//  Actions disponibles via $_POST['action'] :
//    register → inscription
//    login    → connexion
//    logout   → déconnexion
// =============================================================

header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../../php/db_connect.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

match($action) {
    'register' => registerUser(),
    'login'    => loginUser(),
    'logout'   => logoutUser(),
    default    => badRequest()
};

/* ===== INSCRIPTION ===== */
function registerUser(): void {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $niveau   = (int) ($_POST['niveau'] ?? 1);

    $errors = [];
    if (strlen($username) < 3 || strlen($username) > 50) $errors[] = 'Pseudo : 3 à 50 caractères.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))        $errors[] = 'E-mail invalide.';
    if (strlen($password) < 8)                             $errors[] = 'Mot de passe : 8 caractères minimum.';
    if ($niveau < 1 || $niveau > 5)                        $errors[] = 'Niveau invalide.';

    if ($errors) { respond(false, implode(' ', $errors), 400); return; }

    $pdo  = getDB();
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) { respond(false, 'Nom d\'utilisateur ou e-mail déjà utilisé.', 409); return; }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $pdo->prepare('INSERT INTO users (username, email, password_hash, niveau) VALUES (?, ?, ?, ?)')
        ->execute([$username, $email, $hash, $niveau]);

    respond(true, 'Compte créé ! Vous pouvez vous connecter.');
}

/* ===== CONNEXION ===== */
function loginUser(): void {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    if (!$email || !$password) { respond(false, 'Champs manquants.', 400); return; }

    $pdo  = getDB();
    $stmt = $pdo->prepare('SELECT id, username, password_hash, niveau, points, fiabilite FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        respond(false, 'Email ou mot de passe incorrect.', 401);
        return;
    }

    $_SESSION['user_id']   = $user['id'];
    $_SESSION['username']  = $user['username'];
    $_SESSION['niveau']    = $user['niveau'];

    respond(true, 'Connexion réussie.', 200, [
        'user' => [
            'id'        => $user['id'],
            'username'  => htmlspecialchars($user['username']),
            'niveau'    => $user['niveau'],
            'points'    => $user['points'],
            'fiabilite' => $user['fiabilite'],
        ]
    ]);
}

/* ===== DÉCONNEXION ===== */
function logoutUser(): void {
    session_destroy();
    respond(true, 'Déconnexion réussie.');
}

/* ===== HELPERS ===== */
function respond(bool $success, string $message, int $code = 200, array $extra = []): void {
    http_response_code($code);
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
}

function badRequest(): void {
    respond(false, 'Action inconnue.', 400);
}
