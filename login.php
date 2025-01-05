<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'bdd_geststages';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupération des données
$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

// Validation des données
if (empty($login) || empty($password) || empty($role)) {
    header("Location: index.html?error=Veuillez remplir tous les champs.");
    exit();
}

// Vérification dans la base de données
$table = $role === 'etudiant' ? 'etudiant' : 'professeur';
$query = $pdo->prepare("SELECT * FROM $table WHERE login = :login AND mdp = :mdp");
$query->execute(['login' => $login, 'mdp' => $password]);

if ($query->rowCount() > 0) {
    // Connexion réussie : démarrage de la session
    session_start();
    $_SESSION['user_role'] = $role;
    header("Location: accueilAppli.php");
    exit();
} else {
    // Échec de connexion
    header("Location: logAppli.html?error=Identifiant ou mot de passe incorrect.");
    exit();
}
?>
