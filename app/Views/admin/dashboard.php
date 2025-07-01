<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
    <h1>Admin Dashboard</h1>
    <p>Welcome to the administration area.</p>
    <ul>
        <li><a href="<?= base_url('admin/products') ?>">Manage Products</a></li>
        <li><a href="<?= base_url('admin/categories') ?>">Manage Categories</a></li>
        <li><a href="<?= base_url('admin/orders') ?>">Manage Orders</a></li>
        <li><a href="<?= base_url('admin/users') ?>">Manage Users (Optional)</a></li>
    </ul>
<?= $this->endSection() ?>