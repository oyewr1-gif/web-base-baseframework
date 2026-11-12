<h2>Login Pengguna</h2>

<?php if (!empty($error)): ?>
    <div class="alert-error"><?= Security::e($error) ?></div>
<?php endif; ?>

<form method="POST" action="<?= Env::get('APP_URL') ?>/auth/login" style="max-width: 400px;">
    <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">

    <label>Username</label>
    <input type="text" name="username" required autofocus>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit" class="btn">Masuk</button>
</form>
