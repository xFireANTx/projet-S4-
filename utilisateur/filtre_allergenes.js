// 1. On récupère les deux cases à cocher (checkboxes) par leur nom
const checkboxes = document.querySelectorAll('input[name="allergene"]');

// 2. On récupère toutes les boîtes de plats de la page
const tousLesPlats = document.querySelectorAll('.plat');

// Fonction qui va filtrer les plats
function filtrerLaCarte() {
    // On crée une liste des allergènes qu'on souhaite EXCLURE (ceux qui sont cochés)
    let allergenesAExclure = [];

    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            allergenesAExclure.push(checkbox.value); // "viande" et/ou "poisson"
        }
    });

    // On parcourt chaque plat pour décider s'il faut l'afficher ou le cacher
    tousLesPlats.forEach(plat => {
        const allergenesDuPlatStr = plat.getAttribute('data-allergenes') || "";
        const allergenesDuPlat = allergenesDuPlatStr.split(',').map(item => item.trim());

        let doitEtreMasque = false;

        allergenesAExclure.forEach(allergeneExclu => {
            if (allergenesDuPlat.includes(allergeneExclu)) {
                doitEtreMasque = true; // Le plat contient un ingrédient banni !
            }
        });

        // --- AMÉLIORATION VISUELLE POUR LES MENUS ---
        // Si c'est une formule de menu, on cible son parent (.conteneur-menu) pour éviter les trous blancs
        const elementAEnlever = plat.classList.contains('carte-formule') ? plat.parentElement : plat;

        // 3. On applique le changement visuel
        if (doitEtreMasque) {
            elementAEnlever.style.display = "none"; // On cache le plat ou le bloc menu complet
        } else {
            // Note : pour un conteneur-menu ou un plat standard, "block" ou "" redonne le comportement d'origine
            elementAEnlever.style.display = "";
        }
    });
}

// 4. On demande à JavaScript d'exécuter la fonction "filtrerLaCarte" à chaque fois qu'on coche/décoche une case
checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', filtrerLaCarte);
});