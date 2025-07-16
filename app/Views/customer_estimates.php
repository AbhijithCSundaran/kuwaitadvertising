<?php include "common/header.php"; ?>

<div class="form-control mb-3 right_container">

    <!-- Heading with Back to List Button on the Right -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Estimates for <?= ucfirst($customer['name']) ?></h4>
        <a href="<?= base_url('customer/list') ?>" class="btn btn-secondary">
            Back to List
        </a>
    </div>

    <?php if (empty($estimates)) : ?>
        <p>No Estimates Found For This Customer.</p>
    <?php else : ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Estimate ID</th>
                    <th>Date</th>
                    <th>Subtotal</th>
                    <th>Discount (%)</th>
                    <th>Total</th>
                    <th>Items</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($estimates as $est) : ?>
                    <tr>
                        <td><?= $est['estimate_id'] ?></td>
                        <td><?= $est['date'] ?></td>
                        <td><?= number_format($est['subtotal'], 2) ?></td>
                        <td><?= $est['discount'] ?></td>
                        <td><?= number_format($est['total_amount'], 2) ?></td>
                        <td>
                            <ul class="mb-0">
                                <?php foreach ($est['items'] as $item) : ?>
                                    <li><?= $item['description'] ?> (<?= $item['quantity'] ?> x <?= $item['price'] ?>)</li>
                                <?php endforeach ?>
                            </ul>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
                                </div>

<?php include "common/footer.php"; ?>
