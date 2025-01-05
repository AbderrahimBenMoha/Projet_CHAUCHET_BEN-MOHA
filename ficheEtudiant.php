<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_role'])) {
    header("Location: logAppli.html?error=Vous devez être connecté.");
    exit();
}

$user_role = $_SESSION['user_role'];

// Connexion à la base de données
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'bdd_geststages';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Récupérer l'ID de l'étudiant depuis l'URL
$etudiant_id = isset($_GET['id']) ? $_GET['id'] : null;
$etudiant = null;

if ($etudiant_id) {
    $stmt = $conn->prepare("SELECT nom_etudiant, prenom_etudiant, login FROM etudiant WHERE num_etudiant = ?");
    $stmt->bind_param("i", $etudiant_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $etudiant = $result->fetch_assoc();
    }
    $stmt->close();
}

// Récupérer les classes disponibles
$classes = [];
$result = $conn->query("SELECT num_classe, nom_classe FROM classe");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
}

// Traiter le formulaire soumis
$message = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $usernameInput = $_POST['username'] ?? '';
    $passwordInput = $_POST['password'] ?? '';
    $date = $_POST['date'] ?? null;
    $classe = $_POST['classe'] ?? '';

    if ($nom && $prenom && $usernameInput && $passwordInput && $classe) {
        $stmt = $conn->prepare("UPDATE etudiant SET nom_etudiant = ?, prenom_etudiant = ?, annee_obtention = ?, login = ?, mdp = ?, num_classe = ? WHERE num_etudiant = ?");
        $stmt->bind_param("ssssssi", $nom, $prenom, $date, $usernameInput, $passwordInput, $classe, $etudiant_id);

        if ($stmt->execute()) {
            $message = "Informations de l'étudiant mises à jour avec succès.";
        } else {
            $message = "Erreur lors de la mise à jour des informations : " . $conn->error;
        }

        $stmt->close();
    } else {
        $message = "Veuillez remplir tous les champs obligatoires.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche Étudiant</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
        <a href="accueilAppli.php">
            <img src="icons/home.png" alt="Accueil" class="icon"> Accueil
        </a>

        <?php if ($user_role === 'etudiant'): ?>
            <!-- Liens visibles uniquement pour les étudiants -->
            <a href="entreprise-vueEtudiant.php">
                <img src="icons/entreprise.png" alt="Entreprise" class="icon"> Entreprise
            </a>
            <a href="listeEtudiantStage.php" class="active">
                <img src="icons/stage.png" alt="Stagiaire" class="icon"> Stagiaire
            </a>
            <a href="descriptionStage.php">
                <img src="icons/inscrire.png" alt="Inscription" class="icon"> Inscription
            </a>
            <a href="aide.php">
                <img src="icons/aide.png" alt="Aide" class="icon"> Aide
            </a>
        <?php elseif ($user_role === 'professeur'): ?>
            <!-- Liens visibles uniquement pour les professeurs -->
            <a href="entreprise-vueProf.php">
                <img src="icons/entreprise.png" alt="Entreprise" class="icon"> Entreprise
            </a>
            <a href="listeEtudiantStage.php" class="active">
                <img src="icons/stage.png" alt="Stagiaire" class="icon"> Stagiaire
            </a>
            <a href="descriptionStage.php">
                <img src="icons/inscrire.png" alt="Inscription" class="icon"> Inscription
            </a>
            <a href="aide.php">
                <img src="icons/aide.png" alt="Aide" class="icon"> Aide
            </a>
        <?php endif; ?>

        <a href="deconnexion.php">
            Déconnexion
        </a>
    </div>

    <div class="content">
        <h1>Fiche de l'Étudiant</h1>

        <?php if ($etudiant): ?>
            <form method="POST">
                <table>
                    <tr><th colspan="2">Informations concernant l'étudiant</th></tr>
                    <tr>
                        <td><label for="nom">Nom<span class="important">*</span> :</label></td>
                        <td><input type="text" id="nom" name="nom" value="<?= htmlspecialchars($etudiant['nom_etudiant']) ?>" required></td>
                    </tr>
                    <tr>
                        <td><label for="prenom">Prénom<span class="important">*</span> :</label></td>
                        <td><input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($etudiant['prenom_etudiant']) ?>" required></td>
                    </tr>
                    <tr>
                        <td><label for="username">Nom d'utilisateur (8 caractères)<span class="important">*</span> :</label></td>
                        <td><input type="text" id="username" name="username" minlength="8" maxlength="8" value="<?= htmlspecialchars($etudiant['login']) ?>" required></td>
                    </tr>
                    <tr>
                        <td><label for="password">Mot de passe (entre 8 et 30 caractères)<span class="important">*</span> :</label></td>
                        <td><input type="password" id="password" name="password" minlength="8" maxlength="30" required></td>
                    </tr>
                    <tr>
                        <td><label for="date">Date d'obtention du BTS (AAAA-MM-JJ) :</label></td>
                        <td><input type="date" id="date" name="date" required></td>
                    </tr>
                    <tr>
                        <td><label for="classe">Classe<span class="important">*</span> :</label></td>
                        <td>
                            <select id="classe" name="classe" required>
                                <option value="" disabled>Choisissez une classe</option>
                                <?php foreach ($classes as $classe): ?>
                                    <option value="<?= htmlspecialchars($classe['num_classe']) ?>"><?= htmlspecialchars($classe['nom_classe']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <div class="aide">Les champs comportant le symbole <span class="important">*</span> sont obligatoires.</div>
                <div class="center">
                    <input type="submit" value="Modifier">
                </div>
                <?php if ($message): ?>
                    <p class="message"><?= htmlspecialchars($message) ?></p>
                <?php endif; ?>
            </form>
        <?php else: ?>
            <form method="POST">
                <table>
                    <tr><th colspan="2">Informations concernant l'étudiant</th></tr>
                    <tr>
                        <td><label for="nom">Nom<span class="important">*</span> :</label></td>
                        <td><input type="text" id="nom" name="nom" required></td>
                    </tr>
                    <tr>
                        <td><label for="prenom">Prénom<span class="important">*</span> :</label></td>
                        <td><input type="text" id="prenom" name="prenom" required></td>
                    </tr>
                    <tr>
                        <td><label for="username">Nom d'utilisateur (8 caractères)<span class="important">*</span> :</label></td>
                        <td><input type="text" id="username" name="username" minlength="8" maxlength="8" required></td>
                    </tr>
                    <tr>
                        <td><label for="password">Mot de passe (entre 8 et 30 caractères)<span class="important">*</span> :</label></td>
                        <td><input type="password" id="password" name="password" minlength="8" maxlength="30" required></td>
                    </tr>
                    <tr>
                        <td><label for="date">Date d'obtention du BTS (AAAA-MM-JJ) :</label></td>
                        <td><input type="date" id="date" name="date" required></td>
                    </tr>
                    <tr>
                        <td><label for="classe">Classe<span class="important">*</span> :</label></td>
                        <td>
                            <select id="classe" name="classe" required>
                                <option value="" disabled>Choisissez une classe</option>
                                <?php foreach ($classes as $classe): ?>
                                    <option value="<?= htmlspecialchars($classe['num_classe']) ?>"><?= htmlspecialchars($classe['nom_classe']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <div class="aide">Les champs comportant le symbole <span class="important">*</span> sont obligatoires.</div>
                <div class="center">
                    <input type="submit" value="Modifier">
                </div>
        <?php endif; ?>
    </div>
</body>
</html>
