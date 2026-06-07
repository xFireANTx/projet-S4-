<?php
session_start();
if(!isset($_SESSION['client'])){
	header("Location: connexion.html");
	exit;
}

$fichier = '../utilisateurs.json';
$donnees = json_decode(file_get_contents($fichier), true);

if(isset($_GET['email']) && $_SESSION['client']['email'] === "admin@japindien.com"){
	$email_client = $_GET['email'];
	$profil_client = null;
	foreach ($donnees as $utilisateur){
		if($utilisateur['email']=== $email_client){
			$profil_client=$utilisateur;
			break;
		}
	}
	if(!$profil_client){
		die("Utilisateur introuvable");
	}
} else {
	$profil_client= $_SESSION['client'];
}

function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES); }
?>

<?php
// Handle AJAX JSON updates posted to this same file
if($_SERVER['REQUEST_METHOD'] === 'POST'){
	header('Content-Type: application/json; charset=utf-8');
	$raw = file_get_contents('php://input');
	$data = json_decode($raw, true);
	if(!$data || !isset($data['field']) || !array_key_exists('value', $data)){
		echo json_encode(['success'=>false,'error'=>'Requête invalide']);
		exit;
	}

	$allowed = ['nom','prenom','phone','adresse'];
	$field = $data['field'];
	$value = trim($data['value']);
	$email = isset($data['email']) ? $data['email'] : $_SESSION['client']['email'];

	if(!in_array($field, $allowed)){
		echo json_encode(['success'=>false,'error'=>'Champ non modifiable']);
		exit;
	}

	if($field === 'phone'){
		if(!preg_match('/^0[0-9]{9}$/', $value)){
			echo json_encode(['success'=>false,'error'=>'Format de téléphone invalide']);
			exit;
		}
	}

	$file = __DIR__ . '/../utilisateurs.json';
	$users = json_decode(file_get_contents($file), true);
	if(!is_array($users)) $users = [];

	$found = false;
	foreach($users as &$u){
		if(isset($u['email']) && $u['email'] === $email){
			if($_SESSION['client']['email'] !== 'admin@japindien.com' && $_SESSION['client']['email'] !== $email){
				echo json_encode(['success'=>false,'error'=>'Permission refusée']);
				exit;
			}
			$u[$field] = $value;
			$found = true;
			break;
		}
	}
	unset($u);

	if(!$found){
		echo json_encode(['success'=>false,'error'=>'Utilisateur introuvable']);
		exit;
	}

	$w = file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
	if($w === false){
		echo json_encode(['success'=>false,'error'=>'Impossible d\'enregistrer']);
		exit;
	}

	if($_SESSION['client']['email'] === $email){
		foreach($users as $u){
			if($u['email'] === $email){
				$_SESSION['client'] = $u; break;
			}
		}
	}

	echo json_encode(['success'=>true,'user'=>array_values(array_filter($users, function($x) use($email){ return $x['email']===$email; }))[0]]);
	exit;
}
?>

<!DOCTYPE html>
<html>

<head>
	<link rel="stylesheet" type="text/css" href="utilisateur.css">
	<meta name="author" content="groupe 6" />
	<meta name="description" content="Page profil utilisateur" />
	<meta name="keywords" content="profil, utilisateur, commandes, fidélité" />
	<style>
	.field { margin:8px 0; }
	.edit-btn { margin-left:8px; cursor:pointer; }
	.small { font-size:0.9em; color:#555 }
	.orders { margin-top:16px; border-top:1px solid #ddd; padding-top:8px }
	.order { padding:8px; border:1px solid #eee; margin-bottom:8px }
	</style>
</head>

<body>
<h2>Profil</h2>

<div id="profile">
	<div class="field"><strong>Nom :</strong>
		<span data-field="nom" class="value"><?php echo h($profil_client['nom']) ?></span>
		<button class="edit-btn" data-field="nom">✏️</button>
	</div>
	<div class="field"><strong>Prénom :</strong>
		<span data-field="prenom" class="value"><?php echo h($profil_client['prenom']) ?></span>
		<button class="edit-btn" data-field="prenom">✏️</button>
	</div>
	<div class="field"><strong>Email :</strong>
		<span><?php echo h($profil_client['email']) ?></span>
	</div>
	<div class="field"><strong>Téléphone :</strong>
		<span data-field="phone" class="value"><?php echo h($profil_client['phone']) ?></span>
		<button class="edit-btn" data-field="phone">✏️</button>
	</div>
	<div class="field"><strong>Adresse :</strong>
		<span data-field="adresse" class="value"><?php echo h($profil_client['adresse']) ?></span>
		<button class="edit-btn" data-field="adresse">✏️</button>
	</div>

	<div class="field"><strong>Compte fidélité :</strong>
		<span class="small" id="loyalty"><?php echo h($profil_client['loyalty'] ?? 0) ?> points</span>
	</div>

	<div class="orders">
		<h3>Anciennes commandes</h3>
		<?php if(!empty($profil_client['orders']) && is_array($profil_client['orders'])): ?>
			<?php foreach($profil_client['orders'] as $o): ?>
				<div class="order">
					<div><strong>Date:</strong> <?php echo h($o['date'] ?? '') ?></div>
					<div><strong>Montant:</strong> <?php echo h($o['total'] ?? '') ?> €</div>
					<div><strong>Articles:</strong> <?php echo h(implode(', ', $o['items'] ?? [])) ?></div>
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<div class="small">Aucune commande trouvée.</div>
		<?php endif; ?>
	</div>

	<div style="margin-top:16px">
		<button onclick="javascript:history.back()">Retour</button>
	</div>
</div>

<script>
document.querySelectorAll('.edit-btn').forEach(btn=>{
	btn.addEventListener('click', e=>{
		const field = btn.dataset.field;
		const span = document.querySelector('span[data-field="'+field+'"]');
		const old = span.textContent;
		const input = document.createElement('input');
		input.type='text'; input.value=old; input.style.marginLeft='8px';
		const save = document.createElement('button'); save.textContent='Save';
		const cancel = document.createElement('button'); cancel.textContent='Cancel';

		span.style.display='none';
		btn.style.display='none';
		btn.parentNode.appendChild(input);
		btn.parentNode.appendChild(save);
		btn.parentNode.appendChild(cancel);

		cancel.addEventListener('click', ()=>{ input.remove(); save.remove(); cancel.remove(); span.style.display='inline'; btn.style.display='inline'; });

		save.addEventListener('click', ()=>{
			const value = input.value;
			// client-side validation for phone
			if(field === 'phone'){
				const re = /^0[0-9]{9}$/;
				if(!re.test(value)){
					alert('Numéro de téléphone invalide. Format attendu : 0XXXXXXXXX');
					return;
				}
			}
			fetch(window.location.pathname, {
				method: 'POST',
				headers: {'Content-Type':'application/json'},
				body: JSON.stringify({ field: field, value: value, email: '<?php echo h($profil_client['email']) ?>' })
			}).then(r=>{
				if(!r.ok){ throw new Error('HTTP '+r.status); }
				return r.json();
			}).then(res=>{
				if(res.success){
					span.textContent = value;
					input.remove(); save.remove(); cancel.remove(); span.style.display='inline'; btn.style.display='inline';
				} else {
					alert(res.error || 'Erreur');
				}
			}).catch(err=>{
				console.error(err);
				alert('Erreur de connexion ou réponse invalide');
			});
		});
	});
});
</script>

</body>
</html>

