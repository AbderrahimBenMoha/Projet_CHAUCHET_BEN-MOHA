<?php
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
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stage BTS</title>
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
            <a href="descriptionStage.php">
                <img src="icons/inscrire.png" alt="Inscription" class="icon"> Inscription
            </a>
            <a href="aide.php" class="active">
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
            <a href="aide.php" class="active">
                <img src="icons/aide.png" alt="Aide" class="icon"> Aide
            </a>
        <?php endif; ?>

        <a href="deconnexion.php">
            Déconnexion
        </a>
    </div>

    <div class="entete">
        <h1 class="titre">Aide</h1>
        <p class="sous-titre">Bienvenue sur la FAQ</p>
        <hr>
    </div>

    <div class="content">
    <h2>Entreprise</h2>
        <h3>Comment chercher une entreprise ?</h3>
        <div class="cadres">
            <table>
                <tr>
                    <td>
                        <p>Si vous voulez rechercher une entreprise, vous devez aller sur la page " Entreprise ", pour cliquer sur
                            le bouton " Rechercher une entreprise ". Il vous est alors fourni trois critères. Utilisez-les afin de
                            pouvoir trouver les entreprises qui correspondent à vos choix.</p>
                    </td>
                </tr>
            </table>
        </div>

        <h3>Comment ajouter une entreprise ?</h3>
        <div class="cadres">
            <table>
                <tr>
                    <td>
                        <p>Pour ajouter une entreprise, rendez-vous sur la page " Entreprise ", où vous devez cliquer sur le
                            bouton " Ajouter une entreprise ". Vous devrez ensuite ajouter les informations concernant
                            l'entreprise. Toutes les informations ne sont pas obligatoires, mais il est conseillé d'en fournir un
                            maximum pour renseigner les futurs stagiaires sur les entreprises référencées.</p>
                    </td>
                </tr>
            </table>
        </div>

        <h3>Comment afficher ou enlever une information concernant l'entreprise ?</h3>
        <div class="cadres">
            <table>
                <tr>
                    <td>
                        <p>En allant sur la page " Entreprise ", vous pouvez voir les entreprises déjà référencées. Vous pouvez
                            alors remarquer que certaines informations concernant l'entreprise sont absentes. Vous pouvez
                            cependant les afficher grâce à la liste déroulante : choisissez l'information que vous voulez afficher
                            puis cliquez sur le bouton " Ajouter ". Si vous voulez enlever une information, il vous suffit de cliquer
                            sur le moins situé à l'entête de la colonne représentant l'information concerné.</p>
                    </td>
                </tr>
            </table>
        </div>

        <h3>N'y a-t-il pas une autre solution pour voir ces informations ?</h3>
        <div class="cadres">
            <table>
                <tr>
                    <td>
                        <p>Bien sûr, vous pouvez cliquer sur l'icône <img src="icons/voir.png" alt="voir" class="mini_icon"> pour voir toutes les informations concernant l'entreprise
                            que vous avez sélectionné.</p>
                    </td>
                </tr>
            </table>
        </div>

        <h3>Comment puis-je supprimer une entreprise ?</h3>
        <div class="cadres">
            <table>
                <tr>
                    <td>
                        <p>Rien de plus simple, il vous suffit de cliquer sur l'icône <img src="icons/supprimer.png" alt="voir" class="mini_icon"> qui se situe sur la deuxième colonne "
                            Opération ".</p>
                    </td>
                </tr>
            </table>
        </div>

        <h3>Et si je veux modifier une information fausse ? </h3>
        <div class="cadres">
            <table>
                <tr>
                    <td>
                        <p>Cliquez sur l’icône <img src="icons/modifier.png" alt="voir" class="mini_icon">, puis changer le(s) information(s) que vous voulez. Vous pourrez par la même 
                            occasion renseigner une information manquante si vous en avez la possibilité.</p>
                    </td>
                </tr>
            </table>
        </div>

        <h2>Stagiaire</h2>
        <h3>Comment rechercher un stagiaire ? </h3>
        <div class="cadres">
            <table>
                <tr>
                    <td>
                        <p>Tout d'abord, dirigez-vous sur la page " Stagiaire ". Cliquez ensuite sur le bouton " Rechercher un 
                            stagiaire existant ". Vous aurez alors quatre listes déroulantes. Vous pourrez alors choisir, pour 
                            chaque champ, l'information voulue. </p>
                    </td>
                </tr>
            </table>
        </div>

        <h3>Comment inscrire un étudiant à un stage ? </h3>
        <div class="cadres">
            <table>
                <tr>
                    <td>
                        <p>Pour cela, vous devez vous rendre sur la page " Inscription ". Ensuite, vous devrez remplir un 
                            formulaire contenant diverses informations concernant le stage de l’étudiant, comme par exemple 
                            l’entreprise ou encore le professeur qui s’occupera du stage de l’étudiant. Vous pouvez aussi le faire à 
                            partir de la page " Entreprise " : cliquez sur la poignée de main située sur la première colonne " 
                            Opération ", et le formulaire d'inscription s'affichera avec le nom de l'entreprise pré-rentré. </p>
                    </td>
                </tr>
            </table>
        </div>

        <h3>Comment peut-on voir les informations des stagiaires ? </h3>
        <div class="cadres">
            <table>
                <tr>
                    <td>
                        <p>Sur la liste qui s'affiche sur la page " Stagiaire ", ou en cliquant sur l’icône<img src="icons/voir.png" alt="voir" class="mini_icon">. </p>
                    </td>
                </tr>
            </table>
        </div>

        <h3>Comment peut-on supprimer un stagiaire ? </h3>
        <div class="cadres">
            <table>
                <tr>
                    <td>
                        <p>Comme pour une entreprise : cliquez sur l'icône <img src="icons/supprimer.png" alt="voir" class="mini_icon"> présente sur la page " Stagiaire ". </p>
                    </td>
                </tr>
            </table>
        </div>

        <h3>Et pour modifier le contenu d'un champ, pareil que pour les entreprises ?</h3>
        <h3>Tout juste !</h3>
    </div>
</body>

</html>