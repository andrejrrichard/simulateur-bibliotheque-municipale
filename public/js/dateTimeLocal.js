function formatLocalDateTime(utcDateString) {
    if (!utcDateString) return '';    
    // Convertie l'heure UTC du serveur à celle de l'usager
    const date = new Date(utcDateString.replace(' ', 'T') + 'Z');
    const annee = date.getFullYear();
    const mois = String(date.getMonth() + 1).padStart(2, '0');
    const jour = String(date.getDate()).padStart(2, '0');
    const heures = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const secondes = String(date.getSeconds()).padStart(2, '0');    
    return `${annee}-${mois}-${jour} ${heures}:${minutes}:${secondes}`;
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.datetime-local').forEach(element => {
        const dateOriginal = element.textContent.trim();
        if (dateOriginal) {
            element.textContent = formatLocalDateTime(dateOriginal);
        }
    });
});