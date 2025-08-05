<?php include "common/header.php"; ?>
<div class="form-control mb-3 right_container">
    <div class="alert d-none text-center position-fixed" role="alert"></div>

    <h3>Sales Report</h3>
    <form method="get" class="row g-3 mb-4">
        <div class="col-md-3">
            <label>From Date</label>
            <input type="date" id="fromDate" name="from_date" class="form-control" value="<?= esc($filters['from'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label>To Date</label>
            <input type="date" id="toDate" name="to_date" class="form-control" value="<?= esc($filters['to'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label>Customer</label>
            <select id="customerId" name="customer_id" class="form-control">
                <option value="">All Customers</option>
                <?php foreach ($customers as $cust): ?>
                    <option value="<?= $cust['customer_id'] ?>" <?= ($filters['customer_id'] ?? '') == $cust['customer_id'] ? 'selected' : '' ?>>
                        <?= esc($cust['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2" id="filterBtn">Filter</button>
            <a href="<?= base_url('sales/report') ?>" class="btn btn-secondary" id="resetBtn">Reset</a>
        </div>
    </form>
        <table class="table table-bordered" id="salesTable">
            <thead>
                <tr>
                    <th>Sl No</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
           <tbody>
               <?php foreach ($sales as $i => $sale): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= date('d-m-Y', strtotime($sale['date'])) ?></td>
                        <td><?= esc($sale['customer_name']) ?></td>
                        <td><?= number_format($sale['total_amount'], 2) ?> KWD</td>
                        <td>
                            <?php if ($sale['status'] == 1): ?>
                                <span class="badge bg-success">Paid</span>
                            <?php elseif ($sale['status'] == 0): ?>
                                <span class="badge bg-danger">Unpaid</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Unknown</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </tbody>

        </table>
</div>
    </div>
<?php include "common/footer.php"; ?>
<script>
    function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('en-GB'); // Format: dd/mm/yyyy
}


    function loadSales() {
        $.ajax({
            url: "<?= base_url('sales/getSalesReportAjax') ?>",
            type: "POST",
            data: {
                fromDate: $('#fromDate').val(),
                toDate: $('#toDate').val(),
                customerId: $('#customerId').val()
            },
            dataType: "json",
            success: function (data) {
                const sales = data.sales;

                let rows = '';
                if (sales.length > 0) {
                    sales.forEach((item, index) => {
                        const statusLabel = item.status == 1 ? 'Paid' : (item.status == 0 ? 'Unpaid' : 'Unknown');
                        const badgeClass = item.status == 1 ? 'bg-success' : (item.status == 0 ? 'bg-danger' : 'bg-secondary');

                        rows += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${formatDate(item.date)}</td>
                                <td>${item.customer_name}</td>
                                <td>${parseFloat(item.total_amount).toFixed(2)} KWD</td>
                                <td><span class="badge ${badgeClass}">${statusLabel}</span></td>
                            </tr>
                        `;
                    });
                } else {
                    rows = `<tr><td colspan="5" class="text-center">No records found.</td></tr>`;
                }

                $('#salesTable tbody').html(rows);
            },
            error: function (xhr, status, error) {
                console.error("AJAX error:", status, error);
                $('#salesTable tbody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data.</td></tr>');
            }
        });
    }

    // Button event bindings
    $('#filterBtn').click(function (e) {
        e.preventDefault();
        loadSales();
    });

    $('#resetBtn').click(function (e) {
        e.preventDefault();
        $('#fromDate').val('');
        $('#toDate').val('');
        $('#customerId').val('');
        loadSales();
    });

    // Initial Load
    $(document).ready(function () {
        loadSales();
    });
</script>
