<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'bdd_geststages'; // Remplace par le nom de ta base
$user = 'root'; // Remplace par ton utilisateur MySQL
$password = ''; // Remplace par ton mot de passe MySQL

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

// Récupération des données des entreprises
$query_entreprises = "SELECT * FROM entreprise WHERE en_activite = 1 ORDER BY raison_sociale ASC";
$stmt_entreprises = $pdo->prepare($query_entreprises);
$stmt_entreprises->execute();
$entreprises = $stmt_entreprises->fetchAll(PDO::FETCH_ASSOC);

// Récupération des données des étudiants
$query_etudiants = "SELECT * FROM etudiant ORDER BY nom_etudiant ASC";
$stmt_etudiants = $pdo->prepare($query_etudiants);
$stmt_etudiants->execute();
$etudiants = $stmt_etudiants->fetchAll(PDO::FETCH_ASSOC);

// Récupération des données des professeurs
$query_professeurs = "SELECT * FROM professeur ORDER BY nom_prof ASC";
$stmt_professeurs = $pdo->prepare($query_professeurs);
$stmt_professeurs->execute();
$professeurs = $stmt_professeurs->fetchAll(PDO::FETCH_ASSOC);

// Construction des options pour le select des entreprises
$sel_entreprise = '';
foreach ($entreprises as $entreprise) {
    $sel_entreprise .= '<option value="' . htmlspecialchars($entreprise['num_entreprise']) . '">'
        . htmlspecialchars($entreprise['raison_sociale'] . ' - ' . $entreprise['nom_contact'])
        . '</option>';
}

// Construction des options pour le select des étudiants
$sel_etudiant = '';
foreach ($etudiants as $etudiant) {
    $sel_etudiant .= '<option value="' . htmlspecialchars($etudiant['num_etudiant']) . '">'
        . htmlspecialchars($etudiant['prenom_etudiant'] . ' ' . $etudiant['nom_etudiant'])
        . '</option>';
}

// Construction des options pour le select des professeurs
$sel_professeur = '';
foreach ($professeurs as $professeur) {
    $sel_professeur .= '<option value="' . htmlspecialchars($professeur['num_prof']) . '">'
        . htmlspecialchars($professeur['prenom_prof'] . ' ' . $professeur['nom_prof'])
        . '</option>';
}

?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription au stage</title>
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
            <a href="listeEtudiantStage.php">
                <img src="icons/stage.png" alt="Stagiaire" class="icon"> Stagiaire
            </a>
            <a href="descriptionStage.php" class="active">
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
            <a href="descriptionStage.php" class="active">
                <img src="icons/inscrire.png" alt="Inscription" class="icon"> Inscription
            </a>
            <a href="aide.php">
                <img src="icons/aide.png" alt="Aide" class="icon"> Aide
            </a>
        <?php endif; ?>

        <a href="deconnexion.php">
            Déconnexion
        </a>

        <div class="bottom-buttons">
        </div>
    </div>

    <div class="content">

        <form action="#" method="post" class="form-container">
            <fieldset>
                <legend>Contact</legend>
                <table>
                    <tr>
                        <td><label for="entreprise">Entreprise* :</label></td>
                        <td>
                            <select id="entreprise" name="entreprise" required>
                                <option value="">Sélectionnez une entreprise</option>
                                <?php echo $sel_entreprise; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="etudiant">Étudiant* :</label></td>
                        <td>
                            <select id="etudiant" name="etudiant" required>
                                <option value="">Sélectionnez un étudiant</option>
                                <?php echo $sel_etudiant; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="professeur">Professeur* :</label></td>
                        <td>
                            <select id="professeur" name="professeur" required>
                                <option value="">Sélectionnez un professeur</option>
                                <?php echo $sel_professeur; ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </fieldset>

            <br>

            <fieldset>
                <legend>Description du stage</legend>
                <table>
                    <tr>
                        <td><label for="date-debut">Date de début du stage* :</label></td>
                        <td><input type="date" id="date-debut" name="date-debut" required></td>
                    </tr>
                    <tr>
                        <td><label for="date-fin">Date de fin du stage* :</label></td>
                        <td><input type="date" id="date-fin" name="date-fin" required></td>
                    </tr>
                    <tr>
                        <td><label for="type-stage">Type de stage* :</label></td>
                        <td>
                            <select id="type-stage" name="type-stage" required>
                                <option value="">Aucun</option>
                                <option value="stage">Stage</option>
                                <option value="alternance">Alternance</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="description-projet">Description du projet* :</label></td>
                        <td><textarea id="description-projet" name="description-projet" rows="4" required></textarea></td>
                    </tr>
                    <tr>
                        <td><label for="observation">Observation :</label></td>
                        <td><textarea id="observation" name="observation" rows="2"></textarea></td>
                    </tr>
                </table>
            </fieldset>


            <div class="info-obligatoire">
                <p>Les champs comportant le symbole * sont obligatoires</p>
            </div>

            <div class="valide">
                <button type="submit">Inscrire</button>
            </div>
        </form>
    </div>
</body>

</html>




