<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'bdd_geststages';
$user = 'root';
$password = '';

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

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Gestion de la colonne dynamique
$selected_column = $_POST['additional_column'] ?? null;

// Gestion du filtrage
$filter_column = $_POST['filter_column'] ?? null;
$filter_value = $_POST['filter_value'] ?? null;

// Requête SQL pour récupérer les informations des étudiants
$columns = "e.num_etudiant, CONCAT(e.prenom_etudiant, ' ', e.nom_etudiant) AS etudiant, 
            ent.raison_sociale AS entreprise, CONCAT(p.prenom_prof, ' ', p.nom_prof) AS professeur";

if ($selected_column) {
    $columns .= ", e.$selected_column";
}

// Requête SQL avec filtre
$sql = "SELECT $columns
        FROM etudiant e
        LEFT JOIN stage s ON e.num_etudiant = s.num_etudiant
        LEFT JOIN entreprise ent ON s.num_entreprise = ent.num_entreprise
        LEFT JOIN professeur p ON s.num_prof = p.num_prof
        WHERE e.en_activite = 1";

if ($filter_column && $filter_value) {
    $sql .= " AND e.$filter_column LIKE :filter_value";
}

$stmt = $pdo->prepare($sql);

if ($filter_column && $filter_value) {
    $stmt->bindValue(':filter_value', '%' . $filter_value . '%');
}

$stmt->execute();
$etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Étudiants</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function toggleSearchForm() {
            const form = document.getElementById('search-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
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
        <div class="button-container">
            <button onclick="toggleSearchForm()" class="button-back">
                <img src="icons/rechercher.png" alt="Rechercher" class="button-icon"> Rechercher un étudiant
            </button>
            <a href="ficheEtudiant.php" class="button-back">
                <img src="icons/ajouter.png" alt="Ajouter" class="button-icon"> Ajouter un étudiant
            </a>
        </div>

        <div id="search-form" style="display: none; margin-top: 20px;">
            <form method="post" action="">
                <label for="filter_column">Rechercher par :</label>
                <select name="filter_column" id="filter_column">
                    <option value="prenom_etudiant">Prénom</option>
                    <option value="nom_etudiant">Nom</option>
                </select>
                <input type="text" name="filter_value" placeholder="Critère de recherche">
                <button type="submit">Rechercher</button>
            </form>
        </div>

        <hr>


        <div class="tableau">
            <table>
                <tr>
                    <th>Opération</th>
                    <th>Opération</th>
                    <th>Étudiant</th>
                    <th>Entreprise</th>
                    <th>Professeur</th>
                    <?php if ($selected_column): ?>
                        <th><?= ucfirst(str_replace('_', ' ', $selected_column)) ?></th>
                    <?php endif; ?>
                </tr>
                <?php foreach ($etudiants as $etudiant): ?>
                <tr>
                    <td>
                        <div class="icon-buttons">
                            <a href="ficheEtudiant.php?id=<?= htmlspecialchars($etudiant['num_etudiant']) ?>" class="icon_button">
                                <img src="icons/voir.png" alt="Voir">
                            </a>
                        </div>
                    </td>
                    <td>
                        <div class="icon-buttons">
                            <a href="ficheEtudiant.php?id=<?= htmlspecialchars($etudiant['num_etudiant']) ?>" class="icon_button">
                                <img src="icons/modifier.png" alt="Modifier">
                            </a>
                            <a href="ListeEtudiantStage.php" class="icon_button" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet etudiant ?');">
                                <img src="icons/supprimer.png" alt="Supprimer">
                            </a>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($etudiant['etudiant'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($etudiant['entreprise'] ?? 'Aucune') ?></td>
                    <td><?= htmlspecialchars($etudiant['professeur'] ?? 'Aucun') ?></td>
                    <?php if ($selected_column): ?>
                        <td><?= htmlspecialchars($etudiant[$selected_column] ?? 'N/A') ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
