<?php include "common/header.php"; ?>
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
                <?php for ($y = date('Y'); $y >= 2000; $y--): ?>
                    <option value="<?= $y ?>"><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="col-md-3">
            <button id="filterBtn" class="btn btn-primary">Apply Filter</button>
            <button id="resetBtn" class="btn btn-secondary">Reset</button>
        </div>
    </div>

    <table class="table table-bordered" id="reportTable">
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
    </table>
</div>
</div>
<?php include "common/footer.php"; ?>

<script>
$(document).ready(function () {
    let reportTable = $('#reportTable').DataTable({
        ajax: {
            url: "<?= base_url('expense/getExpenseReportAjax') ?>",
            type: "POST",
            data: function (d) {
                d.date = $('#filterDate').val();
                d.month = $('#filterMonth').val();
                d.year = $('#filterYear').val();
            },
            dataSrc: ""
        },
        columns: [
            { data: null },
            { data: "date" },
            { data: "particular" },
            { data: "amount" },
            { data: "payment_mode" }
        ],
        order: [[1, 'desc']],
        columnDefs: [{ orderable: false, targets: 0 }]
    });

    reportTable.on('draw.dt', function () {
        reportTable.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    });

    $('#filterBtn').click(function () {
        reportTable.ajax.reload();
    });

    $('#resetBtn').click(function () {
        $('#filterDate').val('');
        $('#filterMonth').val('');
        $('#filterYear').val('');
        reportTable.ajax.reload();
    });
});
</script>
