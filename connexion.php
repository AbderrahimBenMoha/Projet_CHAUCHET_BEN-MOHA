<?php
// Connexion à la base de données
$servername = "localhost"; // Remplacez par votre hôte de base de données
$username = "root"; // Remplacez par votre utilisateur de base de données
$password = ""; // Remplacez par votre mot de passe
$dbname = "bdd_getstages"; // Nom de votre base de données

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer les données du formulaire
$login = $_POST['login'];
$password = $_POST['password'];
$role = $_POST['role']; // 'etudiant' ou 'professeur'

// Sécuriser les entrées pour éviter les injections SQL
$login = $conn->real_escape_string($login);
$password = $conn->real_escape_string($password);
$role = $conn->real_escape_string($role);

// Vérifier les informations d'identification en fonction du rôle
if ($role == 'etudiant') {
    // Vérifier dans la table des étudiants
    $sql = "SELECT * FROM etudiant WHERE login = '$login' AND mdp = '$password'";
} elseif ($role == 'professeur') {
    // Vérifier dans la table des professeurs
    $sql = "SELECT * FROM professeur WHERE login = '$login' AND mdp = '$password'";
} else {
    // Rôle non valide
    echo "Rôle non valide.";
    exit();
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Utilisateur trouvé, rediriger vers la page d'accueil
    header("Location: accueilAppli.html");
    exit();
} else {
    // Identifiants invalides ou utilisateur non actif
    echo "Identifiants invalides ou utilisateur inactif.";
}

$conn->close();
?>
