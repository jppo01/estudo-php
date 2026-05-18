<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1e2a3b 0%, #2d3f5e 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: #fff; border-radius: 16px; box-shadow: 0 16px 48px rgba(0,0,0,.25); padding: 2.5rem; width: 100%; max-width: 400px; }
        .brand { text-align: center; margin-bottom: 2rem; }
        .brand h1 { font-size: 1.8rem; font-weight: 800; color: #1e2a3b; }
        .brand h1 span { color: #1a56db; }
        .brand p { color: #6b7280; font-size: .9rem; margin: 0; }
    </style>
</head>
<body>
<div class="login-card">
    <?= $content ?? '' ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
