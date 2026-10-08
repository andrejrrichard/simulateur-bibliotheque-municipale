function formatLocalDate(utcDateString) {
    // Convertie la date UTC du serveur à la date de l'usager
    if (!utcDateString) return '';    
    const date = new Date(utcDateString.replace(' ', 'T') + 'Z');
    const annee = date.getFullYear();
    const mois = String(date.getMonth() + 1).padStart(2, '0');
    const jour = String(date.getDate()).padStart(2, '0');    
    return `${annee}-${mois}-${jour}`;
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.date-local').forEach(element => {
        const dateOriginal = element.textContent.trim();
        if (dateOriginal) {
            element.textContent = formatLocalDate(dateOriginal);
        }
    });
});