<?php
session_start();
$utilisateur = $_SESSION['client'];
// On force le fuseau horaire français pour éviter les décalages d'heure du serveur
date_default_timezone_set('Europe/Paris');

$fichier_commandes = __DIR__ . '/../commandes.json';
$commandes = []; // Initialisation d'un tableau vide par sécurité

// Sécurité anti-warning : vérification du fichier json
if (file_exists($fichier_commandes)) {
    $contenu = file_get_contents($fichier_commandes);
    if (!empty(trim($contenu))) { 
        $donnees = json_decode($contenu, true);
        if (is_array($donnees)) {
            $commandes = $donnees;
        }
    }
}

// gestion du bouton
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_pret'])) {
    $id_a_mettre_pret = $_POST['id_pret'];
    foreach ($commandes as &$c) {
        if ($c['id'] === $id_a_mettre_pret) {
            $c['statut'] = 'en_livraison';
        }
    }
    file_put_contents($fichier_commandes, json_encode($commandes, JSON_PRETTY_PRINT));
    header("Location: commande.php");
    exit;
}

// separation des commandes
$commandes_urgentes = [];
$commandes_futures = [];
$commandes_en_livraison = []; // Nouveau tableau : sur la route
$commandes_livrees = [];

$maintenant = time(); // Heure actuelle
$limite_2h = $maintenant + (2 * 3600); // Heure actuelle + 2 heures

foreach ($commandes as $cmd) {
    if (isset($cmd['statut'])) {
        // Commandes en cuisine
            if ($cmd['statut'] === 'en_cours' || strpos($cmd['statut'], 'en_attente') === 0) {
            $temps_commande = strtotime($cmd['date_livraison'] . ' ' . $cmd['heure_livraison']);
            
            if ($temps_commande <= $limite_2h) {
                $commandes_urgentes[] = $cmd;
            } else {
                $commandes_futures[] = $cmd;
            }
        } 
        // Commandes remises au livreur
        elseif ($cmd['statut'] === 'en_livraison') {
            $commandes_en_livraison[] = $cmd;
        }
        // Commandes terminées
        elseif ($cmd['statut'] === 'livree') {
            $commandes_livrees[] = $cmd;
        }
    }
}

// tri chronologique
$fonction_tri = function($a, $b) {
    return strtotime($a['date_livraison'] . ' ' . $a['heure_livraison']) - strtotime($b['date_livraison'] . ' ' . $b['heure_livraison']);
};

$fonction_tri_archive = function($a, $b) {
    return strtotime($b['date_livraison'] . ' ' . $b['heure_livraison']) - strtotime($a['date_livraison'] . ' ' . $a['heure_livraison']);
};

usort($commandes_urgentes, $fonction_tri);
usort($commandes_futures, $fonction_tri);
usort($commandes_en_livraison, $fonction_tri); // On trie aussi celles en livraison
usort($commandes_livrees, $fonction_tri_archive);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="administrateur.css">
    <meta charset="UTF-8">
    <title>Gestion des Commandes</title>
</head>
<body>
<nav style="margin-top: 40px;">
	<?php if($utilisateur['role'] === 'admin' || $utilisateur['role'] === 'restaurateur' ): ?>
        	<a href="../utilisateur/profil.php">profil</a>
        	<?php if($utilisateur['role'] === 'admin' ): ?>
        		<a href="admin.php">admin</a>    
        		<a href="livraison.php">livraison</a> 
        	<?php endif; ?>
        <?php endif; ?>  
</nav>
<div style="margin-bottom: 40px;">
    <h2 style="color: #d9534f;"> Commandes Urgentes (Cuisine)</h2>
    <table border="1" style="width:100%; text-align:center; border-collapse: collapse;">
        <tr style="background-color: #f2dede;">
            <th>ID Commande</th>
            <th>Client</th>
            <th>Date & Heure Livraison</th>
            <th>Détail (Quantité x Plat)</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
        <?php if (empty($commandes_urgentes)): ?>
            <tr><td colspan="6">Aucune commande urgente.</td></tr>
        <?php else: ?>
            <?php foreach ($commandes_urgentes as $cmd): ?>
                <tr>
                    <td><?= htmlspecialchars(substr($cmd['id'], -5)) ?></td>
                    <td><?= htmlspecialchars($cmd['client_nom']) ?></td>
                    <td><strong><?= htmlspecialchars($cmd['date_livraison'] . ' à ' . $cmd['heure_livraison']) ?></strong></td>
                    <td>
                        <?php foreach ($cmd['panier'] as $item): ?>
                            <?= $item['quantite'] ?>x <?= htmlspecialchars($item['nom']) ?><br>
                        <?php endforeach; ?>
                    </td>
                    <td><?= $cmd['total'] ?>€</td>
                    <td>
                    
		<?php if($utilisateur['role'] === 'restaurateur'):?>
                        <form method="POST" style="margin: 0;">
                            <input type="hidden" name="id_pret" value="<?= $cmd['id'] ?>">
                            <button type="submit" style="cursor:pointer; background-color:#FF9800; color:white; border:none; padding:6px 12px; border-radius:3px; font-weight:bold;">
                                Prêt ➔
                            </button>
                        </form>
                 <?php endif ; ?>
                 
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>

