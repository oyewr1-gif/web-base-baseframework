<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= Security::e(($title ?? 'Halaman') . ' - ' . Env::get('APP_NAME')) ?></title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; padding: 0; background: #f8f9fa; color: #212529; }
        nav { background: #1e293b; color: #fff; padding: 14px 24px; display: flex; justify-content: space-between; align-items: center; }
        nav a { color: #e2e8f0; text-decoration: none; margin-right: 16px; font-weight: 500; }
        nav a:hover { color: #38bdf8; }
        .container { max-width: 960px; margin: 28px auto; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .alert-error { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #cbd5e1; padding: 10px; text-align: left; }
        th { background: #f1f5f9; }
        .btn { display: inline-block; padding: 8px 14px; background: #2563eb; color: #fff; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; font-size: 14px; }
        .btn-danger { background: #dc2626; }
        .btn-secondary { background: #64748b; }
        input[type="text"], input[type="number"], input[type="password"] { width: 100%; padding: 8px; margin-top: 4px; margin-bottom: 14px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box; }
    </style>
</head>
<body>
<nav>
    <div>
        <a href="<?= Env::get('APP_URL') ?>/"><strong><?= Security::e(Env::get('APP_NAME')) ?></strong></a>
        <a href="<?= Env::get('APP_URL') ?>/home">Home</a>
        <a href="<?= Env::get('APP_URL') ?>/produk">Produk</a>
    </div>
    <div>
        <?php if (!empty($_SESSION['jwt_token'])): ?>
            <span style="margin-right: 14px; color: #94a3b8;">Halo, <?= Security::e($_SESSION['user']['username']) ?></span>
            <a href="<?= Env::get('APP_URL') ?>/auth/logout">Logout</a>
        <?php else: ?>
            <a href="<?= Env::get('APP_URL') ?>/auth/login">Login</a>
        <?php endif; ?>
    </div>
</nav>
<div class="container">
