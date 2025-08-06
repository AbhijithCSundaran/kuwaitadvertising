<?php include "common/header.php"; ?>
<div class="form-control mb-3 right_container">
    <div class="alert d-none text-center position-fixed" role="alert"></div>

    <h3>Sales Report</h3>
    <form method="get" class="row g-3 mb-4">
        <div class="col-md-3 filter-date p-3">
            <div class="input-group-prepend">
                <span class="input-group-text from-date">From</span>
            </div>
            <input type="date" id="fromDate" name="from_date" class="form-control" value="<?= esc($filters['from'] ?? '') ?>">
        </div>
        <div class="col-md-3 p-3">
             <div class="input-group-prepend">
                <span class="input-group-text from-date">To</span>
            </div>
            <input type="date" id="toDate" name="to_date" class="form-control" value="<?= esc($filters['to'] ?? '') ?>">
        </div>
        <div class="col-md-3 p-3">
            <div class="input-group-prepend">
                <span class="input-group-text from-date">Customer</span>
            </div>
            <select id="customerId" name="customer_id" class="form-control">
                <option value="">All Customers</option>
                <?php foreach ($customers as $cust): ?>
                    <option value="<?= $cust['customer_id'] ?>" <?= ($filters['customer_id'] ?? '') == $cust['customer_id'] ? 'selected' : '' ?>>
                        <?= esc($cust['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end p-3">
            <button type="submit" class="btn btn-primary me-2" id="filterBtn" style="height:51px; width:130px;">Filter</button>
            <a href="<?= base_url('invoice/report') ?>" class="btn btn-secondary " id="resetBtn" style="height:51px; width:130px; line-height: 2;">Reset</a>
        </div>
    </form>
        <table class="table table-bordered" id="salesTable">
            <thead>
                <tr>
                    <th><strong>Sl No</strong></th>
                    <th><strong>Date</strong></th>
                    <th><strong>Customer Name</strong></th>
                    <th><strong>Invoice Amount</strong></th>
                    <th><strong>Status</strong></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $grandTotal = 0;
                    foreach ($invoices as $i => $sale):
                        $grandTotal += (float) $sale['total_amount'];
                ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= date('d-m-Y', strtotime($sale['invoice_date'])) ?></td>
                            <td><?= esc($sale['customer_name']) ?></td>
                            <td><?= number_format($sale['total_amount'], 2) ?> KWD</td>
                            <td>
                            <?php if ($sale['status'] === 'paid'): ?>
                                    <span class="badge bg-success">Paid</span>
                                <?php elseif ($sale['status'] === 'unpaid'): ?>
                                    <span class="badge bg-danger">Unpaid</span>
                                <?php elseif ($sale['status'] === 'partial paid'): ?>
                                    <span class="badge bg-warning text-dark">Partial Paid</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Unknown</span>
                                <?php endif; ?>

                            </td>
                        </tr>
                    <?php endforeach; ?>
            </tbody>
           <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Grand Total:</th>
                    <th id="totalAmount">0.00 KWD</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
</div>
</div>
<?php include "common/footer.php"; ?>
<script>
    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-GB');
    }
    function loadSales() {
        $.ajax({
            url: "<?= base_url('invoice/getSalesReportAjax') ?>",
            type: "POST",
            data: {
                fromDate: $('#fromDate').val(),
                toDate: $('#toDate').val(),
                customerId: $('#customerId').val()
            },
            dataType: "json",
            success: function (data) {
                const sales = data.invoices;
                let rows = '';
                let grandTotal = 0;

                if (sales.length > 0) {
                    sales.forEach((item, index) => {
                        const statusLabel = item.status === 'paid' ? 'Paid' : 
                                            (item.status === 'unpaid' ? 'Unpaid' : 
                                            (item.status === 'partial paid' ? 'Partial Paid' : 'Unknown'));

                        const badgeClass = item.status === 'paid' ? 'bg-success w-100' : 
                                        (item.status === 'unpaid' ? 'bg-danger w-100' : 
                                        (item.status === 'partial paid' ? 'bg-warning text-dark w-100' : 'bg-secondary'));

                        const totalAmount = parseFloat(item.total_amount);
                        grandTotal += totalAmount;

                        rows += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${formatDate(item.invoice_date)}</td>
                                <td>${item.customer_name}</td>
                                <td>${totalAmount.toFixed(2)} KWD</td>
                                <td><span class="badge ${badgeClass}">${statusLabel}</span></td>
                            </tr>
                        `;
                    });
                } else {
                    rows = `<tr><td colspan="5" class="text-center">No records found.</td></tr>`;
                }

                $('#salesTable tbody').html(rows);
                $('#totalAmount').text(`${grandTotal.toFixed(2)} KWD`);
            },
            error: function (xhr, status, error) {
                console.error("AJAX error:", status, error);
                $('#salesTable tbody').html('<tr><td colspan="5" class="text-center text-danger">Error loading data.</td></tr>');
                $('#totalAmount').text(`0.00 KWD`);
            }
        });
    }

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
        loadSales();
</script>
