<?= $this->extend('layout/main') ?> // Changed from layouts/main

<?= $this->section('title') ?>
<?= esc($product['name']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h1><?= esc($product['name']) ?></h1>
<p><?= nl2br(esc($product['description'])) ?></p>

<form action="<?= url_to('order_place') ?>" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="product_id" value="<?= esc($product['id']) ?>">
    <input type="hidden" name="quantity" value="1"> <?php if (!empty($product['colors'])): ?>
        <label for="color">Color:</label>
        <select id="color" name="color" required>
            <?php foreach ($product['colors'] as $c): ?>
                <option value="<?= esc($c) ?>"><?= esc($c) ?></option>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>

    <?php if (!empty($product['sizes'])): ?>
        <label for="size">Size:</label>
        <select id="size" name="size" required>
            <?php foreach ($product['sizes'] as $s): ?>
                <option value="<?= esc($s) ?>"><?= esc($s) ?></option>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>

    <button type="submit">Buy Now</button>
</form>

<h2>Reviews</h2>
<ul>
    <?php if (!empty($reviews)): // Ensure $reviews is passed from controller, even if empty ?>
        <?php foreach ($reviews as $r): ?>
            <li>
                <strong><?= esc($r['user_name']) ?></strong>
                (<?= esc($r['rating']) ?>/5)
                <p><?= esc($r['comment']) ?></p>
                <small><?= date('F j, Y', strtotime($r['created_at'])) ?></small>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li>No reviews yet.</li>
    <?php endif; ?>
    <?= $this->endSection() ?>
