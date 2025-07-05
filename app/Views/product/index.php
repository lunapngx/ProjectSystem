<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<h1>Our Flower Collection</h1>
<div class="product-grid">
    <?php if (!empty($products) && is_array($products)): ?>
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <img src="<?= base_url('public/assets/img/' . $product['image']) ?>" alt="<?= esc($product['name']) ?>">
                <h3><?= esc($product['name']) ?></h3>
                <p>₱<?= esc(number_format($product['price'], 2)) ?></p>
                <a href="<?= url_to('product_detail', $product['id']) ?>" class="btn-sm">View</a>
                <form action="<?= url_to('cart_add') ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="number" name="quantity" value="1" min="1" class="w-25">
                    <button type="submit" class="btn-sm btn-primary">Add to Cart</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No products found.</p>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