<div style="margin-bottom: 40px;">
    <h2 style="color: #5bc0de;"> Commandes à venir (Cuisine)</h2>
    <table border="1" style="width:100%; text-align:center; border-collapse: collapse;">
        <tr style="background-color: #d9edf7;">
            <th>ID Commande</th>
            <th>Client</th>
            <th>Date & Heure Livraison</th>
            <th>Détail (Quantité x Plat)</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
        <?php if (empty($commandes_futures)): ?>
            <tr><td colspan="6">Aucune commande future prévue pour le moment.</td></tr>
        <?php else: ?>
            <?php foreach ($commandes_futures as $cmd): ?>
                <tr>
                    <td><?= htmlspecialchars(substr($cmd['id'], -5)) ?></td>
                    <td><?= htmlspecialchars($cmd['client_nom']) ?></td>
                    <td><strong><?= htmlspecialchars($cmd['date_livraison'] . ' à ' . $cmd['heure_livraison']) ?></strong></td>
                    <td>
                        <?php foreach ($cmd['panier'] as $item): ?>
                            <?= $item['quantite'] ?>x <?= htmlspecialchars($item['nom']) ?><br>
                        <?php endforeach; ?>
                    </td>
                    <td><?= $cmd['total'] ?>€</td>
                    <td>
                    
                    <?php if($utilisateur['role'] === 'restaurateur'):?>
                        <form method="POST" style="margin: 0;">
                            <input type="hidden" name="id_pret" value="<?= $cmd['id'] ?>">
                            <button type="submit" style="cursor:pointer; background-color:#FF9800; color:white; border:none; padding:6px 12px; border-radius:3px; font-weight:bold;">
                                Prêt ➔
                            </button>
                        </form>
                     <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>

<hr style="margin: 30px 0; border: 1px solid #ccc;">

<div style="margin-bottom: 40px;">
    <h2 style="color: #28a745;"> En cours de livraison (Livreurs)</h2>
    <table border="1" style="width:100%; text-align:center; border-collapse: collapse;">
        <tr style="background-color: #d4edda; color: #155724;">
            <th>ID Commande</th>
            <th>Client</th>
            <th>Date & Heure Livraison</th>
            <th>Détail (Quantité x Plat)</th>
            <th>Total</th>
        </tr>
        <?php if (empty($commandes_en_livraison)): ?>
            <tr><td colspan="5">Aucune commande actuellement sur la route.</td></tr>
        <?php else: ?>
            <?php foreach ($commandes_en_livraison as $cmd): ?>
                <tr style="background-color: #f8fff9;">
                    <td><?= htmlspecialchars(substr($cmd['id'], -5)) ?></td>
                    <td><?= htmlspecialchars($cmd['client_nom']) ?></td>
                    <td><?= htmlspecialchars($cmd['date_livraison'] . ' à ' . $cmd['heure_livraison']) ?></td>
                    <td>
                        <?php foreach ($cmd['panier'] as $item): ?>
                            <?= $item['quantite'] ?>x <?= htmlspecialchars($item['nom']) ?><br>
                        <?php endforeach; ?>
                    </td>
                    <td><?= $cmd['total'] ?>€</td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>

<div>
    <h2 style="color: #6c757d;"> Historique des Commandes Livrées</h2>
    <table border="1" style="width:100%; text-align:center; border-collapse: collapse;">
        <tr style="background-color: #e2e3e5; color: #383d41;">
            <th>ID Commande</th>
            <th>Client</th>
            <th>Date & Heure Livraison</th>
            <th>Détail (Quantité x Plat)</th>
            <th>Total</th>
        </tr>
        <?php if (empty($commandes_livrees)): ?>
            <tr><td colspan="5">Aucune commande livrée pour le moment.</td></tr>
        <?php else: ?>
            <?php foreach ($commandes_livrees as $cmd): ?>
                <tr style="color: #6c757d; background-color: #f8f9fa;">
                    <td><?= htmlspecialchars(substr($cmd['id'], -5)) ?></td>
                    <td><?= htmlspecialchars($cmd['client_nom']) ?></td>
                    <td><?= htmlspecialchars($cmd['date_livraison'] . ' à ' . $cmd['heure_livraison']) ?></td>
                    <td>
                        <?php foreach ($cmd['panier'] as $item): ?>
                            <?= $item['quantite'] ?>x <?= htmlspecialchars($item['nom']) ?><br>
                        <?php endforeach; ?>
                    </td>
                    <td><?= $cmd['total'] ?>€</td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</div>


</body>
</html>
