<?php
	session_start();
	if(!isset($_SESSION['client']) || $_SESSION['client']['role'] !== "admin"){
		header("Location: ../utilisateur/Accueil.php");
	}
	$fichier = '../utilisateurs.json';
	$utilisateurs =[];
	
	if(file_exists($fichier)){
		$utilisateurs = json_decode(file_get_contents($fichier), true);
	}
	// stats des plats
	$fichier_commandes = __DIR__ . '/../commandes.json';
	$stats_plats = [];
	if (file_exists($fichier_commandes)) {
		$commandes = json_decode(file_get_contents($fichier_commandes), true);
		if (is_array($commandes)) {
			foreach ($commandes as $cmd) {
				// les commandes qui ont reçu une note
				if (isset($cmd['note_produit']) && !empty($cmd['note_produit'])) {
					$note = floatval($cmd['note_produit']);
					// Récupération des plats
					$items = $cmd['items'] ?? $cmd['panier'] ?? [];
					if (is_array($items)) {
						foreach ($items as $item) {
							// Nettoyage pour récupérer uniquement le nom du plat
							$nom_plat = is_string($item) ? preg_replace('/^\d+x\s*/', '', $item) : ($item['nom'] ?? '');
							if (!empty($nom_plat)) {
								if (!isset($stats_plats[$nom_plat])) {
									$stats_plats[$nom_plat] = ['somme' => 0, 'count' => 0];
								}
								$stats_plats[$nom_plat]['somme'] += $note;
								$stats_plats[$nom_plat]['count'] += 1;
							}
						}
					}
				}
			}
		}
	}

	// Calcul des moyennes et tri du meilleur au moins bon
	$plats_classement = [];
	foreach ($stats_plats as $nom => $data) {
		$plats_classement[] = [
			'nom' => $nom,
			'moyenne' => round($data['somme'] / $data['count'], 2),
			'votes' => $data['count']
		];
	}
	// Tri décroissant sur la note moyenne
	usort($plats_classement, function($a, $b) {
		return $b['moyenne'] <=> $a['moyenne'];
	});
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
					<li><a class="lien_bouton" href="../utilisateur/notation.php" target="_blank">Notation</a></li>
				</ul>
			</nav>
	    </div>
		<br><br>
    <h1> Classement des plats les mieux notés</h1>
    <div class="tableau" style="margin-bottom: 5px;">
        <br>
        <table border="1" style="width:100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th><u>Nom du Plat</u></th>
                    <th><u>Note Moyenne</u></th>
                    <th><u>Nombre d'avis</u></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($plats_classement)): ?>
                    <?php foreach ($plats_classement as $p): ?>
                        <tr align="center">
                            <td><strong><?= htmlspecialchars($p['nom']) ?></strong></td>
                            <td style="color: #ff9800; font-weight: bold; font-size: 1.1em;">
                                <?= htmlspecialchars($p['moyenne']) ?> / 5 ★
                            </td>
                            <td><?= htmlspecialchars($p['votes']) ?> évaluation(s)</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" align="center" style="padding: 20px; color: #666;">
                            Aucune évaluation de plat enregistrée pour le moment.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
