<?php include "common/header.php"; ?>
<div class="form-control mb-3 right_container">
    <div class="alert d-none text-center position-fixed" role="alert"></div>

    <h3>Sales Report</h3>
    <form method="get" class="row g-3 mb-4">
        <div class="col-md-3">
            <label>From Date</label>
            <input type="date" name="from_date" class="form-control" value="<?= esc($filters['from'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label>To Date</label>
            <input type="date" name="to_date" class="form-control" value="<?= esc($filters['to'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label>Customer</label>
            <select name="customer_id" class="form-control">
                <option value="">All Customers</option>
                <?php foreach ($customers as $cust): ?>
                    <option value="<?= $cust['customer_id'] ?>" <?= ($filters['customer_id'] ?? '') == $cust['customer_id'] ? 'selected' : '' ?>>
                        <?= esc($cust['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">Filter</button>
            <a href="<?= base_url('sales/report') ?>" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <?php if (count($sales) > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Sl No</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Total Amount</th>
                    <th>Discount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $i => $sale): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= date('d-m-Y', strtotime($sale['date'])) ?></td>
                        <td><?= esc($sale['customer_name']) ?></td>
                        <td><?= number_format($sale['total_amount'], 2) ?> KWD</td>
                        <td><?= number_format($sale['discount'], 2) ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">No sales data found for selected criteria.</div>
    <?php endif; ?>
</div>
    </div>
<?php include "common/footer.php"; ?>
