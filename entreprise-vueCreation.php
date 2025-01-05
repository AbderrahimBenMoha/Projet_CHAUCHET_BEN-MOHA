<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'bdd_geststages'; // Remplace par le nom de ta base
$user = 'root'; // Remplace par ton utilisateur MySQL
$password = ''; // Remplace par ton mot de passe MySQL

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


// Récupération des données des specialites
$query_specialites = "SELECT * FROM specialite";
$stmt_specialites = $pdo->prepare($query_specialites);
$stmt_specialites->execute();
$specialites = $stmt_specialites->fetchAll(PDO::FETCH_ASSOC);

// Construction des options pour le select des specialites
$sel_specialite = '';
foreach ($specialites as $specialite) {
    $sel_specialite .= '<option value="' . htmlspecialchars($specialite['num_spec']) . '">'
        . htmlspecialchars($specialite['libelle'])
        . '</option>';
}



// Vérification si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $raison_sociale = htmlspecialchars($_POST['nom-entreprise']);
    $nom_contact = htmlspecialchars($_POST['nom-contact']);
    $nom_resp = htmlspecialchars($_POST['nom-responsable']);
    $rue = htmlspecialchars($_POST['rue']);
    $cp = htmlspecialchars($_POST['code-postal']);
    $ville = htmlspecialchars($_POST['ville']);
    $tel = htmlspecialchars($_POST['telephone']);
    $fax = htmlspecialchars($_POST['fax']);
    $email = htmlspecialchars($_POST['email']);
    $observation = htmlspecialchars($_POST['observation']);
    $site_entreprise = htmlspecialchars($_POST['url-site']);
    $niveau = htmlspecialchars($_POST['niveau']);
    $specialite = htmlspecialchars($_POST['specialite']);
    $en_activite = 1; // Par défaut, l'entreprise est en activité

    try {
        // Requête SQL d'insertion
        $sql = "INSERT INTO entreprise (
                    raison_sociale, nom_contact, nom_resp, rue_entreprise, cp_entreprise, ville_entreprise, 
                    tel_entreprise, fax_entreprise, email, observation, site_entreprise, niveau, en_activite
                ) VALUES (
                    :raison_sociale, :nom_contact, :nom_resp, :rue, :cp, :ville, 
                    :tel, :fax, :email, :observation, :site_entreprise, :niveau, :en_activite
                )";

        $stmt = $pdo->prepare($sql);
        
        // Exécution avec les paramètres
        $stmt->execute([
            ':raison_sociale' => $raison_sociale,
            ':nom_contact' => $nom_contact,
            ':nom_resp' => $nom_resp,
            ':rue' => $rue,
            ':cp' => $cp,
            ':ville' => $ville,
            ':tel' => $tel,
            ':fax' => $fax,
            ':email' => $email,
            ':observation' => $observation,
            ':site_entreprise' => $site_entreprise,
            ':niveau' => $niveau,
            ':en_activite' => $en_activite,
        ]);

        echo "<p>Entreprise ajoutée avec succès !</p>";
    } catch (PDOException $e) {
        echo "<p>Erreur lors de l'ajout de l'entreprise : " . $e->getMessage() . "</p>";
    }
}

// Récupération des données des spécialités
$query_specialites = "SELECT * FROM specialite";
$stmt_specialites = $pdo->prepare($query_specialites);
$stmt_specialites->execute();
$specialites = $stmt_specialites->fetchAll(PDO::FETCH_ASSOC);

// Construction des options pour le select des spécialités
$sel_specialite = '';
foreach ($specialites as $specialite) {
    $sel_specialite .= '<option value="' . htmlspecialchars($specialite['num_spec']) . '">'
        . htmlspecialchars($specialite['libelle'])
        . '</option>';
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Entreprise</title>
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

        <!-- Boutons en bas de la page -->
        <div class="bottom-buttons">
            <a href="#" class="active">
                <img src="icons/droite.png" alt="Développer" class="icon"> Développer
            </a>
            <a href="#">
                <img src="icons/gauche.png" alt="Réduire" class="icon"> Réduire
            </a>
        </div>
    </div>

    <div class="content">
        <h1>Information sur l'Entreprise</h1>
        <form action="#" method="post" class="form-container">
            
            <fieldset>
                <legend>Information</legend>
                <table>
                    <tr>
                        <td><label for="nom-entreprise">Nom de l'entreprise* :</label></td>
                        <td><input type="text" id="nom-entreprise" name="nom-entreprise" required></td>
                    </tr>
                    <tr>
                        <td><label for="nom-contact">Nom du contact :</label></td>
                        <td><input type="text" id="nom-contact" name="nom-contact"></td>
                    </tr>
                    <tr>
                        <td><label for="nom-responsable">Nom du responsable :</label></td>
                        <td><input type="text" id="nom-responsable" name="nom-responsable"></td>
                    </tr>
                </table>
            </fieldset>

            <br>

            <fieldset>
                <legend>Contact</legend>
                <table>
                    <tr>
                        <td><label for="rue">Rue* :</label></td>
                        <td><input type="text" id="rue" name="rue" required></td>
                    </tr>
                    <tr>
                        <td><label for="code-postal">Code postal* :</label></td>
                        <td><input type="text" id="code-postal" name="code-postal" required></td>
                    </tr>
                    <tr>
                        <td><label for="ville">Ville* :</label></td>
                        <td><input type="text" id="ville" name="ville" required></td>
                    </tr>
                    <tr>
                        <td><label for="telephone">Téléphone* :</label></td>
                        <td><input type="tel" id="telephone" name="telephone" required></td>
                    </tr>
                    <tr>
                        <td><label for="fax">Fax :</label></td>
                        <td><input type="tel" id="fax" name="fax"></td>
                    </tr>
                    <tr>
                        <td><label for="email">Email :</label></td>
                        <td><input type="email" id="email" name="email"></td>
                    </tr>
                </table>
            </fieldset>

            <br>

            <fieldset>
                <legend>Divers</legend>
                <table>
                    <tr>
                        <td><label for="observation">Observation :</label></td>
                        <td><textarea id="observation" name="observation" rows="4"></textarea></td>
                    </tr>
                    <tr>
                        <td><label for="url-site">URL du site :</label></td>
                        <td><input type="url" id="url-site" name="url-site"></td>
                    </tr>
                    <tr>
                        <td><label for="niveau">Niveau* :</label></td>
                        <td><input type="text" id="niveau" name="niveau" required></td>
                    </tr>
                </table>
            </fieldset>

            <br>

            <fieldset>
                <legend>Spécialité</legend>
                <table>
                    <tr>
                        <td><label for="specialite">Spécialité :</label></td>
                        <td>
                            <select id="specialite" name="specialite" required>
                                <option value="">Sélectionnez une spécialité</option>
                                <?php echo $sel_specialite; ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </fieldset>


            <div class="info-obligatoire">
                <p>Les champs comportant le symbole * sont obligatoires.</p>
            </div>
            <div class="valide">
                <button type="submit">Modifier</button>
            </div>
        </form>
    </div>
</body>
</html>