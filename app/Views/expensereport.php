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
    }

    #plainExpenseTable th:nth-child(4),
    #plainExpenseTable td:nth-child(4) {
        width: 20% !important;
        text-align: left;
    }

    #plainExpenseTable th:nth-child(5),
    #plainExpenseTable td:nth-child(5) {
        width: 20% !important; /* Reduced from 30% to 20% */
    }
</style>


<div class="form-control mb-3 right_container">
    <div class="alert d-none text-center position-fixed" role="alert"></div>

    <h3 class="mb-3">Expense Report</h3>

    <div class="row mb-3">
        <div class="col-md-3">
            <input type="date" id="filterDate" class="form-control" placeholder="Filter by Date">
        </div>
        <div class="col-md-3">
            <select id="filterMonth" class="form-control">
                <option value="">Filter by Month</option>
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>"><?= date('F', mktime(0, 0, 0, $m, 10)) ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select id="filterYear" class="form-control">
                <option value="">Filter by Year</option>
                <?php
                    $currentYear = date('Y');
                    $endYear = $currentYear + 5;
                    for ($y = $endYear; $y >= 2000; $y--): ?>
                        <option value="<?= $y ?>"><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-3">
            <button id="filterBtn" class="btn btn-primary">Apply Filter</button>
            <button id="resetBtn" class="btn btn-secondary">Reset</button>
        </div>
    </div>

    <table class="table table-bordered" id="plainExpenseTable">
        <thead>
            <tr>
                <th>SI No</th>
                <th>Date</th>
                <th>Particular</th>
                <th>Amount</th>
                <th>Payment Mode</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total:</th>
                <th id="totalAmount">₹0.00</th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>
                </div>
<?php include "common/footer.php"; ?>

<script>
$(document).ready(function () {
    function loadExpenses() {
        $.ajax({
            url: "<?= base_url('expense/getExpenseReportAjax') ?>",
            type: "POST",
            data: {
                date: $('#filterDate').val(),
                month: $('#filterMonth').val(),
                year: $('#filterYear').val()
            },
            dataType: "json",
            success: function (data) {
                let rows = '';
                let total = 0;

                if (data.length > 0) {
                    data.forEach((item, index) => {
                        total += parseFloat(item.amount);
                        rows += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.date}</td>
                                <td>${item.particular}</td>
                                <td>₹${parseFloat(item.amount).toFixed(2)}</td>
                                <td>${item.payment_mode}</td>
                            </tr>
                        `;
                    });
                } else {
                    rows = `<tr><td colspan="5" class="text-center">No records found.</td></tr>`;
                }

                $('#plainExpenseTable tbody').html(rows);
                $('#totalAmount').text('₹' + total.toFixed(2));
            }
        });
    }

    $('#filterBtn').click(function () {
        loadExpenses();
    });

    $('#resetBtn').click(function () {
        $('#filterDate').val('');
        $('#filterMonth').val('');
        $('#filterYear').val('');
        loadExpenses();
    });

    // Initial load
    loadExpenses();
});
</script>
