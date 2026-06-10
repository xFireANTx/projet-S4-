<?php
session_start();

// Vérification de sécurité : l'utilisateur doit être connecté
if (!isset($_SESSION['client'])) {
    header("Location: connexion.html");
    exit;
}

// Récupération de l'ID de la commande à noter
$id_commande = $_GET['id'] ?? $_POST['id_commande'] ?? '';

if (empty($id_commande)) {
    die("Erreur : ID de commande manquant.");
}

// TRAITEMENT DU FORMULAIRE LORS DU CLIC SUR SOUMETTRE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating_livraison = $_POST['rating_livraison'] ?? null;
    $rating_produit = $_POST['rating_produit'] ?? null;

    $fichier_commandes = __DIR__ . '/../commandes.json';
    
    if (file_exists($fichier_commandes)) {
        $commandes = json_decode(file_get_contents($fichier_commandes), true);
        
        if (is_array($commandes)) {
            foreach ($commandes as &$cmd) {
                // On cherche la bonne commande pour y injecter les notes
                if ($cmd['id'] === $id_commande) {
                    $cmd['note_livraison'] = $rating_livraison;
                    $cmd['note_produit'] = $rating_produit;
                    $cmd['deja_note'] = true; // Flag pour bloquer une seconde notation
                    break;
                }
            }
            // Sauvegarde des modifications
            file_put_contents($fichier_commandes, json_encode($commandes, JSON_PRETTY_PRINT));
        }
    }

    // Retour au profil avec message de succès
    header("Location: profil.php?note=success");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="utilisateur.css">
    <title>Le Japindien - Noter ma commande</title>
    <meta name="author" content="groupe 6" />
    <meta name="description" content="Cette page est la page de notation permettant aux clients de noter le restaurant."/>
    <meta name="keywords" content="notation, client, restaurant"/>
</head>
<body>

<div class="bandeau">
    <div class="logo_restaurant"><img class="logo" src="../photos/logo_japindien.png" /></div>
    <div class="bandeau_nom">Le Japindien</div>
    <div class="navigation_droite">
        <div class="bandeau_accueil"><a class="lien_bouton" href="Accueil.php">Accueil</a></div>
        <div class="bandeau_accueil"><a class="lien_bouton" href="profil.php">Mon Profil</a></div>
    </div>
</div>

<div class="main" style="padding-top: 100px; max-width: 600px; margin: 0 auto;">
    
    <form method="POST" action="notation.php">
        <input type="hidden" name="id_commande" value="<?= htmlspecialchars($id_commande) ?>">

        <div class="rating-container" style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h2>Notez votre expérience de livraison</h2>
            <p style="color: #666; font-size: 0.9em;">Commande #<?= htmlspecialchars(substr($id_commande, -5)) ?></p>
            <div class="star-rating">
                <input type="radio" id="liv5" name="rating_livraison" value="5"><label for="liv5">★</label>
                <input type="radio" id="liv4" name="rating_livraison" value="4"><label for="liv4">★</label>
                <input type="radio" id="liv3" name="rating_livraison" value="3"><label for="liv3">★</label>
                <input type="radio" id="liv2" name="rating_livraison" value="2"><label for="liv2">★</label>
                <input type="radio" id="liv1" name="rating_livraison" value="1"><label for="liv1">★</label>
            </div>
        </div>

        <div class="rating-container" style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h2>Notez la qualité des produits reçus</h2>
            <div class="star-rating">
                <input type="radio" id="prod5" name="rating_produit" value="5"><label for="prod5">★</label>
                <input type="radio" id="prod4" name="rating_produit" value="4"><label for="prod4">★</label>
                <input type="radio" id="prod3" name="rating_produit" value="3"><label for="prod3">★</label>
                <input type="radio" id="prod2" name="rating_produit" value="2"><label for="prod2">★</label>
                <input type="radio" id="prod1" name="rating_produit" value="1"><label for="prod1">★</label>
            </div>
        </div>

        <div class="form-container" style="text-align: center;">
            <button type="submit" class="btn-submit" style="cursor:pointer; background-color:#ff9800; color:white; border:none; padding:12px 30px; border-radius:5px; font-weight:bold; font-size:1em;">
                Soumettre mon avis
            </button>
        </div>
    </form>
</div>

</body>
</html>