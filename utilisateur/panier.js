let panier = JSON.parse(localStorage.getItem('panier')) || [];

document.addEventListener("DOMContentLoaded", () => {
	mettreAJourAffichage();
  //Pour éviter qu'on entre une date antérieur
	const champDate = document.getElementById('date-commande');
	if (champDate) {
		const dateAjd = new Date().toISOString().split('T')[0];
		champDate.setAttribute('min', dateAjd);
	}
  
	document.querySelectorAll('.ajouter').forEach(bouton => {
		bouton.addEventListener('click', () => {
			const id = bouton.getAttribute('data-id') ;
			const nom = bouton.getAttribute('data-nom');
			let prixdata = bouton.getAttribute('data-prix');
			
			const prixFinal = parseFloat(String(prixdata).replace(',', '.').replace('€', '').trim());
			ajouterAuPanier(id, nom, prixFinal);
			});
		});
});

function togglePanier() {
    const volet = document.getElementById('panier-volet');
    volet.classList.toggle('panier-ouvert');
}

function ajouterAuPanier(id, nom, prix) {
    const articleExistant = panier.find(item => item.id === id);

    if (articleExistant) {
        articleExistant.quantite += 1;
    } else {
        panier.push({ id, nom, prix, quantite: 1 });
    }

    sauvegarderEtActualiser();
}

// Dans panier.js -> fonction validerCommande()

const idCommandeModif = localStorage.getItem('modif_commande_id') || '';

fetch('traitement_commande.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        panier: panier,
        date: dateChoisie,
        heure: heureChoisie,
        id_commande_modif: idCommandeModif // On envoie l'ID ici
    })
})
.then(response => response.json())
.then(data => {
    if (!data.success) {
        alert("Erreur : " + data.message);
        return;
    }

    // On nettoie le flag de modification
    localStorage.removeItem('modif_commande_id');
    localStorage.removeItem('panier');

    // SI le nouveau total est supérieur -> Redirection vers CY Bank pour la différence
    if (data.cybank) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'https://www.plateforme-smc.fr/cybank/index.php';

        // ... (votre logique actuelle pour ajouter les champs cachés paiement.transaction, montant, etc.) ...
        
        document.body.appendChild(form);
        form.submit();
    } else {
        // SI le total est inférieur ou égal -> Pas besoin de payer, succès direct !
        alert("Votre commande a été modifiée avec succès !");
        window.location.href = 'profil.php';
    }
});

function modifierQuantite(id, action) {
    const article = panier.find(item => item.id === id);
    if (!article) return;

    if (action === 'plus') {
        article.quantite += 1;
    } else if (action === 'moins') {
        article.quantite -= 1;
        if (article.quantite <= 0) {
            panier = panier.filter(item => item.id !== id);
        }
    }
    sauvegarderEtActualiser();
}

function viderPanier() {
    panier = [];
    sauvegarderEtActualiser();
}

function sauvegarderEtActualiser() {
    localStorage.setItem('panier', JSON.stringify(panier));
    mettreAJourAffichage();
}

function mettreAJourAffichage() {
	const conteneurArticles = document.getElementById('panier-articles');
	const compteur = document.getElementById('panier-compteur');
	const somme = document.getElementById('panier-somme');
    
	conteneurArticles.innerHTML = '';
	let totalPrix = 0;
	let totalArticles = 0;
  
	panier.forEach(item => {
		totalPrix += item.prix * item.quantite;
		totalArticles += item.quantite;

		conteneurArticles.innerHTML +=`
		    <div class="article-panier">
		        <div style="width: 300px">
		            <strong>${item.nom}</strong><br>
		            <small>${item.prix.toFixed(2)}€ x ${item.quantite}</small>
		        </div>
		        <div style="width: 90px">
		            <button  onclick="modifierQuantite('${item.id}', 'moins')">-</button>
		            <button  onclick="modifierQuantite('${item.id}', 'plus')">+</button>
		        </div>
		    </div>
		`;
	    });

	    compteur.textContent = totalArticles;
	    somme.textContent = totalPrix.toFixed(2);
}


