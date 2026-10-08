document.getElementById('date_retour_locale').addEventListener('change', function() {
    const dateLocale = this.value;
    if (dateLocale) {
        // Ajouter 23:59:59 dans le fuseau local du navigateur
        const date = new Date(dateLocale + 'T23:59:59');        
        
        if (isNaN(date.getTime())) {
            console.error('Date invalide:', dateLocale);
            return;
        }
        
        const annee = date.getUTCFullYear();
        const mois = String(date.getUTCMonth() + 1).padStart(2, '0');
        const jour = String(date.getUTCDate()).padStart(2, '0');
        const heures = String(date.getUTCHours()).padStart(2, '0');
        const minutes = String(date.getUTCMinutes()).padStart(2, '0');
        const secondes = String(date.getUTCSeconds()).padStart(2, '0');
        
        const utcDate = `${annee}-${mois}-${jour} ${heures}:${minutes}:${secondes}`;
        document.getElementById('date_retour_utc').value = utcDate;
    }
});

window.addEventListener('load', function() {
    const event = new Event('change');
    document.getElementById('date_retour_locale').dispatchEvent(event);
});