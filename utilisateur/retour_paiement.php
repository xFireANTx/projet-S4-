<?php
session_start();
require('getapikey.php');

$vendeur = isset($_GET['vendeur']) ? $_GET['vendeur'] : '';
$transaction = isset($_GET['transaction']) ? $_GET['transaction'] : '';
$montant = isset($_GET['montant']) ? $_GET['montant'] : '';
$control_recu = isset($_GET['control']) ? $_GET['control'] : '';
$statut = $_GET['statut'] ?? $_GET['status'] ?? : '';

$paiement_valide = false;
$message_erreur = "";

if(!empty($vendeur) && !empty($transaction) && !empty($montant) && !empty($control_recu)){
	
	$api_key = getAPIKey($vendeur);
	
	$chaine_validation = $api_key.'#'.$transaction.'#'.$montant.'#'.$vendeur.'#'.$statut.'#';
	$control_calcule = md5($chaine_validation);
	
	if($control_recu === $control_calcule){
		if($statut === 'accepted'){
			$paiement_valide = true;
		} 
		else {
			$message_erreur = "Le paiement a été refusé par la banque.";
		}
	}
	else {
		$message_erreur ="Alerte sécurité : Les données de la transaction ot été modifiées.";
	}
}
else{
	$message_erreur ="Paramètre de transaction manquants.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Retour Paiement - Le Japindien</title>
    <link rel="stylesheet" type="text/css" href="utilisateur.css">
    <style>
        .conteneur-retour { text-align: center; margin-top: 50px; font-family: Arial, sans-serif; }
        .carte-statut { display: inline-block; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); background: #fff; }
        .succes { color: #27ae60; }
        .erreur { color: #e74c3c; }
        .bouton-retour { display: inline-block; margin-top: 20px; padding: 10px 20px; background-color: #333; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>

<body>
<div class="conteneur-retour">
        <div class="carte-statut">
            <?php if ($paiement_valide): ?>
                <h1 class="succes">Paiement Réussi !</h1>
                <p>Merci pour votre commande chez <strong>Le Japindien</strong>.</p>
                <p>Identifiant de transaction : <code><?= htmlspecialchars($transaction) ?></code></p>
                <p>Montant débité : <strong><?= htmlspecialchars($montant) ?> €</strong></p>
                
                <script>
                    localStorage.removeItem('panier');
                </script>

            <?php else: ?>
                <h1 class="erreur"> Échec du paiement</h1>
                <p>Malheureusement, nous n'avons pas pu valider votre règlement.</p>
                <p style="font-style: italic; color: blue;"><?= htmlspecialchars($message_erreur) ?></p>
            <?php endif; ?>
            
            <a class="bouton-retour" href="Accueil.php">Retourner à l'accueil</a>
        </div>
    </div>

</body>
</html>
