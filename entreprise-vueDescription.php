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
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Démarre la session pour vérifier le rôle de l'utilisateur
session_start();

// Vérifie si l'utilisateur est connecté et si le rôle est défini
if (!isset($_SESSION['user_role'])) {
    // Si l'utilisateur n'est pas connecté, redirige-le vers la page de connexion
    header("Location: logAppli.html?error=Vous devez être connecté.");
    exit();
}

// Définir le rôle de l'utilisateur en fonction de la session
$user_role = $_SESSION['user_role'];

// Vérification de l'existence de l'ID dans l'URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Préparer la requête pour récupérer les informations de l'entreprise
    $sql = "SELECT * FROM entreprise WHERE num_entreprise = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    
    // Vérifier si l'entreprise existe
    $entreprise = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$entreprise) {
        echo "<p>Aucune entreprise trouvée pour cet ID.</p>";
        exit;
    }
} else {
    echo "<p>ID invalide ou manquant.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'Entreprise</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="sidebar">
    <a href="accueilAppli.php">
            <img src="icons/home.png" alt="Accueil" class="icon"> Accueil
        </a>

        <?php if ($user_role === 'etudiant'): ?>
            <!-- Liens visibles uniquement pour les étudiants -->
            <a href="entreprise-vueEtudiant.php" class="active">
                <img src="icons/entreprise.png" alt="Entreprise" class="icon"> Entreprise
            </a>
            <a href="listeEtudiantStage.php">
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
            <a href="entreprise-vueProf.php" class="active">
                <img src="icons/entreprise.png" alt="Entreprise" class="icon"> Entreprise
            </a>
            <a href="listeEtudiantStage.php">
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
        <h1>Détails de l'Entreprise</h1>
        <div class="entreprise-details">
            <table>
                <tr>
                    <th>Raison Sociale</th>
                    <td><?= htmlspecialchars($entreprise['raison_sociale']) ?></td>
                </tr>
                <tr>
                    <th>Nom du Contact</th>
                    <td><?= htmlspecialchars($entreprise['nom_contact']) ?></td>
                </tr>
                <tr>
                    <th>Nom du Responsable</th>
                    <td><?= htmlspecialchars($entreprise['nom_resp']) ?></td>
                </tr>
                <tr>
                    <th>Adresse</th>
                    <td><?= htmlspecialchars($entreprise['rue_entreprise']) ?>, <?= htmlspecialchars($entreprise['cp_entreprise']) ?>, <?= htmlspecialchars($entreprise['ville_entreprise']) ?></td>
                </tr>
                <tr>
                    <th>Téléphone</th>
                    <td><?= htmlspecialchars($entreprise['tel_entreprise']) ?></td>
                </tr>
                <tr>
                    <th>Fax</th>
                    <td><?= htmlspecialchars($entreprise['fax_entreprise']) ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?= htmlspecialchars($entreprise['email']) ?></td>
                </tr>
                <tr>
                    <th>Site Web</th>
                    <td><a href="<?= htmlspecialchars($entreprise['site_entreprise']) ?>" target="_blank"><?= htmlspecialchars($entreprise['site_entreprise']) ?></a></td>
                </tr>
                <tr>
                    <th>Observation</th>
                    <td><?= nl2br(htmlspecialchars($entreprise['observation'])) ?></td>
                </tr>
                <tr>
                    <th>Niveau</th>
                    <td><?= htmlspecialchars($entreprise['niveau']) ?></td>
                </tr>
                <tr>
                    <th>En Activité</th>
                    <td><?= $entreprise['en_activite'] == 1 ? 'Oui' : 'Non' ?></td>
                </tr>
            </table>
        </div>

        <?php if ($user_role === 'etudiant'): ?>
            <a href="entreprise-vueEtudiant.php" class="button-back">Retour à la liste des entreprises</a>
        <?php elseif ($user_role === 'professeur'): ?>
            <a href="entreprise-vueProf.php" class="button-back">Retour à la liste des entreprises</a>
        <?php endif; ?>
    </div>
</body>
</html>
