<?php
session_start();

$fichier_commandes = __DIR__ . '/../commandes.json';

//traitement du bouton valider la livraison
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_commande'])) {
    $id_cmd_a_valider = $_POST['id_commande'];
    
    if (file_exists($fichier_commandes)) {
        $commandes = json_decode(file_get_contents($fichier_commandes), true);
        if (is_array($commandes)) {
            foreach ($commandes as &$cmd) {
                if ($cmd['id'] === $id_cmd_a_valider) {
                    $cmd['statut'] = 'livree'; // On passe le statut à "livrée"
                    break;
                }
            }
            // On sauvegarde les modifications dans le fichier JSON
            file_put_contents($fichier_commandes, json_encode($commandes, JSON_PRETTY_PRINT));
        }
    }
    
    // On recharge la page proprement pour rafraîchir la liste
    header("Location: livraison.php");
    exit;
}

//recuperation des commendes en cours de livraison
$commandes_a_livrer = [];
if (file_exists($fichier_commandes)) {
    $commandes = json_decode(file_get_contents($fichier_commandes), true);
    if (is_array($commandes)) {
        foreach ($commandes as $cmd) {
            // Le livreur ne voit que les commandes qui ont été marquées comme "Prêtes" (en_livraison)
            if (isset($cmd['statut']) && $cmd['statut'] === 'en_livraison') {
                $commandes_a_livrer[] = $cmd;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" type="text/css" href="administrateur.css">
    <meta charset="UTF-8">
    <title>Espace Livraison - Le Japindien</title>

</head>
<body>

    <div class="tableau_livraison" style="padding: 20px;">
        <h2>Commandes à livrer</h2>
        <br>
        <table border="1" style="width:100%; border-collapse: collapse; text-align: center;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th>Num Commande</th>
                    <th>Adresse de destination</th>
                    <th>Téléphone</th>
                    <th>Itinéraire</th>
                    <th>État de la livraison</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($commandes_a_livrer)): ?>
                    <?php foreach ($commandes_a_livrer as $cmd): ?>
                        <?php 
                        $adresse_client = $cmd['client_adresse'] ?? 'Adresse non spécifiée';
                        // Lien universel vers les applications GPS
                        $url_navigation = "http://maps.google.com/?q=" . urlencode($adresse_client);
                        ?>
                        <tr>
                            <td>#<?= htmlspecialchars(substr($cmd['id'], -5)) ?></td>
                            <td><?= htmlspecialchars($adresse_client) ?></td>
                            <td><?= htmlspecialchars($cmd['client_telephone'] ?? 'Non spécifié') ?></td>
                            <td>
                                <a href="<?= $url_navigation ?>" target="_blank" class="btn-nav">
                                    🗺️ Ouvrir le GPS
                                </a>
                            </td>
                            <td>
                                <form method="POST" action="livraison.php" style="margin:0;">
                                    <input type="hidden" name="id_commande" value="<?= htmlspecialchars($cmd['id']) ?>">
                                    <button type="submit" class="btn-validation-verte" title="Marquer comme livrée">
                                        ✓ Livrée
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="padding: 20px; color: #666;">
                            Aucune livraison à faire pour le moment !
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <nav style="margin-top: 40px;">
        <a href="../utilisateur/profil.php">profil</a>
        <a href="commande.php">commande</a>
        <a href="livraison.php">livraison</a>
        <a href="admin.php">admin</a>    
    </nav>

</body>
</html>