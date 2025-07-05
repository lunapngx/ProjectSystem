<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Checkout<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="checkout-container">
    <!-- ── LEFT: checkout form ── -->
    <div class="left">
        <?= isset($validation) ? $validation->listErrors('<div style="color:red">','</div>') : '' ?>
        <?= form_open('Checkout') ?><?= csrf_field() ?>

        <h3>1. Customer Information</h3>
        <input type="text" name="first_name"    placeholder="First Name" value="<?= set_value('first_name') ?>"><br><br>
        <input type="text" name="last_name"     placeholder="Last Name"  value="<?= set_value('last_name') ?>"><br><br>
        <input type="email" name="email"        placeholder="Email"      value="<?= set_value('email') ?>"><br><br>
        <input type="text" name="phone"         placeholder="Phone"      value="<?= set_value('phone') ?>"><br><br>

        <h3>2. Shipping Address</h3>
        <input type="text" name="street"        placeholder="Street Address"       value="<?= set_value('street') ?>"><br><br>
        <input type="text" name="apartment"     placeholder="Apt, Suite, etc. (opt)"value="<?= set_value('apartment') ?>"><br><br>
        <input type="text" name="city"          placeholder="City"                 value="<?= set_value('city') ?>"><br><br>
        <input type="text" name="state"         placeholder="State"                value="<?= set_value('state') ?>"><br><br>
        <input type="text" name="zip"           placeholder="ZIP Code"             value="<?= set_value('zip') ?>"><br><br>

        <h3>3. Payment Method</h3>
        <label>
            <input type="radio" name="payment_method" value="cod"
                <?= set_radio('payment_method','cod', true) ?>>
            Cash on Delivery
        </label><br><br>

        <label>
            <input type="radio" name="payment_method" value="pickup"
                <?= set_radio('payment_method', 'pickup') ?>>
            Pick Up
        </label><br><br>

        <button type="submit">Place Order</button>
        <?= form_close() ?>
    </div>

    <!-- ── RIGHT: order summary ── -->
    <div class="right order-summary">
        <h3>Order Summary <small>(<?= /** @var TYPE_NAME $items */
                count(value: $items) ?> Items)</small></h3>

        <?php foreach($items as $it): ?>
            <div class="item" style="display:flex; gap:.5rem; margin-bottom:1rem;">
                <img src="<?= esc($it['thumb']) ?>" width="60" alt="">
                <div class="details" style="flex:1;">
                    <strong><?= esc($it['name']) ?></strong><br>
                    <small>
                        Color: <?= esc($it['options']['color']) ?> |
                        Size: <?= esc($it['options']['size']) ?>
                    </small><br>
                    <span><?= $it['qty'] ?> × <?= number_to_currency($it['price']) ?></span>
                </div>
            </div>
            <hr>
        <?php endforeach ?>

        <div class="promo" style="display:flex; gap:.5rem; margin:1rem 0;">
            <input type="text" name="promo_code" placeholder="Promo Code">
            <button type="button">Apply</button>
        </div>

        <div class="totals">
            <div style="display:flex;justify-content:space-between">
                <span>Subtotal</span><span><?= $subtotal = "";
                    number_to_currency($subtotal) ?></span>
            </div>
            <div style="display:flex;justify-content:space-between">
                <span>Shipping</span><span><?= number_to_currency($shipping) ?></span>
            </div>
            <div style="display:flex;justify-content:space-between">
                <span>Tax</span><span><?= number_to_currency($tax) ?></span>
            </div>
            <hr style="border:0;border-top:1px dotted #ccc;">
            <div style="display:flex;justify-content:space-between;font-size:1.1rem">
                <strong>Total</strong><strong><?= number_to_currency($total) ?></strong>
            </div>
        </div>

        <div class="secure" style="text-align:center;margin-top:1rem;font-size:.9rem">
            🔒 Secure Checkout
            <div class="icons" style="font-size:1.5rem;margin-top:.5rem">
                💳 🏦 🅿️ 🍎
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>


<?= $this->section('scripts') ?>9
<script>
    // any JS you need just on this page
</script>
<?= $this->endSection() ?>
