<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	$temps_actuel = time(); // Temps actuel en sec
	$delai_minimum = 10;

	if (isset($_SESSION['derniere_tentative'])) {
		$temps_ecoule = $temps_actuel - $_SESSION['derniere_tentative'];

		if ($temps_ecoule < $delai_minimum) {
			$temps_attente = $delai_minimum - $temps_ecoule;
			echo "<p style='color:red; font-weight:bold;'>Veuillez patienter encore {$temps_attente} seconde(s) avant de réessayer.</p>";
			echo "<br><a href='javascript:history.back()'>Retour</a>";
			exit;
		}
	}

	$_SESSION['derniere_tentative'] = $temps_actuel;


	$email = $_POST['connexion_AdresseEmail'];
	$mdp = $_POST['connexion_mdp'];

	$fichier = '../utilisateurs.json';

	if (file_exists($fichier)) {
		$contenu_actuel = file_get_contents($fichier);
		$donnees = json_decode($contenu_actuel, true);
		$utilisateur_trouve = false;

		foreach ($donnees as $utilisateur) {
			if ($utilisateur['email'] === $email) {
				if (password_verify($mdp, $utilisateur['mdp'])) {
					unset($_SESSION['derniere_tentative']);

					$_SESSION['client'] = [
						'nom' => $utilisateur['nom'],
						'prenom' => $utilisateur['prenom'],
						'adresse' => $utilisateur['adresse'],
						'phone' => $utilisateur['phone'],
						'email' => $utilisateur['email'],
					];
					$utilisateur_trouve = true;
					break;
				}
			}
		}

		if ($utilisateur_trouve) {
			if ($email === "admin@japindien.com") {
				header("Location: ../administrateur/admin.php");
				exit;
			}
			header("Location: Accueil.php");
			exit;
		} else {
			echo "<p style='color:red'>Adresse mail ou mot de passe incorrect</p>";
			echo "<br><a href='javascript:history.back()'>Retour au formulaire de connexion</a>";
		}
	} else {
		echo "<b style='color:red'>Erreur: </b>Base de données introuvable";
	}
} else {
	header("Location: connexion.html");
	exit;
}
