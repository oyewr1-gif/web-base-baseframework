<h1>Selamat Datang di <?= Security::e(Env::get('APP_NAME')) ?></h1>
<p>Aplikasi front-end berbasis PHP MVC yang terhubung dengan Almuna REST API menggunakan otentikasi JWT.</p>

<div style="margin-top: 20px;">
    <a href="<?= Env::get('APP_URL') ?>/produk" class="btn">Lihat Daftar Produk</a>
    <?php if (empty($_SESSION['jwt_token'])): ?>
        <a href="<?= Env::get('APP_URL') ?>/auth/login" class="btn btn-secondary">Login Sekarang</a>
    <?php endif; ?>
</div>
