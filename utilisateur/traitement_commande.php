<?php
session_start();
header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['client'])) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit;
}

// Récupérer les données envoyées par panier.js
$donnees_json = file_get_contents('php://input');
$donnees = json_decode($donnees_json, true);

if (!$donnees || empty($donnees['panier'])) {
    echo json_encode(['success' => false, 'message' => 'Panier vide']);
    exit;
}

$email_client = $_SESSION['client']['email'];
$total = 0;
$articles_resume = [];

// Calcul du total et résumé pour le profil
foreach ($donnees['panier'] as $item) {
    $total += $item['prix'] * $item['quantite'];
    $articles_resume[] = $item['quantite'] . 'x ' . $item['nom'];
}

$id_commande_unique = uniqid(); // ID unique pour la commande

$nouvelle_commande = [
    'id' => uniqid(), 
    'client_email' => $email_client,
    'client_nom' => $_SESSION['client']['nom'] . ' ' . $_SESSION['client']['prenom'],
    'client_adresse' => $_SESSION['client']['adresse'] ?? 'Adresse non renseignée',
    'client_phone' => $_SESSION['client']['phone'] ?? 'Non renseigné',
    'date_livraison' => $donnees['date'],
    'heure_livraison' => $donnees['heure'],
    'panier' => $donnees['panier'],
    'total' => $total,
    'statut' => 'en_attente' // 
];

//Sauvegarder dans commandes.json (pour l'admin)
$fichier_commandes = __DIR__ . '/../commandes.json';
$toutes_commandes = file_exists($fichier_commandes) ? json_decode(file_get_contents($fichier_commandes), true) : [];
$toutes_commandes[] = $nouvelle_commande;
file_put_contents($fichier_commandes, json_encode($toutes_commandes, JSON_PRETTY_PRINT));

// 2. Mettre à jour l'historique dans utilisateurs.json (pour le profil)
$fichier_users = __DIR__ . '/../utilisateurs.json';
$utilisateurs = json_decode(file_get_contents($fichier_users), true);

foreach ($utilisateurs as &$u) {
	if ($u['email'] === $email_client) {
		if (!isset($u['orders'])) {
			$u['orders'] = [];
		}
        // Ajouter la commande simplifiée pour le profil
		$u['orders'][] = [	
			'id' => $id_commande_unique,	
			'date' => $donnees['date'] . ' à ' . $donnees['heure'],
			'total' => $total,
			'items' => $articles_resume
		];
		$_SESSION['client'] = $u; // Mettre à jour la session
		break;
    }
}
file_put_contents($fichier_users, json_encode($utilisateurs, JSON_PRETTY_PRINT));

//CY BANK

require_once(__DIR__ . '/../getapikey.php');
$vendeur = "MEF-2_C";
$api_key = getAPIKey($vendeur);
$montant_banque = number_format($total, 2, '.' , '');

$url_retour = "http://localhost/utilisateur/retour_paiement.php";

$chaine_hash = $api_key.'#'.$id_commande_unique.'#'.$montant_banque.'#'.$vendeur.'#'.$url_retour.'#';
$cle_control = md5($chaine_hash);

echo json_encode([
	'success' => true, 
	'cybank'=> [
		'transaction' => $id_commande_unique,
		'montant' => $montant_banque,
		'vendeur' => $vendeur, 
		'retour' => $url_retour,
		'control' => $cle_control
	]
]);
?>
