// CSRF token para requisições AJAX
const csrfToken = document.querySelector('input[name="_csrf"]')?.value || '';

// Fechar alertas automaticamente após 5s
document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
        bsAlert.close();
    }, 5000);
});
