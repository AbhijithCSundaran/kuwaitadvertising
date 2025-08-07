<?php include "common/header.php"; ?>
<style>
    .filter_item {
        width: 50%;
    }

    #invoiceTable th,
    #invoiceTable td {
        vertical-align: middle;
        padding: 12px 20px;
        font-size: 14px;
    }

    #invoiceTable th:nth-child(1),
    #invoiceTable td:nth-child(1) {
        width: 10%;
    }

    #invoiceTable th:nth-child(2),
    #invoiceTable td:nth-child(2) {
        width: 20%;
    }

    #invoiceTable th:nth-child(4),
    #invoiceTable td:nth-child(4) {
        width: 15%;
        text-align: right;
    }
</style>

<div class="form-control mb-3 right_container">
    <div class="alert d-none text-center position-fixed" role="alert"></div>
    <h3 class="mb-3">Company Ledger (Paid Invoices)</h3>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="company_id" class="form-label">Select Company <span class="text-danger">*</span></label>
            <select class="form-control" id="company_id" required>
                <option value="">-- Select Company --</option>
                <?php foreach ($companies as $company): ?>
                    <option value="<?= $company['company_id'] ?>">
                        <?= ucwords(strtolower(esc($company['company_name']))) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="d-flex gap-1 mb-3 flex-wrap">
        <div class="filter_item input-group">
            <span class="input-group-text">From</span>
            <input type="date" id="fromDate" class="form-control">
        </div>
        <div class="filter_item input-group">
            <span class="input-group-text">To</span>
            <input type="date" id="toDate" class="form-control">
        </div>
        <select id="filterMonth" class="filter_item form-control">
            <option value="">Filter by Month</option>
            <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>"><?= date('F', mktime(0, 0, 0, $m, 10)) ?></option>
            <?php endfor; ?>
        </select>
        <select id="filterYear" class="filter_item form-control">
            <option value="">Filter by Year</option>
            <?php for ($y = date('Y') + 5; $y >= 2000; $y--): ?>
                <option value="<?= $y ?>"><?= $y ?></option>
            <?php endfor; ?>
        </select>
        <button id="filterBtn" class="btn btn-primary">Apply Filter</button>
        <button id="resetBtn" class="btn btn-secondary">Reset</button>
    </div>

    <table class="table table-bordered" id="invoiceTable">
        <thead>
            <tr>
                <th>Invoice ID</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Amount (₹)</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total:</th>
                <th id="totalInvoiceAmount">₹0.00</th>
            </tr>
        </tfoot>
    </table>
</div>
</div>
<?php include "common/footer.php"; ?>

<script>
function formatDate(dateStr) {
    const dateObj = new Date(dateStr);
    if (isNaN(dateObj)) return dateStr;
    const d = String(dateObj.getDate()).padStart(2, '0');
    const m = String(dateObj.getMonth() + 1).padStart(2, '0');
    const y = dateObj.getFullYear();
    return `${d}-${m}-${y}`;
}

function loadPaidInvoices() {
    const companyId = $('#company_id').val();
    const from = $('#fromDate').val();
    const to = $('#toDate').val();
    const month = $('#filterMonth').val();
    const year = $('#filterYear').val();

    if (!companyId) {
        $('.alert')
            .removeClass('d-none alert-success')
            .addClass('alert alert-danger')
            .text('Please select a company.')
            .fadeIn();
        setTimeout(() => $('.alert').fadeOut(), 2000);
        return;
    }

    $.ajax({
        url: "<?= base_url('companyledger/getPaidInvoices') ?>",
        method: "POST",
        data: { company_id: companyId, from, to, month, year },
        dataType: "json",
        success: function (res) {
            let rows = '';
            let total = 0;

            if (res.length > 0) {
                res.forEach(invoice => {
                    total += parseFloat(invoice.amount);
                    rows += `
                        <tr>
                            <td>${invoice.invoice_id}</td>
                            <td>${formatDate(invoice.date)}</td>
                            <td>${invoice.customer_name}</td>
                            <td class="text-end">₹${parseFloat(invoice.amount).toFixed(2)}</td>
                        </tr>
                    `;
                });
            } else {
                rows = `<tr><td colspan="4" class="text-center">No paid invoices found.</td></tr>`;
            }

            $('#invoiceTable tbody').html(rows);
            $('#totalInvoiceAmount').text('₹' + total.toFixed(2));
        }
    });
}

$(document).ready(function () {
    $('#filterBtn').click(loadPaidInvoices);
    $('#resetBtn').click(function () {
        $('#fromDate, #toDate, #filterMonth, #filterYear').val('');
        loadPaidInvoices();
    });
});
</script>
