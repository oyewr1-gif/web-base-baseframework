<!DOCTYPE html>
<html lang="id">
<head><title><?= Security::e(Env::get('APP_NAME')) ?> - Produk</title></head>
<body>
    <h2>Daftar Produk</h2>

    <?php if (!empty($_SESSION['jwt_token'])): ?>
        <p>
            Halo, <b><?= Security::e($_SESSION['user']['username']) ?></b> |
            <a href="<?= Env::get('APP_URL') ?>/produk/create">+ Tambah Produk</a> |
            <a href="<?= Env::get('APP_URL') ?>/auth/logout">Logout</a>
        </p>
    <?php else: ?>
        <p><a href="<?= Env::get('APP_URL') ?>/auth/login">Login untuk Kelola Produk</a></p>
    <?php endif; ?>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $row): ?>
            <tr>
                <td><?= (int) $row['id'] ?></td>
                <td><?= Security::e($row['nama']) ?></td>
                <td>Rp <?= number_format((int) $row['harga'], 0, ',', '.') ?></td>
                <td>
                    <?php if (!empty($_SESSION['jwt_token'])): ?>
                        <a href="<?= Env::get('APP_URL') ?>/produk/edit/<?= (int) $row['id'] ?>">Edit</a>
                        <form method="POST" action="<?= Env::get('APP_URL') ?>/produk/delete/<?= (int) $row['id'] ?>" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
                            <button type="submit" onclick="return confirm('Hapus produk ini?')">Hapus</button>
                        </form>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
