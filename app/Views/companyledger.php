<?php include "common/header.php"; ?>
<style>
    #plainExpenseTable th,
    #plainExpenseTable td {
        vertical-align: middle;
        padding: 16px 30px;
        font-size: 14px;
    }

    #plainExpenseTable th:nth-child(1),
    #plainExpenseTable td:nth-child(1) {
        width: 10%;
        text-align: center;
    }

    #plainExpenseTable th:nth-child(2),
    #plainExpenseTable td:nth-child(2) {
        width: 15%;
         text-align: center !important;
    }

    #plainExpenseTable th:nth-child(4),
    #plainExpenseTable td:nth-child(4) {
        width: 20% !important;
        text-align: center !important;
    }

    #plainExpenseTable th:nth-child(5),
    #plainExpenseTable td:nth-child(5) {
        width: 20% !important;
        text-align: center !important;
    }

    .filter_item {
        width: 50%;
    }
</style>

<div class="form-control mb-3 right_container">
    <div class="alert d-none text-center position-fixed" role="alert"></div>

    <h3 class="mb-3">Company Ledger</h3>

    <div class="d-flex gap-1 mb-3">
        <div class="filter_item input-group">
            <div class="input-group-prepend">
                <span class="input-group-text from-date">From</span>
            </div>
            <input type="date" id="fromDate" class="form-control">
        </div>

        <div class="filter_item input-group">
            <div class="input-group-prepend">
                <span class="input-group-text from-date">To</span>
            </div>
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
            <?php
            $currentYear = date('Y');
            $endYear = $currentYear + 5;
            for ($y = $endYear; $y >= 2000; $y--): ?>
                <option value="<?= $y ?>"><?= $y ?></option>
            <?php endfor; ?>
        </select>
        <button id="filterBtn" class="btn btn-primary">Apply Filter</button>
        <button id="resetBtn" class="btn btn-secondary">Reset</button>
    </div>

    <table class="table table-bordered" id="plainExpenseTable">
        <thead>
            <tr>
                <th><strong>SI No</strong></th>
                <th><strong>Invoice ID</strong></th>
                <th><strong>Customer</strong></th>
                <th><strong>Date</strong></th>
                <th><strong>Amount</strong></th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-end">Total:</th>
                <th id="totalAmount">₹0.00</th>
            </tr>
        </tfoot>
    </table>
</div>
</div>
<?php include "common/footer.php"; ?>
<script>
function formatDate(dateStr) {
    const dateObj = new Date(dateStr);
    const day = String(dateObj.getDate()).padStart(2, '0');
    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
    const year = dateObj.getFullYear();
    return `${day}-${month}-${year}`;
}

$(document).ready(function () {
    let filterApplied = false;

    function showAlert(type, message) {
        const alertBox = $('.alert');
        alertBox.removeClass('d-none alert-success alert-danger')
                .addClass(`alert alert-${type}`)
                .text(message)
                .fadeIn();
        setTimeout(() => alertBox.fadeOut(), 2000);
    }

    function loadPaidInvoices() {
        const from = $('#fromDate').val();
        const to = $('#toDate').val();
        const month = $('#filterMonth').val();
        const year = $('#filterYear').val();

        if (month && !year) {
            showAlert('danger', 'Select Both Month And Year.');
            return;
        }

        if ((from && !to) || (!from && to)) {
            showAlert('danger', 'Please Select Both From and To dates For Report.');
            return;
        }

        $.ajax({
            url: "<?= base_url('companyledger/getPaidInvoices') ?>",
            method: "POST",
            data: { from, to, month, year },
            dataType: "json",
            success: function (response) {
                if (response.status !== 'success') {
                    showAlert('danger', response.message);
                    return;
                }

                const res = response.data;
                let rows = '';
                let total = 0;

                if (res.length > 0) {
                    res.forEach((invoice, index) => {
                        total += parseFloat(invoice.total_amount);
                        rows += `
                            <tr>
                                <td class="text-center">${index + 1}</td>
                                <td>${invoice.invoice_id}</td>
                                <td>${invoice.customer_name}</td>
                                <td>${formatDate(invoice.invoice_date)}</td>
                                <td class="text-end">₹${parseFloat(invoice.total_amount).toFixed(2)}</td>
                            </tr>
                        `;
                    });
                } else {
                    rows = `<tr><td colspan="5" class="text-center">No paid invoices found.</td></tr>`;
                }

                $('#plainExpenseTable tbody').html(rows);
                $('#totalAmount').text('₹' + total.toFixed(2));
            }
        });
    }

    $('#fromDate, #toDate').on('change', function () {
        const dateVal = $(this).val();
        if (dateVal) {
            const dateObj = new Date(dateVal);
            const month = dateObj.getMonth() + 1;
            const year = dateObj.getFullYear();
            $('#filterMonth').val(month);
            $('#filterYear').val(year);
        }
    });

    $('#filterMonth, #filterYear').on('change', function () {
        if (filterApplied) {
            $('#fromDate').val('');
            $('#toDate').val('');
        }
    });

    $('#filterBtn').click(function () {
        filterApplied = true;
        loadPaidInvoices();
    });

    $('#resetBtn').click(function () {
        $('#fromDate, #toDate, #filterMonth, #filterYear').val('');
        filterApplied = false;
        loadPaidInvoices();
    });

    loadPaidInvoices();
});
</script>
