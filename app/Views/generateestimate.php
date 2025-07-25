<?php include "common/header.php"; ?>
<style>
    @media print {
       *{
         -webkit-print-color-adjust: exact !important;
        /* Safari/Chrome */
        print-color-adjust: exact !important;
        /* Firefox */
       } .no-print,
        .navbar,
        .footer,

        .sidebar {
            display: none !important;
        }

        body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        } */

        .right_container {
            width: 100%;
            margin-left: auto;
            padding: 25px;
            transform: scale(0.5);
        }

        .background-box {
            background-image: url("../images/estimate-6-bg.jpg");
            background-size: 100%;
            background-position: top;
            background-repeat: no-repeat;
        }
    }
</style>
<div class="right_container">
    <div class="no-print" style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
        <button onclick="window.print()"
            style="background-color: #a1263a; color: white; padding: 8px 16px; border: none; border-radius: 5px;">
            🖨️ Print
        </button>
        <button onclick="window.location.href='<?= base_url('estimate/edit/' . $estimate['estimate_id']) ?>'"
            style="background-color: #a1263a; color: white; padding: 8px 16px; border: none; border-radius: 5px; margin-left: 10px;">
            Discard
        </button>

        </div>


    <div class="m-auto background-box" id="printArea">
        <div class="print_wrpr">
            <div class="row">

                <div class="col-12 col-md-6">
                    <h3><strong>Quotation</strong></h3>
                </div>

                <div class="col-12 col-md-6 text-end">
                    <p><strong>Date:</strong> <?= date('d-m-Y', strtotime($estimate['date'])) ?></p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12 col-md-6">
                    <p><strong>To:</strong></p>
                    <p><?= esc($estimate['customer_name'] ?? '') ?></p>
                    <p><?= esc($estimate['customer_address'] ?? '') ?></p>
                </div>
                <div class="col-12 col-md-6 text-end">
                    <p><strong>From:</strong></p>
                    <p><?= esc($user_name ?? '') ?></p>
                </div>
            </div>
            <!-- <div class="table-responsive"> -->
                <table class="generate-table ">
                    <thead class="thead-dark">
                        <tr>
                            <th>Sl No</th>
                            <th>Description</th>
                            <th>Unit Price</th>
                            <th>Quantity</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $si = 1;
                        $grandTotal = 0;
                        foreach ($items as $item):
                            $grandTotal += $item['total'];
                            ?>
                            <tr>
                                <td><?= $si++ ?></td>
                                <td><?= esc($item['description']) ?></td>
                                <td><?= number_format($item['price'], 2) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td><?= number_format($item['total'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <!-- </div> -->
            <div class="d-flex">
                <div class="col-8 terms mt-5">
                    <strong>TERMS & CONDITIONS</strong><br>
                    1. This estimate is valid for 60 days.<br>
                    2. Additional amount will be added according to the requirements.<br>
                    3. Full payment is required to process the order.<br>
                    4. Cancellation of processed order will not be accepted.
                </div>

                <div class="col-4 mt-3">
                    <!-- <div class="table-responsive"> -->
                        <table class="table summary-table table-borderless w-100 text-end">
                            <tbody>
                                <tr>
                                    <td class="label">SUBTOTAL</td>
                                    <td><?= number_format($grandTotal, 2) ?> KWD</td>
                                </tr>
                                <tr>
                                    <td class="label">DISCOUNTS</td>
                                    <td><?= number_format($estimate['discount'] ?? 0, 2) ?>%</td>
                                </tr>
                                <tr>
                                    <td class="label total">TOTAL</td>
                                    <td class="total">
                                        <?= number_format($estimate['total_amount'] ?? 0, 2) ?> KWD
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    <!-- </div> -->
                </div>

            </div>

            <div class="footer-f">
                If you have any queries about this estimate, please contact<br>
                (Name, Email, Phone)<br>
                <strong>Thank You For Your Business!</strong>
            </div>
        </div>
    </div>
</div>
</div>
<?php include "common/footer.php"; ?>
<script>
    function printEstimate() {
        var printContents = document.getElementById('printArea').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        // document.body.innerHTML = originalContents;
        location.reload();
    }

</script>