const bouton = document.getElementById('bouton-theme');

if (localStorage.getItem('theme') === 'sombre') {
    document.body.classList.add('sombre');
    bouton.textContent = "Mode Clair";
    bouton.style.backgroundColor = "#ffffff";
    bouton.style.color = "#333333";
}

bouton.addEventListener('click', function () {
    document.body.classList.toggle('sombre');

    if (document.body.classList.contains('sombre')) {
        bouton.textContent = "Mode Clair";
        bouton.style.backgroundColor = "#ffffff";
        bouton.style.color = "#333333";
        localStorage.setItem('theme', 'sombre');
    } else {
        bouton.textContent = "Mode Sombre";
        bouton.style.backgroundColor = "#333333";
        bouton.style.color = "#ffffff";
        localStorage.setItem('theme', 'clair');
    }
});