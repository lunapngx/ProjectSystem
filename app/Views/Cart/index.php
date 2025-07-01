<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Your Cart<?= $this->endSection() ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/Cart.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="cart-container">
        <h1 class="my-4 text-center">CART</h1>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('info')): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?= session()->getFlashdata('info') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="cart-grid">
            <div class="cart-items">
                <table>
                    <thead>
                    <tr>
                        <th>PRODUCT</th>
                        <th>PRICE</th>
                        <th>QUANTITY</th>
                        <th>TOTAL</th>
                        <th></th> </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($cartItems)): ?>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td>
                                    <div class="product-info-cell">
                                        <img src="<?= base_url('assets/img/' . esc($item->product->image)) ?>" alt="<?= esc($item->product->name) ?>" class="product-image-small">
                                        <div>
                                            <strong><?= esc($item->product->name) ?></strong>
                                            <?php if (isset($item->options)): ?>
                                                <small>
                                                    <?php foreach($item->options as $key => $value): ?>
                                                        <?= esc(ucfirst($key)) ?>: <?= esc($value) ?>
                                                    <?php endforeach; ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>₱<?= esc(number_format($item->product->price, 2)) ?></td>
                                <td>
                                    <form action="<?= url_to('cart_update') ?>" method="post" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="product_id" value="<?= esc($item->product->id) ?>">
                                        <div class="quantity-controls">
                                            <button type="button" class="btn-minus">-</button>
                                            <input type="number" name="quantity" value="<?= esc($item->quantity) ?>" min="1" max="<?= esc($item->product->stock) ?? 99 ?>" readonly>
                                            <button type="button" class="btn-plus">+</button>
                                        </div>
                                        <button type="submit" class="d-none">Update</button> </form>
                                </td>
                                <td>₱<?= esc(number_format($item->itemTotal, 2)) ?></td>
                                <td>
                                    <form action="<?= url_to('cart_remove') ?>" method="post" onsubmit="return confirm('Are you sure you want to remove this item?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="product_id" value="<?= esc($item->product->id) ?>">
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">Your cart is empty.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>

                <div class="cart-actions text-end py-3 px-3">
                    <a href="<?= url_to('products_list') ?>" class="btn btn-secondary">Continue Shopping</a>
                    <button type="button" class="btn btn-primary" id="updateCartBtn">Update Cart</button>
                </div>
            </div>

            <div class="order-summary">
                <h3>ORDER SUMMARY</h3>
                <div>
                    <span>Subtotal</span>
                    <!-- give this an ID so we can read it in JS -->
                    <span id="subtotal">₱<?= esc(number_format($total, 2)) ?></span>
                </div>
                <div class="shipping-options">
                    <span>Shipping</span>
                    <div>
                        <label>
                            <input type="radio" name="shipping_method" value="standard" checked>
                            Standard Delivery – ₱40
                        </label>
                        <label>
                            <input type="radio" name="shipping_method" value="pickup">
                            Self Pick-Up – ₱0
                        </label>
                        <label>
                            <input type="radio" name="shipping_method" value="free">
                            Free Shipping (Orders over ₱300)
                        </label>
                    </div>
                </div>
                <div>
                    <span>Discount</span>
                    <span>-₱0.00</span>
                </div>
                <div class="total">
                    <span>TOTAL</span>
                    <!-- and this one so we can update the number -->
                    <span id="grandTotal">₱<?= esc(number_format($total + 40, 2)) ?></span>
                </div>
                <a href="<?= url_to('checkout_view') ?>" class="btn btn-proceed">
                    PROCEED TO CHECKOUT <i class="bi bi-arrow-right"></i>
                </a>
                <a href="<?= url_to('products_list') ?>" class="btn btn-continue">
                    Continue Shopping <i class="bi bi-arrow-left"></i>
                </a>
                …
            </div>
        </div>
    </div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // grab the elements
            const subtotalEl  = document.getElementById('subtotal');
            const totalEl     = document.getElementById('grandTotal');
            const shippingRad = document.querySelectorAll('input[name="shipping_method"]');

            // parse the ₱123.45 to a number
            const parsePeso = txt => parseFloat(txt.replace(/[^0-9.]/g,'')) || 0;
            const fmtPeso   = num => '₱' + num.toFixed(2);

            // read initial subtotal once
            const base = parsePeso(subtotalEl.textContent);

            shippingRad.forEach(radio => {
                radio.addEventListener('change', () => {
                    let shipCost = 0;

                    switch(radio.value) {
                        case 'standard':
                            shipCost = 40; break;
                        case 'pickup':
                            shipCost = 0;  break;
                        case 'free':
                            shipCost = base > 300 ? 0 : 40;
                            break;
                    }
                    totalEl.textContent = fmtPeso(base + shipCost);
                });
            });
        });
    </script>
<?= $this->endSection() ?>