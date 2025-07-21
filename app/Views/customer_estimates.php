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
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead>
                    <tr>
                        <th>Estimate ID</th>
                        <th>Estimate Date</th>
                        <th>Items</th>
                        <th>Subtotal</th>
                        <th>Discount (%)</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($estimates as $est) : ?>
                        <tr>
                            <td><?= $est['estimate_id'] ?></td>
                            <td><?= date('d-m-Y', strtotime($est['date'])) ?></td>
                            <td class="text-start">
                                <ul class="mb-0 ps-3">
                                    <?php foreach ($est['items'] as $item) : ?>
                                        <li><?= $item['description'] ?> (<?= $item['quantity'] ?> x <?= number_format($item['price'], 2) ?>)</li>
                                    <?php endforeach ?>
                                </ul>
                            </td>
                            <td><?= number_format($est['subtotal'], 2) ?></td>
                            <td><?= $est['discount'] ?></td>
                            <td><?= number_format($est['total_amount'], 2) ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</div>
<?php include "common/footer.php"; ?>
