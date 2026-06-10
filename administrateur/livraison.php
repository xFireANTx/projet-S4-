<?php
session_start();
$utilisateur = $_SESSION['client'];
$fichier_commandes = __DIR__ . '/../commandes.json';

// traitement du bouton valider la livraison ou signaler adresse introuvable
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_commande'])) {
    $id_cmd_a_valider = $_POST['id_commande'];
    $action = $_POST['action'] ?? 'livree';

    if (file_exists($fichier_commandes)) {
        $commandes = json_decode(file_get_contents($fichier_commandes), true);
        if (is_array($commandes)) {
            foreach ($commandes as &$cmd) {
                if ($cmd['id'] === $id_cmd_a_valider) {
                    if ($action === 'livree') {
                        $cmd['statut'] = 'livree'; // On passe le statut à "livree"
                    } elseif ($action === 'adresse_introuvable') {
                        $cmd['statut'] = 'adresse_introuvable';
                        $cmd['note_livreur'] = 'Adresse introuvable signalée par le livreur';
                        $cmd['date_note_livreur'] = date('c');
                    }
                    break;
                }
            }
            // On sauvegarde les modifications dans le fichier JSON (avec LOCK_EX)
            file_put_contents($fichier_commandes, json_encode($commandes, JSON_PRETTY_PRINT), LOCK_EX);
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
    <nav>
    <a href="../utilisateur/profil.php">profil</a>
	<?php if($utilisateur['role'] === 'admin' || $utilisateur['role'] === 'restaurateur' ): ?>
        	<a href="commande.php">commande</a>
        	<?php if($utilisateur['role'] === 'admin' ): ?>
        		<a href="admin.php">admin</a>    
        	<?php endif; ?>
        <?php endif; ?>
    </nav>

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
                                     Ouvrir le GPS
                                </a>
                            </td>
                            
                            <td>
                                <?php if($utilisateur['role'] === 'livreur'):?>
                                    <form method="POST" action="livraison.php" style="display:inline;margin:0 4px;">
                                        <input type="hidden" name="id_commande" value="<?= htmlspecialchars($cmd['id']) ?>">
                                        <input type="hidden" name="action" value="livree">
                                        <button type="submit" class="btn-validation-verte" title="Marquer comme livrée">
                                            ✓ Livrée
                                        </button>
                                    </form>

                                    <form method="POST" action="livraison.php" style="display:inline;margin:0 4px;">
                                        <input type="hidden" name="id_commande" value="<?= htmlspecialchars($cmd['id']) ?>">
                                        <input type="hidden" name="action" value="adresse_introuvable">
                                        <button type="submit" class="btn-warning" title="Signaler adresse introuvable" onclick="return confirm('Confirmer : adresse introuvable ?');">
                                             Introuvable
                                        </button>
                                    </form>

                                <?php endif; ?>
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



</body>
</html>
