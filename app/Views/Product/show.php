<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= /** @var TYPE_NAME $product */
esc(data: $product['name']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1><?= esc($product['name']) ?></h1>
<p><?= nl2br(esc($product['description'])) ?></p>
<!-- rest of form + reviews as above… -->
<?= $this->endSection() ?>
