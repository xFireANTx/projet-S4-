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
		        <div div style="width: 90px">
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
        if(data.success) {
            alert("Commande enregistrée pour le " + dateChoisie + " à " + heureChoisie + ".");
            viderPanier();
            togglePanier();
            champDate.value = '';
            champHeure.value = '';
        } else {
            alert("Erreur : " + (data.message || "Veuillez vous reconnecter."));
        }
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