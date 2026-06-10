<?php
session_start();
$estconnecte = isset($_SESSION['client']);
$role = $_SESSION['client']['role'] ?? null;

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
			<?php if ($estconnecte): ?>
				<div class="bandeau_accueil">
					<?php if($role === "admin"): ?>
						<a class="lien_bouton" href="../administrateur/admin.php">Admin</a>
					<?php elseif($role === "restaurateur"): ?>
						<a class="lien_bouton" href="../administrateur/commande.php">Commande</a>
					<?php elseif($role === "livreur"): ?>
						<a class="lien_bouton" href="../administrateur/livraison.php">Livreur</a>
					<?php endif; ?>
				</div>
			<?php else: ?>
				<button id="bouton-panier" class="lien_bouton" onclick="togglePanier()">
					Panier (<span id="panier-compteur">0</span>)
				</button>
			<?php endif; ?>
		</div>
	</div>
	<div class="remplissage"></div>


	<div class="filtres-checkbox" style="margin: 20px 40px; font-family: Arial, sans-serif;">
		<p style="font-weight: bold; margin-bottom: 10px; color: #c41e5e;">Filtrer la carte :</p>

		<label style="margin-right: 20px; font-size: 16px; cursor: pointer;">
			<input type="checkbox" name="allergene" value="viande" style="margin-right: 5px; transform: scale(1.2);">
			🥩 Sans viande
		</label>

		<label style="margin-right: 20px; font-size: 16px; cursor: pointer;">
			<input type="checkbox" name="allergene" value="poisson" style="margin-right: 5px; transform: scale(1.2);">
			🐟 Sans poisson
		</label>
	</div>


	<h2 class="section-titre">Nos menus</h2>

	<div class="conteneur-menu">
		<div class="plat carte-formule" data-allergenes="poisson">
			<div class="formule-images">
				<img src="../photos/lassimangue.png" alt="Lassi Mangue">
				<span class="plus">+</span>
				<img src="../photos/sushicurry.png" alt="Maki curry">
				<span class="plus">+</span>
				<img src="../photos/dorayaki.png" alt="Dorayaki">
			</div>

			<div class="formule-details">
				<div class="contenu-carte">
					<h3 class="titre">Menu signatures</h3>
					<p class="description">
						L'expérience japo-indienne a son paroxisme : faites un voyage en inde et au japon en un seul
						repas.
					</p>
					<ul class="liste-formule">
						<li> Lassi Mangue</li>
						<li> Maki curry </li>
						<li> Dorayaki </li>
					</ul>
				</div>

				<div class="bas-carte">
					<div class="bas-gauche">
						<span class="ancien-prix">22,50€</span>
						<span class="prix">20,00€</span>
					</div>
					<button class="ajouter" data-id="1" data-nom="Menu signature" data-prix="20.00">+</button>
				</div>
			</div>
		</div>
	</div>

	<div class="conteneur-menu">
		<div class="plat carte-formule" data-allergenes="viande">
			<div class="formule-images">
				<img src="../photos/mogu.png" alt="Mogu">
				<span class="plus">+</span>
				<img src="../photos/tonkatsu.png" alt="Tonkatsu">
				<span class="plus">+</span>
				<img src="../photos/cheesecake.png" alt="Cheesecake">
			</div>

			<div class="formule-details">
				<div class="contenu-carte">
					<h3 class="titre">Menu japonais</h3>
					<p class="description">
						L'expérience japonaise complète: du plate au dessert en passant par la boisson.
					</p>
					<ul class="liste-formule">
						<li> Mogu</li>
						<li> Tonkatsu </li>
						<li> Cheesecake</li>
					</ul>
				</div>

				<div class="bas-carte">
					<div class="bas-gauche">
						<span class="ancien-prix">25,00€</span>
						<span class="prix">22,00€</span>
					</div>
					<button class="ajouter" data-id="2" data-nom="Menu japonais" data-prix="22.00">+</button>
				</div>
			</div>
		</div>
	</div>

	<div class="conteneur-menu">
		<div class="plat carte-formule" data-allergenes="viande">
			<div class="formule-images">
				<img src="../photos/lassimangue.png" alt="Lassi Mangue">
				<span class="plus">+</span>
				<img src="../photos/poulettikkaa.jpg" alt="Tikka Masala">
				<span class="plus">+</span>
				<img src="../photos/kulfi.png" alt="Kulfi">
			</div>

			<div class="formule-details">
				<div class="contenu-carte">
					<h3 class="titre">Menu indiens</h3>
					<p class="description">
						L'expérience indienne complète: du plate au dessert en passant par la boisson.
					</p>
					<ul class="liste-formule">
						<li> Lassi Mangue</li>
						<li> Tikkamasala </li>
						<li> Kulfi</li>
					</ul>
				</div>

				<div class="bas-carte">
					<div class="bas-gauche">
						<span class="ancien-prix">24,50€</span>
						<span class="prix">22,00€</span>
					</div>
					<button class="ajouter" data-id="3" data-nom="Menu indiens" data-prix="22.00">+</button>
				</div>
			</div>
		</div>
	</div>

	<h2 class="section-titre">Boissons</h2>
	<?php if (!empty($liste_plat)): ?>
		<?php
		$compteur = 0;

		foreach ($liste_plat as $plat):
			if ($plat['type'] === 'boisson'):
				if ($compteur % 3 == 0) {
					echo "<div class='conteneur-menu'>";
				}
		?>
				<?php ?>
				<div class="plat" data-allergenes="<?php echo !empty($plat['allergène']) ? implode(',', $plat['allergène']) : ''; ?>">
					<div class="section">
						<div class="image">
							<img src="<?= $plat['image'] ?>" alt="<?= $plat['nom'] ?>">
						</div>
						<div class="contenu-carte">
							<h3 class="titre"><?= $plat['nom'] ?></h3>
							<p class="description"><?= $plat['description'] ?></p>

							<div class="zone-allergenes">
								<span class="declencheur-allergene" onclick="afficherAllergenes(this)" style="cursor: pointer; color: #71BFD5; font-size: 13px; font-weight: bold; text-decoration: underline;">
									Allergènes
								</span>
								<p class="liste-allergenes" style="display: none; font-size: 12px; color: #D40078; margin-top: 5px;">
									<?php
									if (!empty($plat['allergène'])) {
										// Transforme le tableau d'allergènes en une chaîne de texte séparée par des virgules
										echo implode(', ', $plat['allergène']);
									} else {
										echo "Aucun allergène reconnu.";
									}
									?>
								</p>
							</div>
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
	<?php endif; ?>

	<h2 class="section-titre">
		Nos signatures
	</h2>

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
				<div class="plat" data-allergenes="<?php echo !empty($plat['allergène']) ? implode(',', $plat['allergène']) : ''; ?>">
					<div class="section">
						<div class="image">
							<img src="<?= $plat['image'] ?>" alt="<?= $plat['nom'] ?>">
						</div>
						<div class="contenu-carte">
							<h3 class="titre"><?= $plat['nom'] ?></h3>
							<p class="description"><?= $plat['description'] ?></p>

							<div class="zone-allergenes">
								<span class="declencheur-allergene" onclick="afficherAllergenes(this)" style="cursor: pointer; color: #71BFD5; font-size: 13px; font-weight: bold; text-decoration: underline;">
									Allergènes
								</span>
								<p class="liste-allergenes" style="display: none; font-size: 12px; color: #D40078; margin-top: 5px;">
									<?php
									if (!empty($plat['allergène'])) {
										// Transforme le tableau d'allergènes en une chaîne de texte séparée par des virgules
										echo implode(', ', $plat['allergène']);
									} else {
										echo "Aucun allergène reconnu.";
									}
									?>
								</p>
							</div>
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
	<?php endif; ?>

	<h2 class="section-titre">
		Nos plats <img src="../photos/japon.png" alt="Drapeau Japon" class="flag-icon">
	</h2>
	<?php if (!empty($liste_plat)): ?>
		<?php
		$compteur = 0;

		foreach ($liste_plat as $plat):
			if ($plat['pays'] === 'jap' && $plat['type'] === 'plat'):
				if ($compteur % 3 == 0) {
					echo "<div class='conteneur-menu'>";
				}
		?>
				<?php ?>
				<div class="plat" data-allergenes="<?php echo !empty($plat['allergène']) ? implode(',', $plat['allergène']) : ''; ?>">
					<div class="section">
						<div class="image">
							<img src="<?= $plat['image'] ?>" alt="<?= $plat['nom'] ?>">
						</div>
						<div class="contenu-carte">
							<h3 class="titre"><?= $plat['nom'] ?></h3>
							<p class="description"><?= $plat['description'] ?></p>

							<div class="zone-allergenes">
								<span class="declencheur-allergene" onclick="afficherAllergenes(this)" style="cursor: pointer; color: #71BFD5; font-size: 13px; font-weight: bold; text-decoration: underline;">
									Allergènes
								</span>
								<p class="liste-allergenes" style="display: none; font-size: 12px; color: #D40078; margin-top: 5px;">
									<?php
									if (!empty($plat['allergène'])) {
										// Transforme le tableau d'allergènes en une chaîne de texte séparée par des virgules
										echo implode(', ', $plat['allergène']);
									} else {
										echo "Aucun allergène reconnu.";
									}
									?>
								</p>
							</div>
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
	<?php endif; ?>

	<h2 class="section-titre">
		Nos plats <img src="../photos/inde.png" alt="Drapeau Inde" class="flag-icon">
	</h2>
	<?php if (!empty($liste_plat)): ?>
		<?php
		$compteur = 0;

		foreach ($liste_plat as $plat):
			if ($plat['pays'] === 'ind' && $plat['type'] === 'plat'):
				if ($compteur % 3 == 0) {
					echo "<div class='conteneur-menu'>";
				}
		?>
				<?php ?>
				<div class="plat" data-allergenes="<?php echo !empty($plat['allergène']) ? implode(',', $plat['allergène']) : ''; ?>">
					<div class="section">
						<div class="image">
							<img src="<?= $plat['image'] ?>" alt="<?= $plat['nom'] ?>">
						</div>
						<div class="contenu-carte">
							<h3 class="titre"><?= $plat['nom'] ?></h3>
							<p class="description"><?= $plat['description'] ?></p>

							<div class="zone-allergenes">
								<span class="declencheur-allergene" onclick="afficherAllergenes(this)" style="cursor: pointer; color: #71BFD5; font-size: 13px; font-weight: bold; text-decoration: underline;">
									Allergènes
								</span>
								<p class="liste-allergenes" style="display: none; font-size: 12px; color: #D40078; margin-top: 5px;">
									<?php
									if (!empty($plat['allergène'])) {
										// Transforme le tableau d'allergènes en une chaîne de texte séparée par des virgules
										echo implode(', ', $plat['allergène']);
									} else {
										echo "Aucun allergène reconnu.";
									}
									?>
								</p>
							</div>
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
	<?php endif; ?>
	<h2 class="section-titre">
		Nos desserts
	</h2>

	<?php if (!empty($liste_plat)): ?>
		<?php
		$compteur = 0;

		foreach ($liste_plat as $plat):
			if ($plat['type'] === 'dessert'):
				if ($compteur % 3 == 0) {
					echo "<div class='conteneur-menu'>";
				}
		?>
				<?php ?>
				<div class="plat" data-allergenes="<?php echo !empty($plat['allergène']) ? implode(',', $plat['allergène']) : ''; ?>">
					<div class="section">
						<div class="image">
							<img src="<?= $plat['image'] ?>" alt="<?= $plat['nom'] ?>">
						</div>
						<div class="contenu-carte">
							<h3 class="titre"><?= $plat['nom'] ?></h3>
							<p class="description"><?= $plat['description'] ?></p>

							<div class="zone-allergenes">
								<span class="declencheur-allergene" onclick="afficherAllergenes(this)" style="cursor: pointer; color: #71BFD5; font-size: 13px; font-weight: bold; text-decoration: underline;">
									Allergènes
								</span>
								<p class="liste-allergenes" style="display: none; font-size: 12px; color: #D40078; margin-top: 5px;">
									<?php
									if (!empty($plat['allergène'])) {
										// Transforme le tableau d'allergènes en une chaîne de texte séparée par des virgules
										echo implode(', ', $plat['allergène']);
									} else {
										echo "Aucun allergène reconnu.";
									}
									?>
								</p>
							</div>
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
	<script src="filtre_allergenes.js"></script>
	<script src="panier.js"></script>
	<script>
		function afficherAllergenes(elementClique) {
			//On cherche le paragraphe des allergènes qui se trouve juste à côté du texte cliqué
			const liste = elementClique.nextElementSibling;

			//Si la liste est cachée, on l'affiche, sinon on la cache
			if (liste.style.display === "none") {
				liste.style.display = "block";
			} else {
				liste.style.display = "none";
			}
		}
	</script>
</body>

</html>
