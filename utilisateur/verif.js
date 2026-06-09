function toggleMdp(idChamp) {
    const champ = document.getElementById(idChamp);

    if (champ.type === "password") {
        champ.type = "text";
    } else {
        champ.type = "password";
    }
}





const formulaire = document.querySelector('.form_inscri');

formulaire.addEventListener('submit', function (event) {

    const nom = document.getElementById('Nom').value.trim();
    const prenom = document.getElementById('Prenom').value.trim();
    const phone = document.getElementById('Phone').value.trim();
    const email = document.getElementById('Mail').value.trim();
    const mdp = document.getElementById('Mdp').value;
    const confirmationMdp = document.getElementById('Confirmation mdp').value;

    let erreurs = [];

    const regexLettres = /^[a-zA-ZÀ-ÿ\s\-]+$/;
    if (!regexLettres.test(nom)) {
        erreurs.push("Le nom ne doit contenir que des lettres, des espaces ou des tirets.");
    }

    if (!regexLettres.test(prenom)) {
        erreurs.push("Le prénom ne doit contenir que des lettres, des espaces ou des tirets.");
    }

    const regexPhone = /^0[0-9]{9}$/;
    if (!regexPhone.test(phone)) {
        erreurs.push("Le numéro de téléphone doit être valide (ex: 0102030405).");
    }

    if (mdp.length < 8) {
        erreurs.push("Le mot de passe doit contenir au moins 8 caractères.");
    }

    if (mdp !== confirmationMdp) {
        erreurs.push("La confirmation du mot de passe ne correspond pas.");
    }

    if (erreurs.length > 0) {
        event.preventDefault();

        const zoneDonnees = document.getElementById('zone-donnees');

        zoneDonnees.innerHTML = "";

        erreurs.forEach(function (msg) {
            const p = document.createElement('p');
            p.style.color = "red";
            p.textContent = msg;
            zoneDonnees.appendChild(p);
        });
    }
});