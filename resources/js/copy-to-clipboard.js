// Funkcja kopiowania do schowka
function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
        // Nowoczesne API
        return navigator.clipboard.writeText(text).then(() => {
            return true;
        }).catch(() => {
            return false;
        });
    } else {
        // Fallback dla starszych przeglądarek
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            const result = document.execCommand('copy');
            document.body.removeChild(textArea);
            return Promise.resolve(result);
        } catch (err) {
            document.body.removeChild(textArea);
            return Promise.resolve(false);
        }
    }
}

// Eksportuj funkcję globalnie
window.copyToClipboard = copyToClipboard;
