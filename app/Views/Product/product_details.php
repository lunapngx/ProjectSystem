<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
<?= esc($product['name']) ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <h1><?= esc($product['name']) ?></h1>
    <p><?= nl2br(esc($product['description'])) ?></p>
    <p><strong>Price:</strong> $<?= esc($product['price']) ?></p>

    <form action="<?= base_url('order/place') ?>" method="post" class="mt-3">
        <input type="hidden" name="product_id" value="<?= esc($product['id']) ?>">

        <div class="mb-3">
            <label for="color" class="form-label">Color:</label>
            <select id="color" name="color" class="form-select" required>
                <?php foreach ($product['colors'] as $c): ?>
                    <option value="<?= esc($c) ?>"><?= esc($c) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="size" class="form-label">Size:</label>
            <select id="size" name="size" class="form-select" required>
                <?php foreach ($product['sizes'] as $s): ?>
                    <option value="<?= esc($s) ?>"><?= esc($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Buy Now</button>
    </form>

    <hr>

    <h2 class="mt-4">Reviews</h2>
    <ul class="list-group">
        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $r): ?>
                <li class="list-group-item">
                    <strong><?= esc($r['user_name']) ?></strong>
                    (<?= esc($r['rating']) ?>/5)
                    <p><?= esc($r['comment']) ?></p>
                    <small class="text-muted"><?= date('F j, Y', strtotime($r['created_at'])) ?></small>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="list-group-item">No reviews yet.</li>
        <?php endif; ?>
    </ul>
</div>
<?= $this->endSection() ?>
