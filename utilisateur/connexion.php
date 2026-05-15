<?php
session_start();
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$email = $_POST['connexion_AdresseEmail'];
		$mdp = $_POST['connexion_mdp'];
		
		$fichier = '../utilisateurs.json';
		
		if(file_exists($fichier)){		
			$contenu_actuel = file_get_contents($fichier);
			$donnees = json_decode($contenu_actuel, true);
			$utilisateur_trouve=false;
			
			
			foreach ($donnees as $utilisateur){
				if($utilisateur['email'] === $email){
					if(password_verify($mdp, $utilisateur['mdp'])){
						$_SESSION['client'] =[
							'nom' => $utilisateur['nom'],
							'prenom' => $utilisateur['prenom'],
							'adresse' => $utilisateur['adresse'],
							'phone' => $utilisateur['phone'],
							'email' => $utilisateur['email'],
						];
						$utilisateur_trouve=true;
						break;
					}
				}
			}
			
			if($utilisateur_trouve){
				header("Location: Accueil.html");
				exit;
			}
			else{
				echo "<p style='color:red'>Adresse mail ou mot de passe incorrect</p>";
				echo "<br><a href='javascript:history.back()'>Retour au formulaire de connexion</a>";
			}
			
		}
		else{
			echo "<b style='color:red'>Erreur: </b>Base de données introuvable";
		}
	}
	else{
		header("Location: connexion.html");
		exit;
	}
?>
