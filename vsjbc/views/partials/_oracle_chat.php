<!-- Oracle Chat Widget -->
<button class="oracle-btn" id="oracleBtn" title="Pergunte ao Oráculo">
    <i class="bi bi-robot"></i>
</button>

<div class="oracle-panel hidden" id="oraclePanel">
    <div class="oracle-header">
        <i class="bi bi-robot"></i>
        <span>Oráculo IA</span>
        <span style="margin-left:auto;font-size:.75rem;opacity:.7">Man. Assistencial</span>
        <button onclick="document.getElementById('oraclePanel').classList.add('hidden')"
                style="background:none;border:none;color:#fff;margin-left:.5rem;opacity:.7;cursor:pointer;font-size:1rem">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="oracle-messages" id="oracleMessages">
        <div class="msg-bubble msg-oracle">
            Olá! Sou o Oráculo, assistente da equipe de Manutenção Assistencial e Engenharia Clínica. Posso responder dúvidas sobre as demandas e chamados de manutenção cadastrados. Como posso ajudar?
        </div>
    </div>
    <div class="oracle-input-row">
        <input type="text" id="oracleInput" placeholder="Digite sua pergunta..." maxlength="500"
               onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault();window.oracleSend();}">
        <button onclick="window.oracleSend()"><i class="bi bi-send-fill"></i></button>
    </div>
</div>
