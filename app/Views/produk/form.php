<?php
$isEdit = ($action === 'edit');
$formAction = $isEdit
    ? Env::get('APP_URL') . '/produk/edit/' . (int) ($item['id'] ?? 0)
    : Env::get('APP_URL') . '/produk/create';

$namaVal  = $old['nama'] ?? ($item['nama'] ?? '');
$hargaVal = $old['harga'] ?? ($item['harga'] ?? '');
?>

<h2><?= $isEdit ? 'Edit Produk' : 'Tambah Produk Baru' ?></h2>

<?php if (!empty($errors)): ?>
    <div class="alert-error">
        <ul style="margin: 0; padding-left: 20px;">
            <?php foreach ($errors as $err): ?>
                <li><?= Security::e(is_array($err) ? implode(', ', $err) : $err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="<?= $formAction ?>" style="max-width: 480px;">
    <input type="hidden" name="csrf_token" value="<?= Security::csrfToken() ?>">

    <label>Nama Produk (Minimal 3 karakter)</label>
    <input type="text" name="nama" value="<?= Security::e($namaVal) ?>" required minlength="3">

    <label>Harga (Rp)</label>
    <input type="number" name="harga" value="<?= (int) $hargaVal ?>" required min="0">

    <button type="submit" class="btn"><?= $isEdit ? 'Perbarui Data' : 'Simpan Produk' ?></button>
    <a href="<?= Env::get('APP_URL') ?>/produk" class="btn btn-secondary">Batal</a>
</form>
