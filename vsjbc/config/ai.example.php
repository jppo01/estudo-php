<?php
// Copie para config/ai.php e preencha com sua chave Gemini API
define('GEMINI_API_KEY', 'SUA_CHAVE_AQUI');
define('GEMINI_MODEL',   'gemini-2.5-flash');
define('GEMINI_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta/models/' . GEMINI_MODEL . ':generateContent');
