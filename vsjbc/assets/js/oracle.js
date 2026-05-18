(function () {
    const btn    = document.getElementById('oracleBtn');
    const panel  = document.getElementById('oraclePanel');
    const msgs   = document.getElementById('oracleMessages');
    const input  = document.getElementById('oracleInput');
    if (!btn) return;

    // Alternar painel
    btn.addEventListener('click', function () {
        panel.classList.toggle('hidden');
        if (!panel.classList.contains('hidden')) {
            input.focus();
        }
    });

    function appendMsg(text, role) {
        const div = document.createElement('div');
        div.className = 'msg-bubble msg-' + role;
        div.textContent = text;
        msgs.appendChild(div);
        msgs.scrollTop = msgs.scrollHeight;
        return div;
    }

    window.oracleSend = async function () {
        const question = input.value.trim();
        if (!question) return;

        appendMsg(question, 'user');
        input.value = '';
        input.disabled = true;

        const typing = appendMsg('Digitando…', 'oracle msg-typing');

        try {
            const csrf = document.querySelector('input[name="_csrf"]')?.value || '';
            const res  = await fetch(window.APP_URL + '/api/oracle-chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrf,
                },
                body: JSON.stringify({ question }),
            });
            const data = await res.json();
            typing.remove();
            appendMsg(data.answer || data.error || 'Sem resposta.', 'oracle');
        } catch (e) {
            typing.remove();
            appendMsg('Erro de conexão. Tente novamente.', 'oracle');
        } finally {
            input.disabled = false;
            input.focus();
        }
    };

    // Expor APP_URL para fetch
    const scriptTags = document.querySelectorAll('script[src]');
    // A URL base é inferida do href relativo
})();

// Expor APP_URL via meta tag ou inline — definido no layout
if (typeof window.APP_URL === 'undefined') {
    // Fallback: inferir do location
    const path = location.pathname.split('/');
    const idx  = path.indexOf('vsjbc');
    window.APP_URL = idx !== -1
        ? location.origin + '/' + path.slice(0, idx + 1).join('/')
        : location.origin;
}
