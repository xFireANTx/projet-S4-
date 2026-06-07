<?php
	//TODO stylisé les pages d'erreurs et de succès avec la da du site
	$nom = $_POST['nom'];
	$prenom = $_POST['prenom'];
	$adresse = $_POST['adresse'];
	$phone = $_POST['phone'];
	$email = $_POST['AdresseEmail'];
	$mdp = $_POST['mdp'];
	$confirmationMdp = $_POST['confirmation_mdp'];
	
	$erreurs = [];
	
	
	//Vérification que les données en entrée du formulaire sont conformes(a priori il y a aussi un message par défaut sur le navigateur)	
	if(empty($nom)){
		$erreurs[] = "Le nom est obligatoire.";
	}
	elseif(!preg_match("/^[a-zA-ZÀ-ÿ\s\-]+$/",$nom)) {
    		$erreurs[] = "Le nom ne doit contenir que des lettres, des espaces ou des tirets.";
	}
	if(empty($prenom)){
		$erreurs[] = "Le nom est obligatoire.";
	}
	elseif(!preg_match("/^[a-zA-ZÀ-ÿ\s\-]+$/",$prenom)) {
    		$erreurs[] = "Le prénom ne doit contenir que des lettres, des espaces ou des tirets.";
	}
	
	if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
		$erreurs[] = "L'adresse email n'est pas valide.";
	}
	
	if(strlen($mdp)<8){
		$erreurs[]= "Le mot de passe doit contenir au moins 8 caractères.";
	}
	
	if($mdp !== $confirmationMdp){
		$erreurs[]= "La confirmation du mot de passe ne correspond pas.";
	}
	
	//Si tout est en ordre on récupère les données de la base et on compare pour voir si l'utilisateur existe deja, si non on l'ajoute à la base

	if(empty($erreurs)){
	
		$fichier = '../utilisateurs.json';//Le fichier json est censé être à la racine du projet donc dans le dossier parent
		$contenu_actuel = file_get_contents($fichier);
		$donnees = json_decode($contenu_actuel, true);
		
		if (!is_array($donnees)) { 
			$donnees=[];
		}
		foreach($donnees as $utilisateur){
			if($utilisateur['email'] === $email) {
		        echo "Cet email est déjà utilisé.";
				echo "<a href='javascript:history.back()'>Retour au formulaire</a>";
				exit;
		    }
			elseif($utilisateur['phone'] === $phone){
			    echo "Ce numéro de téléphone est déjà assigné à ce mail: {$utilisateur['email']}";
				echo "<a href='javascript:history.back()'>Retour au formulaire</a>";
				exit;
			}
		}

		$nouvelUtilisateur = ["nom" => $nom,
			"prenom" => $prenom,
			"adresse" => $adresse, 
		 	"phone" => $phone, 
		 	"email" => $email, 
		 	"mdp" => password_hash($mdp, PASSWORD_DEFAULT),//Le mode de passe est encrypté même dans le .json
			"loyalty" => 0,
			"order" => [],
			];

		array_push($donnees, $nouvelUtilisateur);
		if(file_put_contents($fichier, json_encode($donnees, JSON_PRETTY_PRINT))) {
		    echo "Inscription réussie ! <a href='connexion.html'>Connectez-vous ici</a>";
		}else{
		    echo "Erreur lors de l'enregistrement.";
		    echo "<a href='javascript:history.back()'>Retour au formulaire</a>";
		}
	}else{
		foreach($erreurs as $erreur){
			echo "<p style='color:red;'>$erreur</p>";
		}
		echo "<a href='javascript:history.back()'>Retour au formulaire</a>";
	}

?>
