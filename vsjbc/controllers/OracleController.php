<?php
class OracleController
{
    private OracleKnowledgeModel $kb;
    private DemandModel $demands;
    private GlpiTicketModel $glpi;

    public function __construct()
    {
        $this->kb      = new OracleKnowledgeModel();
        $this->demands = new DemandModel();
        $this->glpi    = new GlpiTicketModel();
    }

    // ── Chat API ─────────────────────────────────────────────────────────────

    public function chat(): void
    {
        Auth::require();
        if (!CSRF::verify()) Response::json(['error' => 'CSRF inválido'], 403);

        // Rate limiting: máximo 30 mensagens por hora por sessão
        $count = $_SESSION['_oracle_count'] ?? 0;
        $reset = $_SESSION['_oracle_reset'] ?? 0;
        if (time() > $reset) {
            $count = 0;
            $_SESSION['_oracle_reset'] = time() + 3600;
        }
        if ($count >= 30) {
            Response::json(['error' => 'Limite de mensagens atingido. Aguarde 1 hora.'], 429);
        }
        $_SESSION['_oracle_count'] = $count + 1;

        $body     = json_decode(file_get_contents('php://input'), true) ?? [];
        $question = trim(Sanitizer::str('question', $body));

        if (strlen($question) < 3 || strlen($question) > 1000) {
            Response::json(['error' => 'Pergunta inválida.'], 400);
        }

        $context  = $this->buildContext($question);
        $answer   = $this->callGemini($context, $question);
        Response::json(['answer' => $answer]);
    }

    // ── Gestão da base de conhecimento ───────────────────────────────────────

    public function knowledge(): void
    {
        Auth::require();
        $entries = $this->kb->findAll();
        $flash   = flash_get();
        require __DIR__ . '/../views/oracle/knowledge.php';
    }

    public function createKnowledge(): void
    {
        Auth::require();
        $flash = flash_get();
        require __DIR__ . '/../views/oracle/knowledge_form.php';
    }

    public function storeKnowledge(): void
    {
        Auth::require();
        CSRF::requireValid();
        $data = $this->extractKbData();
        if (!$data['question'] || !$data['answer']) {
            flash('danger', 'Pergunta e resposta são obrigatórios.');
            Response::redirect('oraculo/novo');
        }
        $this->kb->create($data, Auth::id());
        flash('success', 'Conhecimento adicionado ao Oráculo!');
        Response::redirect('oraculo');
    }

    public function editKnowledge(string $id): void
    {
        Auth::require();
        $entry = $this->kb->findById((int)$id);
        if (!$entry) Response::notFound();
        $flash = flash_get();
        require __DIR__ . '/../views/oracle/knowledge_form.php';
    }

    public function updateKnowledge(string $id): void
    {
        Auth::require();
        CSRF::requireValid();
        $data = $this->extractKbData();
        if (!$data['question'] || !$data['answer']) {
            flash('danger', 'Pergunta e resposta são obrigatórios.');
            Response::redirect("oraculo/{$id}/editar");
        }
        $this->kb->update((int)$id, $data);
        flash('success', 'Conhecimento atualizado!');
        Response::redirect('oraculo');
    }

    public function deleteKnowledge(string $id): void
    {
        Auth::require();
        CSRF::requireValid();
        $this->kb->delete((int)$id);
        flash('success', 'Entrada removida.');
        Response::redirect('oraculo');
    }

    // ── Internos ──────────────────────────────────────────────────────────────

    private function buildContext(string $question): string
    {
        $parts = [];

        $kbResults = $this->kb->searchRelevant($question, 5);
        if ($kbResults) {
            $parts[] = "=== BASE DE CONHECIMENTO ===";
            foreach ($kbResults as $k) {
                $parts[] = "P: {$k['question']}\nR: {$k['answer']}";
            }
        }

        $demandResults = $this->demands->searchForContext($question, 5);
        if ($demandResults) {
            $parts[] = "\n=== DEMANDAS RELEVANTES ===";
            foreach ($demandResults as $d) {
                $status   = status_label($d['status']);
                $priority = priority_label($d['priority']);
                $deadline = $d['deadline'] ? date_br($d['deadline']) : 'sem prazo';
                $parts[] = "Demanda #{$d['id']}: {$d['title']} | Status: {$status} | Prioridade: {$priority} | Prazo: {$deadline}\n{$d['description']}";
            }
        }

        $glpiResults = $this->glpi->searchForContext($question, 5);
        if ($glpiResults) {
            $parts[] = "\n=== CHAMADOS GLPI RELEVANTES ===";
            foreach ($glpiResults as $t) {
                $parts[] = "Ticket #{$t['glpi_id']}: {$t['title']} | Status: {$t['status']}\nSolicitante: {$t['requester']} | Responsável: {$t['assignee']}\n{$t['description']}\nSolução: {$t['solution']}";
            }
        }

        $ctx = implode("\n\n", $parts);

        // Limitar contexto a ~3000 chars para não exceder limites de API
        if (strlen($ctx) > 3000) {
            $ctx = mb_substr($ctx, 0, 3000) . '...';
        }

        return $ctx;
    }

    private function callGemini(string $context, string $question): string
    {
        $systemPrompt = "Você é o Oráculo, assistente virtual da equipe de TI VSJBC.\n"
            . "Responda em Português do Brasil. Seja objetivo e profissional.\n"
            . "Use APENAS as informações fornecidas abaixo. Se não encontrar a informação, diga:\n"
            . "'Não encontrei essa informação na minha base de dados.'\n\n"
            . $context;

        $payload = [
            'contents' => [
                [
                    'role'  => 'user',
                    'parts' => [['text' => $systemPrompt . "\n\nPergunta do usuário: " . $question]],
                ]
            ],
            'generationConfig' => [
                'temperature'     => 0.3,
                'maxOutputTokens' => 512,
            ],
        ];

        $ch = curl_init(GEMINI_ENDPOINT . '?key=' . GEMINI_API_KEY);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            // Em desenvolvimento, mostrar erro detalhado
            if (APP_ENV === 'development') {
                return "Erro na API Gemini. HTTP: {$httpCode}. cURL: {$curlErr}. Resposta: " . substr($response ?: '', 0, 200);
            }
            return 'Desculpe, não consegui conectar ao serviço de IA no momento. Tente novamente.';
        }

        $data = json_decode($response, true);
        return $data['candidates'][0]['content']['parts'][0]['text']
            ?? 'Não obtive resposta da IA. Tente reformular sua pergunta.';
    }

    private function extractKbData(): array
    {
        return [
            'category' => Sanitizer::str('category'),
            'question' => Sanitizer::str('question'),
            'answer'   => Sanitizer::str('answer'),
            'tags'     => Sanitizer::str('tags'),
            'active'   => isset($_POST['active']),
        ];
    }
}
