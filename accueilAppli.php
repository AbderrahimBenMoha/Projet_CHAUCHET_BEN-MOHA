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
    <style>
        /* Styles pour gérer l'état réduit de la sidebar */
        .sidebar {
            transition: width 0.3s ease;
            width: 250px; /* Largeur par défaut */
        }

        .sidebar.reduced {
            width: 80px; /* Largeur réduite */
        }

        .sidebar a {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .sidebar.reduced a .icon {
            margin-right: 0;
        }

        .sidebar.reduced a {
            justify-content: center;
        }
    </style>
</head>

<body>
    <div class="sidebar" id="sidebar">
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

        <!-- Boutons en bas de la page -->
        <div class="bottom-buttons">
            <a href="#" onclick="expandSidebar()">
                <img src="icons/droite.png" alt="Développer" class="icon"> Développer
            </a>
            <a href="#" onclick="reduceSidebar()">
                <img src="icons/gauche.png" alt="Réduire" class="icon"> Réduire
            </a>
        </div>
    </div>

    <div class="entete">
        <h1 class="titre">Stage BTS</h1>
        <p class="sous-titre">Bienvenue sur la page de gestion des stages</p>
        <hr>
    </div>

    <script>
        // Fonction pour développer la sidebar
        function expandSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.remove('reduced');
        }

        // Fonction pour réduire la sidebar
        function reduceSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.add('reduced');
        }
    </script>
</body>

</html>
