<?php include "common/header.php"; ?>

<style>
    table tbody td {
        font-size: 14px;
        vertical-align: middle;
    }

    .action-btn {
        font-size: 18px;
        cursor: pointer;
        margin-right: 8px;
    }

    .text-view {
        color: #0da2c7;
    }

    .text-print {
        color: green;
    }
    table thead th {
    font-weight: 700 !important;
    color: #000; /* darker text */
}
</style>

<div class="form-control mb-3 right_container">
    <div class="alert d-none text-center position-fixed" role="alert"></div>

    <div class="row align-items-center mb-2">
        <div class="col-md-6">
            <h3 class="mb-0">Invoice Transactions</h3>
        </div>
    </div>

    <input type="hidden" id="invoice_id" value="<?= $invoice_id ?>">

    <div class="table-responsive">
        <table class="table table-bordered" id="transactionTable" style="width:100%">
            <thead>
                <tr>
                    <th>Sl No</th>
                    <th>Paid Amount</th>
                    <th>Total Paid Amount</th>
                    <th>Invoice Amount</th>
                    <th>Payment Mode</th>
                    <th>Transaction Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="transactionBody"></tbody>
        </table>
    </div>
</div>
</div>


<?php include "common/footer.php"; ?>

<script>
    $(document).ready(function () {

        const invoice_id = $("#invoice_id").val();

        loadTransactions(invoice_id);

        function loadTransactions(invoice_id) {

            $.post(
                "<?= base_url('invoice/transactionListJson') ?>",
                { invoice_id: invoice_id },
                function (res) {
                    let tbody = "";
                    let sl = 1;

                    if (!res.data || res.data.length === 0) {
                        tbody = `
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No Transactions Found
                            </td>
                        </tr>`;
                    } else {
                        res.data.forEach(row => {

                            // AUTO-DETECT the correct ID key
                            const id =
                                row.receipt_id ??
                                row.transaction_id ??
                                row.rv_id ??
                                row.id ??
                                row.voucher_id ??
                                null;

                            // If ID is still null → highlight error in table
                            if (!id) {
                                console.log("ERROR: No ID found in row:", row);
                            }

                            const viewUrl = "<?= base_url('receiptvoucher/view/') ?>" + row.transaction_id;
                            const printUrl = "<?= base_url('receiptvoucher/index/') ?>" + row.transaction_id;



                            tbody += `
<tr>
    <td>${sl++}</td>
    <td>${parseFloat(row.paid_amount).toFixed(3)}</td>
    <td>${parseFloat(row.partial_paid_amount).toFixed(3)}</td>
    <td>${parseFloat(row.invoice_amount).toFixed(3)}</td>
    <td>${formatMode(row.payment_mode)}</td>
    <td>${formatDate(row.created_at)}</td>

    <td>
        <div class="d-flex gap-2">
            <a href="${viewUrl}" title="View" style="color: rgb(13, 162, 199);">
                <i class="bi bi-eye-fill"></i>
            </a>

            <a href="${printUrl}" title="Print" style="color: green;">
                <i class="bi bi-printer-fill"></i>
            </a>
        </div>
    </td>
</tr>`;


                        });
                    }

                    $("#transactionBody").html(tbody);
                },
                "json"
            );
        }

        function formatMode(mode) {
            switch (mode) {
                case 'cash': return "Cash";
                case 'knet': return "KNET";
                case 'cheque': return "Cheque";
                case 'mixed': return "Mixed";
                default: return mode;
            }
        }

        function formatDate(dateString) {
    if (!dateString) return "";

    const d = new Date(dateString);

    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = String(d.getFullYear()).slice(-2); // last 2 digits

    return `${day}-${month}-${year}`;
}


    });
</script>