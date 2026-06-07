<?php
session_start();
$estconnecte = isset($_SESSION['client']);

$fichier_plat = __DIR__ . '/../menu.json';
$liste_plat = [];

if (file_exists($fichier_plat)) {
	$liste_plat = json_decode(file_get_contents($fichier_plat), true);
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
	<div class="bandeau">
		<div class="logo_restaurant"><img class="logo" src="../photos/logo_japindien.png" /></div>
		<div class="bandeau_nom">Le Japindien</div>

		<div class="navigation_droite">
			<button id="bouton-theme" class="lien_bouton" style="background-color: #333;">Mode Sombre</button>

			<div class="bandeau_accueil"><a class="lien_bouton" href="Accueil.php">Accueil</a></div>

			<?php if ($estconnecte): ?>
				<div class="bandeau_moncompte">
					<div class="dropdown">
						<button class="lien_bouton">Mon compte</button>
						<div class="dropdown-content">
							<a href="profil.php">Profil</a>
							<a href="deconnexion.php">Deconnexion</a>
						</div>
					</div>
				</div>
			<?php else: ?>
				<div class="bandeau_moncompte">
					<div class="dropdown">
						<button class="lien_bouton">Mon compte</button>
						<div class="dropdown-content">
							<a href="connexion.html">Connexion</a>
							<a href="inscription.html">Inscription</a>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<div class="bandeau_accueil"><a class="lien_bouton" href="presentation.php">A la carte</a></div>
			<?php if ($estconnecte && $_SESSION['client']['email'] === 'admin@japindien.com'): ?>
				<div class="bandeau_accueil"><a class="lien_bouton" href="../administrateur/admin.php">Admin</a></div>
			<?php else: ?>
				<div class="bandeau_accueil">
					<button id="bouton-panier" class="lien_bouton" onclick="togglePanier()">
						Panier (<span id="panier-compteur">0</span>)
					</button>
    				</div>
			<?php endif; ?>
			
		</div>
	</div>

	<div class="remplissage"></div>
	<br>
	<h2 class="section-titre">Nos signatures</h2>
	<?php if (!empty($liste_plat)): ?>
		<?php
		$compteur = 0;

		foreach ($liste_plat as $plat):
			if ($plat['pays'] === 'spe'):
				if ($compteur % 3 == 0) {
					echo "<div class='conteneur-menu'>";
				}
		?>
				<?php ?>
				<div class="plat">
					<div class="section">
						<div class="image">
							<img src="<?= $plat['image'] ?>" alt="<?= $plat['nom'] ?>">
						</div>
						<div class="contenu-carte">
							<h3 class="titre"><?= $plat['nom'] ?></h3>
							<p class="description"><?= $plat['description'] ?></p>
						</div>
						<div class="bas-carte">
							<div class="bas-gauche"><span class="prix"><?= $plat['prix'] ?>€</span></div>
							<button class="ajouter"
								data-id="<?= $plat['id'] ?>"
								data-nom="<?= $plat['nom'] ?>"
								data-prix="<?= $plat['prix'] ?>">+</button>
						</div>
					</div>
				</div>
		<?php
				$compteur++;
			endif;
			if ($compteur % 3 == 0) {
				echo '</div>';
			}

		endforeach;

		if ($compteur % 3 != 0) {
			echo '</div>';
		}
		?>
	<?php else: ?>
		<p style="text-align: center; width: 100%;">Aucun plat disponible pour le moment.</p>
	<?php endif; ?>

			<div id="panier-volet" class="panier-ferme">
			    	<div class="panier-entete">
					<h3>Mon Panier</h3>
					<button class="bouton-fermer" onclick="togglePanier()">×</button>
			    	</div>
			    
			    	<div id="panier-articles">
				</div>
			    
			    	<div class="panier-total">
					<p>Total : <span id="panier-somme">0.00</span>€</p>
					
					<div style="margin-bottom: 15px; margin-top: 10px;">
						<label for="date-commande" style="display: block; margin-bottom: 5px;">Date de livraison :</label>
						<input type="date" id="date-commande" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
    					</div>
    					
					<div style="margin-bottom: 15px;">
						<label for="heure-commande" style="display: block; margin-bottom: 5px;">Heure souhaitée :</label>
						<input type="time" id="heure-commande" min="10:30" max="22:30" style="width: 100%; padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
					</div>
						<button id="bouton-valider" onclick="validerCommande()">Commander</button>
						<button id="bouton-vider" onclick="viderPanier()">Vider le panier</button>
				</div>
			</div>

	<script src="mode_sombre.js"></script>
	<script src="panier.js"></script>
</body>

</html>
