<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?= /** @var TYPE_NAME $category */
        esc(data: $category['name']) ?></title>
</head>
<body>
<h1><?= esc($category['name']) ?></h1>
<ul>
    <?php /** @var TYPE_NAME $products */
    foreach ($products as $p): ?>
        <li>
            <a href="<?= base_url('Product/'.$p['id']) ?>">
                <?= esc($p['name']) ?> — $<?= esc($p['price']) ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
</body>
</html>

<?= $this->endSection() ?>


