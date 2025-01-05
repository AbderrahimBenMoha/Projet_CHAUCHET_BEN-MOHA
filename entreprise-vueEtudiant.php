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

// Gestion de la colonne dynamique
$selected_column = $_POST['additional_column'] ?? null;

// Gestion du filtrage
$filter_column = $_POST['filter_column'] ?? null;
$filter_value = $_POST['filter_value'] ?? null;

// Construction des colonnes sélectionnées
$columns = "e.num_entreprise, e.raison_sociale, e.nom_resp, 
            CONCAT(e.rue_entreprise, ', ', e.cp_entreprise, ' ', e.ville_entreprise) AS adresse,
            e.site_entreprise, GROUP_CONCAT(s.libelle SEPARATOR ', ') AS specialites";

if ($selected_column) {
    $columns .= ", e.$selected_column";
}

// Requête SQL avec filtre
$sql = "SELECT $columns
        FROM entreprise e
        LEFT JOIN spec_entreprise se ON e.num_entreprise = se.num_entreprise
        LEFT JOIN specialite s ON se.num_spec = s.num_spec
        WHERE e.en_activite = 1";

if ($filter_column && $filter_value) {
    $sql .= " AND e.$filter_column LIKE :filter_value";
}

$sql .= " GROUP BY e.num_entreprise";

$stmt = $pdo->prepare($sql);

if ($filter_column && $filter_value) {
    $stmt->bindValue(':filter_value', '%' . $filter_value . '%');
}

$stmt->execute();
$entreprises = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Entreprises</title>
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
        <a href="accueilAppli.php" class="active">
            <img src="icons/home.png" alt="Accueil" class="icon"> Accueil
        </a>

        <?php if ($user_role === 'etudiant'): ?>
            <!-- Liens visibles uniquement pour les étudiants -->
            <a href="entreprise-vueEtudiant.php">
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
            <a href="entreprise-vueProf.php">
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
        <div class="button-container">
            <button onclick="toggleSearchForm()" class="button-back">
                <img src="icons/rechercher.png" alt="Rechercher" class="button-icon"> Rechercher une entreprise
            </button>
            <a href="entreprise-vueCreation.php" class="button-back">
                <img src="icons/ajouter.png" alt="Ajouter" class="button-icon"> Ajouter une entreprise
            </a>
        </div>

        <div id="search-form" style="display: none; margin-top: 20px;">
            <form method="post" action="">
                <label for="filter_column">Rechercher par :</label>
                <select name="filter_column" id="filter_column">
                    <option value="raison_sociale">Raison Sociale</option>
                    <option value="email">Email</option>
                    <option value="nom_resp">Nom Responsable</option>
                </select>
                <input type="text" name="filter_value" placeholder="Critère de recherche">
                <button type="submit">Rechercher</button>
            </form>
        </div>

        <hr>

        <form method="post" action="">
            <label for="additional_column">Ajouter une colonne :</label>
            <select name="additional_column" id="additional_column">
                <option value="email">Email</option>
                <option value="niveau">Niveau</option>
                <option value="tel_entreprise">Téléphone</option>
                <option value="fax_entreprise">Fax</option>
            </select>
            <button type="submit">Ajouter</button>
        </form>

        <div class="tableau">
            <table>
                <tr>
                    <th>Opération</th>
                    <th>Entreprise</th>
                    <th>Responsable</th>
                    <th>Adresse</th>
                    <th>Site</th>
                    <th>Spécialités</th>
                    <?php if ($selected_column): ?>
                        <th><?= ucfirst(str_replace('_', ' ', $selected_column)) ?></th>
                    <?php endif; ?>
                </tr>
                <?php foreach ($entreprises as $entreprise): ?>
                <tr>
                    <td>
                        <div class="icon-buttons">
                            <a href="entreprise-vueDescription.php?id=<?= htmlspecialchars($entreprise['num_entreprise']) ?>" class="icon_button">
                                <img src="icons/voir.png" alt="Voir">
                            </a>
                            <a href="ficheEtudiant.php?id=<?= htmlspecialchars($entreprise['num_entreprise']) ?>" class="icon_button">
                                <img src="icons/inscrire.png" alt="Inscrire">
                            </a>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($entreprise['raison_sociale']) ?></td>
                    <td><?= htmlspecialchars($entreprise['nom_resp'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($entreprise['adresse']) ?></td>
                    <td>
                        <?php if (!empty($entreprise['site_entreprise'])): ?>
                            <div class="icon_tab">
                                <a href="<?= htmlspecialchars($entreprise['site_entreprise']) ?>" target="_blank">
                                    <img src="icons/lien.png" alt="Visiter le site" style="width: 24px; height: 24px; vertical-align: middle;">
                                </a>
                            </div>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($entreprise['specialites'] ?? 'N/A') ?></td>
                    <?php if ($selected_column): ?>
                        <td><?= htmlspecialchars($entreprise[$selected_column] ?? 'N/A') ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
