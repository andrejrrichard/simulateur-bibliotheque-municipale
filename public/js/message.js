// enlève le div du message pour récupérer l'espace en vertical
document.getElementById('flash-message').addEventListener('animationend', function() {
    this.remove();
});