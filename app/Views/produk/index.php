<div style="display: flex; justify-content: space-between; align-items: center;">
    <h2>Daftar Produk</h2>
    <?php if (!empty($_SESSION['jwt_token'])): ?>
        <a href="<?= Env::get('APP_URL') ?>/produk/create" class="btn">+ Tambah Produk</a>
    <?php endif; ?>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Produk</th>
            <th>Harga</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($items)): ?>
            <tr><td colspan="4" style="text-align:center;">Belum ada data produk.</td></tr>
        <?php else: ?>
            <?php foreach ($items as $row): ?>
            <tr>
                <td><?= (int) $row['id'] ?></td>
                <td><?= Security::e($row['nama']) ?></td>
                <td>Rp <?= number_format((int) $row['harga'], 0, ',', '.') ?></td>
                <td>
                    <?php if (!empty($_SESSION['jwt_token'])): ?>
                        <a href="<?= Env::get('APP_URL') ?>/produk/edit/<?= (int) $row['id'] ?>" class="btn btn-secondary">Edit</a>
                        <form method="POST" action="<?= Env::get('APP_URL') ?>/produk/delete/<?= (int) $row['id'] ?>" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</button>
                        </form>
                    <?php else: ?>
                        <span style="color: #64748b; font-size: 13px;">Login untuk kelola</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
