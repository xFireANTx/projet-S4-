<?php

// 
// 
// 
// 
// 
// pour le gps du livreur on peut utiliser ca 
// 
// partie php
// $adresse = urlencode("Tour Eiffel, Paris");
// $google_maps_url = "https://www.google.com/maps/search/?api=1&query=" . $adresse;
// $waze_url = "https://waze.com/ul?q=" . $adresse;
// 
// partie html
/* <a href="<?php echo $google_maps_url; ?>">Y aller avec Google Maps</a>
// <a href="<?php echo $waze_url; ?>">Y aller avec Waze</a> */
// 
// 
// 
// 
// 
// 
// 
// 
// 
// 
// 
// 
// 
// 
// 

$fichier_commandes = __DIR__ . '/../commandes.json';
$commandes = file_exists($fichier_commandes) ? json_decode(file_get_contents($fichier_commandes), true) : [];

// GESTION DU BOUTON "MARQUER COMME LIVRÉE"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_livraison'])) {
    $id_a_valider = $_POST['id_livraison'];
    foreach ($commandes as &$c) {
        if ($c['id'] === $id_a_valider) {
            $c['statut'] = 'livree'; // Clôture définitivement la commande
        }
    }
    file_put_contents($fichier_commandes, json_encode($commandes, JSON_PRETTY_PRINT));
    header("Location: livraison.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" type="text/css" href="administrateur.css">
    <meta charset="UTF-8">
    <title>Livraisons en cours</title>
</head>
<body>

    <div class="tableau_livraison">
        <h2>Commandes prêtes (En cours de livraison)</h2>
        <br>
        <table border="1" style="width:100%; text-align:center;">
        <tr>
            <th><u>Nom du client</u></th>
            <th><u>Adresse</u></th>
            <th><u>Numéro de téléphone</u></th>
            <th><u>Heure prévue</u></th>
            <th><u>Etat de la livraison</u></th>
        </tr>
        <?php foreach ($commandes as $cmd): ?>
            <?php if (isset($cmd['statut']) && $cmd['statut'] === 'en_livraison'): ?>
            <tr>
                <td><?= htmlspecialchars($cmd['client_nom']) ?></td>
                <td><?= htmlspecialchars($cmd['client_adresse']) ?></td>
                <td><?= htmlspecialchars($cmd['client_phone']) ?></td>
                <td><?= htmlspecialchars($cmd['heure_livraison']) ?></td>
                <td>
                    <form method="POST" style="margin: 0;">
                        <input type="hidden" name="id_livraison" value="<?= $cmd['id'] ?>">
                        <button type="submit" style="cursor:pointer; background-color:#4CAF50; color:white; border:none; padding:6px 12px; border-radius:3px;">
                            Marquer comme livrée ✓
                        </button>
                    </form>
                </td>
            </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        </table>
    </div>

    <nav>
        <a href="../utilisateur/profil.php">profil</a>
        <a href="commande.php">commande</a>
        <a href="livraison.php">livraison</a>
        <a href="admin.php">admin</a>    
    </nav>

</body>
</html>