function validerCommande() {
    if (panier.length === 0) {
        alert("Votre panier est vide !");
        return;
    }
    
    const champDate = document.getElementById('date-commande');
    const champHeure = document.getElementById('heure-commande');
    const boutonValider = document.getElementById('bouton-valider');
    
    const dateChoisie = champDate.value;
    const heureChoisie = champHeure.value;
    
    if(!dateChoisie || !heureChoisie){
        alert("Veuillez choisir une date et une heure pour votre commande.");
        return;
    }

    // Désactiver le bouton pendant le chargement
    boutonValider.disabled = true;
    boutonValider.textContent = "Commande en cours...";

    fetch('traitement_commande.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            panier: panier,
            date: dateChoisie,
            heure: heureChoisie
        })
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert("Erreur : " + (data.message || "Veuillez vous reconnecter."));
                return;
            }

            // --- AJOUT ICI : Si le paiement n'est pas requis (modification moins chère) ---
            if (data.cybank === null) {
                alert("Modification enregistrée avec succès (aucun paiement supplémentaire requis) !");
                localStorage.removeItem('panier'); // On vide le panier local
                window.location.href = "profil.php"; // Redirection vers le profil
                return;
            }
            // -----------------------------------------------------------------------------

            const paiement = data.cybank;

            // Création du formulaire demandé par CY Bank
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'https://www.plateforme-smc.fr/cybank/index.php';

        function ajouterChamp(nom, valeur) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = nom;
            input.value = valeur;
            form.appendChild(input);
        }

        ajouterChamp('transaction', paiement.transaction);
        ajouterChamp('montant', paiement.montant);
        ajouterChamp('vendeur', paiement.vendeur);
        ajouterChamp('retour', paiement.retour);
        ajouterChamp('control', paiement.control);

        document.body.appendChild(form);

        // Envoi vers CY Bank
        form.submit();
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert("Une erreur de connexion est survenue.");
    })
    .finally(() => {
        // Réactiver le bouton
        boutonValider.disabled = false;
        boutonValider.textContent = "Commander";
    });
}


function validerCommande() {
    if (panier.length === 0) {
        alert("Votre panier est vide !");
        return;
    }

    const champDate = document.getElementById('date-commande');
    const champHeure = document.getElementById('heure-commande');
    const boutonValider = document.getElementById('bouton-valider');

    const dateChoisie = champDate.value;
    const heureChoisie = champHeure.value;

    if (!dateChoisie || !heureChoisie) {
        alert("Veuillez choisir une date et une heure pour votre commande.");
        return;
    }

    // VÉRIFICATION DE LA DATE ET HEURE DANS LE PASSÉ 
    const maintenant = new Date();
    // Crée un objet Date complet à partir des inputs (ex: "2026-06-10T15:30")
    const dateCommande = new Date(`${dateChoisie}T${heureChoisie}`);

    if (dateCommande < maintenant) {
        alert("La date ou l'heure choisie est déjà passée. Veuillez sélectionner un horaire futur !");
        return;
    }

    // Désactiver le bouton pendant le chargement
    boutonValider.disabled = true;
    boutonValider.textContent = "Commande en cours...";

    const idCommandeModif = localStorage.getItem('modif_commande_id') || '';

    fetch('traitement_commande.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            panier: panier,
            date: dateChoisie,
            heure: heureChoisie,
            id_commande_modif: idCommandeModif
        })
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert("Erreur : " + (data.message || "Veuillez vous reconnecter."));
                return;
            }
            // Si aucun paiement requis
            if (data.cybank === null) {
                alert("Modification enregistrée avec succès (aucun paiement supplémentaire requis) !");
                localStorage.removeItem('panier');
                localStorage.removeItem('modif_commande_id');
                window.location.href = "profil.php";
                return;
            }

            const paiement = data.cybank;

            // Création du formulaire demandé par CY Bank
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'https://www.plateforme-smc.fr/cybank/index.php';

            function ajouterChamp(nom, valeur) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = nom;
                input.value = valeur;
                form.appendChild(input);
            }

            ajouterChamp('transaction', paiement.transaction);
            ajouterChamp('montant', paiement.montant);
            ajouterChamp('vendeur', paiement.vendeur);
            ajouterChamp('retour', paiement.retour);
            ajouterChamp('control', paiement.control);

            document.body.appendChild(form);

            // Envoi vers CY Bank
            form.submit();
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert("Une erreur de connexion est survenue.");
        })
        .finally(() => {
            // Réactiver le bouton
            boutonValider.disabled = false;
            boutonValider.textContent = "Commander";
        });
}
