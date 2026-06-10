<?php
	session_start();
	if(!isset($_SESSION['client']) || $_SESSION['client']['email'] !== "admin@japindien.com"){
		header("Location: ../utilisateur/Accueil.php");
	}
	$fichier = '../utilisateurs.json';
	$utilisateurs =[];
	
	if(file_exists($fichier)){
		$utilisateurs = json_decode(file_get_contents($fichier), true);
	}
	?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>administateur</title>
    <link rel="stylesheet" href="administrateur.css">
</head>
<body>
		<h1>Gestion des Utilisateurs</h1>
	    <div class="tableau">
		    <br>
		    <table border="1" style="width:100%">
		    <thead>
				<tr>
					<th><u>Nom</u></th>
					<th><u>Prénom</u></th>
					<th><u>Mail</u></th>
					<th><u>Téléphone</u></th>
					<th><u>Profil</u></th>

				</tr>
			</thead>
			<tbody>
			<?php 
				if(!empty($utilisateurs)){
					foreach($utilisateurs as $u){
			?>
						<tr align="center">
							<td><?php echo $u['nom']; ?></td>
							<td><?php echo $u['prenom']; ?></td>
							<td><?php echo $u['email']; ?></td>
							<td><?php echo $u['phone']; ?></td>
							<td>
								<a href="../utilisateur/profil.php?email=<?php echo urlencode($u['email']); ?>">
									<button type="button">
										<img alt="profil" src="../photos/profil.png">
									</button>
								</a>
							</td>
						</tr>
						<?php
					}
				}
				?>
			
			</tbody>
			</table>
			<nav>
				<h3>Liens pages</h3>
				<ul>
					<li><a class="lien_bouton" href="admin.php" target="_blank">Admin</a></li>
					<li><a class="lien_bouton" href="../utilisateur/profil.php"  target="_blank">Profil</a></li>
					<li><a class="lien_bouton" href="commande.php"  target="_blank">Commande</a></li>
					<li><a class="lien_bouton" href="livraison.php"  target="_blank">Livraison</a></li>
					<li><a class="lien_bouton" href="../utilisateur/Accueil.php" target="_blank">Accueil</a></li>
					<li><a class="lien_bouton" href="../utilisateur/connexion.html"  target="_blank">Connexion</a></li>
					<li><a class="lien_bouton" href="../utilisateur/inscription.html"  target="_blank">Inscription</a></li>
					<li><a class="lien_bouton" href="../utilisateur/presentation.php"  target="_blank">Présentation</a></li>
					<li><a class="lien_bouton" href="../utilisateur/notation.html" target="_blank">Notation</a></li>
				</ul>
			</nav>
	    </div>


</body>
</html>
