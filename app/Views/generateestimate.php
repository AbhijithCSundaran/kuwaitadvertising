<?php include "common/header.php"; ?>
<style>
    .bg-img{
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        z-index: -1;
    }
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
      <?php if (isset($estimate['is_converted']) && $estimate['is_converted'] == 1): ?>
    <button disabled
        style="background-color: orange; color: white; padding: 8px 16px; border: none; border-radius: 5px; margin-left: 10px;">
        Converted
    </button>
<?php else: ?>
    <button onclick="window.location.href='<?= base_url('invoice/convertFromEstimate/' . $estimate['estimate_id']) ?>'"
        style="background-color: #a1263a; color: white; padding: 8px 16px; border: none; border-radius: 5px; margin-left: 10px;">
        Convert Invoice
    </button>
<?php endif; ?>


    </div>
    <div class="background-image">
        <img class="bg-img" src="<?php echo ASSET_PATH; ?>assets/images/estimate-6-bg.jpg" alt="estimateimage">
        <div class="print_wrpr">
            <div class="row">

                <div class=" col-6">
                    <h3><strong>Quotation</strong></h3>
                </div>

                <div class=" col-6 text-end">
                    <p><strong>Date:</strong> <?= date('d-m-Y', strtotime($estimate['date'])) ?></p>
                </div>
            </div>
            <div class="row mt-3">
                <div class=" col-6">
                    <p><strong>To:</strong></p>
                    <p><?= esc($estimate['customer_name'] ?? '') ?></p>
                    <p><?= esc($estimate['customer_address'] ?? '') ?></p>
                </div>
                <div class=" col-6 text-end">
                    <p><strong>From:</strong></p>
                   <p><?= ucwords(strtolower(esc($user_name ?? ''))) ?></p>
                    <p><?= ucwords(strtolower(esc($role_name ?? ''))) ?></p>
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
            <div class=" d-flex">
                <div class="col-6 terms mt-5">
                    <strong>TERMS & CONDITIONS</strong><br>
                    1. This estimate is valid for 60 days.<br>
                    2. Additional amount will be added according to the requirements.<br>
                    3. Full payment is required to process the order.<br>
                    4. Cancellation of processed order will not be accepted.
                </div>

                <div class="col-6 mt-4">
                      <div class="text-end mt-4">

                        <!-- SUBTOTAL -->
                        <div class="d-flex justify-content-end mb-1">
                            <div class="me-4 ">SUBTOTAL</div>
                            <div><?= number_format($grandTotal, 2) ?> KWD</div>
                        </div>

                        <!-- DISCOUNTS -->
                        <div class="d-flex justify-content-end mb-2">
                            <div class="me-4">DISCOUNTS</div>
                            <div><?= number_format($estimate['discount'] ?? 0, 2) ?> KWD</div>
                        </div>

                        <!-- TOTAL in colored box -->
                        <div class="d-flex justify-content-end">
                            <div style="background-color: #a1263a; color: #fff; padding: 8px 16px; min-width: 220px;" class="d-flex justify-content-between">
                                <div class="fw-bold">TOTAL</div>
                                <div class="fw-bold"><?= number_format($estimate['total_amount'] ?? 0, 2) ?> KWD</div>
                            </div>
                        </div>

                    </div>
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
<!-- <script>
    function printEstimate() {
        var printContents = document.getElementById('printArea').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        // document.body.innerHTML = originalContents;
        location.reload();
    }

</script> -->