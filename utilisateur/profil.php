<?php
	session_start();
	if(!isset($_SESSION['client'])){
		header("Location: connexion.html");
		exit;
	}

	$fichier = '../utilisateurs.json';
	$donnees = json_decode(file_get_contents($fichier), true);

	if(isset($_GET['email']) && $_SESSION['client']['email'] === "admin@japindien.com"){

		$email_client =   $_GET['email'];
		$profil_client = null;	
		
		foreach ($donnees as $utilisateur){
			if($utilisateur['email']=== $email_client){
				$profil_client=$utilisateur;
				break;
			}
		}
		if(!$profil_client){
			die("Utilisateur introuvable");
		}
	}
	else{
		$profil_client= $_SESSION['client'];	
	}

?>

<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" type="text/css" href="utilisateur.css">
	<meta name="author" content="groupe 6" />
	<meta name="description" content="Cette page est la page d'acceuil du restaurant fictif le Japindien" />
	<meta name="keywords" content="page accueil, restaurant, Japindien" />
</head>

<body>
<p><strong>Nom :</strong> <?php echo $profil_client['nom'] ?></p>
<p><strong>Prénom</strong> <?php echo $profil_client['prenom'] ?></p>
<p><strong>Email :</strong> <?php echo $profil_client['email'] ?></p>
<p><strong>Téléphone:</strong> <?php echo $profil_client['phone'] ?></p>
<p><strong>Adresse</strong> <?php echo $profil_client['adresse'] ?></p>

</body>
</html>
