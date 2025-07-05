<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= /** @var PRODUCT $product */
        esc(data: $product['name']) ?></title>
</head>
<body>

<h1><?= esc($product['name']) ?></h1>
<p><?= nl2br(esc($product['description'])) ?></p>
<p><strong>Price:</strong> $<?= esc($product['price']) ?></p>

<form action="<?= base_url('order/place') ?>" method="post">
    <input type="hidden" name="product_id" value="<?= esc($product['id']) ?>">

    <label for="color">Color:</label>
    <select id="color" name="color" required>
        <?php foreach ($product['colors'] as $c): ?>
            <option value="<?= esc($c) ?>"><?= esc($c) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="size">Size:</label>
    <select id="size" name="size" required>
        <?php foreach ($product['sizes'] as $s): ?>
            <option value="<?= esc($s) ?>"><?= esc($s) ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Buy Now</button>
</form>

<h2>Reviews</h2>
<ul>
    <?php if (! empty($reviews)): ?>
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
</ul>

</body>
</html>

<?= $this->endSection() ?>


