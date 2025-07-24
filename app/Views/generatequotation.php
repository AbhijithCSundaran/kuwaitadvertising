<?php include "common/header.php"; ?>
<style>
    @media print {
        .no-print,
        .navbar,
        .footer,
        .sidebar {
            display: none !important;
        }

        body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .estimate-header,
        .estimate-footer {
            text-align: center;
        }
        .company-logo{
            text-align: right;
        }
       
    }
     .company-logo img {
            max-height: 160px;
            width: auto;
        }
         .estimate-title {
               text-align: center;
                font-size: x-large;
        }
        .table-bordered th, .table-bordered td {
            border: 2px solid black;
            padding: 5px 10px;
            font-weight: bold;
        }
         .items-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    .items-table th {
      background-color: #da1c1c;
      color: #fff;
      padding: 8px;
      font-size: 13px;
      border: 1px solid #ccc;
    }

    .items-table td {
      border: 1px solid #ccc;
      padding: 6px;
      height: 30px;
    }
    .name-table{
        padding-top: 35px;
    }
</style>
 <div class="right_container">
    <div class="no-print" style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
      <button onclick="window.print()"
        style="background-color: #991b36; color: white; padding: 8px 16px; border: none; border-radius: 5px;">
        🖨️ Print
      </button>
      <button onclick="window.location.href='<?= base_url('estimate/edit/' . $estimate['estimate_id']) ?>'"
        style="background-color: #991b36; color: white; padding: 8px 16px; border: none; border-radius: 5px; margin-left: 10px;">
        Discard
      </button>

    </div>
    <div class="estimate-container">

      <!-- Header -->
      <div class="d-flex col-12 mb-3">
        <div class="col-6">
          <div class="address">
            <em>Al-Shuwaikh Area, 3</em><br>
            <em>Behind Sultan Center - 4th Ring Road</em><br>
            <em>Tel: +965 600 60 102</em>
          </div>
        </div>

        <!-- Company Logo-->
        <div class="col-6 d-flex justify-content-end ">
          <div class="company-logo">
            <img src="<?php echo ASSET_PATH; ?>assets/images/invoice-logo.png" alt="Invoicelogo">
            <!-- <div class="company-name">Al Shaya International Printing Co</div>
            <div class="company-arabic">شركة الشايع للطباعة الدولية</div> -->
          </div>
        </div>
         </div>
        <div class="estimate-title"><strong><u>Quotation</u></strong></strong></div>
   
        <div class="col-md-6">
             <strong>TO: M/S. <?= esc($estimate['customer_name'] ?? 'Customer Name') ?></strong><br>
    </div>
    <hr>
    <div class="row mt-4">
        <div class="col-md-6">
            Person Name:
            <?= esc($estimate['customer_name'] ?? '') ?><br>
            Business Name:<br>
            Address:
            <?= esc($estimate['customer_address'] ?? '') ?><br>
            Contact no:
        </div>
       <div class="col-md-6 d-flex justify-content-end">
    <table class="table table-bordered w-auto mb-0">
        <tr>
            <th>Quote Date</th>
            <td><?= date('d-m-Y', strtotime($estimate['date'])) ?></td>
        </tr>
        <tr>
            <th>Quote No.</th>
            <td><?= $estimate['estimate_id'] ?></td>
        </tr>
    </table>
</div>

    </div>

    <table class=" items-table table-striped">
        <thead>
            <tr>
                <th>SR. No</th>
                <th>DESCRIPTION</th>
                <th>QTY</th>
                <th>UNIT PRICE(KD)</th>
                <th>Total Amount(KD)</th>
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
                <td><?= esc($item['quantity']) ?></td>
                <td><?= number_format($item['price'], 3) ?></td>
                <td><?= number_format($item['total'], 3) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

   <!-- Terms -->
    <div class="mt-4">
        <strong>Advance 70% Balance 30% After Delivery</strong>
    </div>

    <!-- In Words and Totals Row -->
    <div class="row mt-3">
        <!-- Left: In Words -->
        <div class="col-md-6">
            <strong>In Words:</strong><br>
            <?= ucwords($amountInWords ?? '') ?>
        </div>

        <!-- Right: Totals and Signatures -->
        <div class="col-md-6 ">
        <div style="width: 100%; display: flex; justify-content: flex-end;">
                <div style="text-align: right;">
                    <div style="font-weight: bold; color: #2c3e50;">SUBTOTAL</div>
                    <div style="border-top: 2px solid black; margin: 2px 0 4px 0;"></div>
                    <div style="display: flex; align-items: center; justify-content: flex-end;">
                        <div style="color: red; font-weight: bold; margin-right: 5px;">Grand total</div>
                        <div style="background-color: #f08080; padding: 4px 10px; font-weight: bold;">
                            <?= number_format($grandTotal ?? 0, 3) ?> KWD
                        </div>
                    </div>
                </div>
        </div>
         <div class="name-table col-md-12 d-flex justify-content-end">
            <table class="table table-bordered w-auto mb-0 pt-6">
                <tr>
                    <th class="text-center">Authorized Signatory</th>
                    <td></td>
                </tr>
                <tr>
                    <td><strong>Receipient Name:</strong></td>
                    <td></td>
                </tr>
                <tr>
                    <td><strong>Signature:</strong></td>
                    <td></td>
                </tr>
            </table>
        </div>
        </div>
    </div>

</div>
</div>
            </div>
<?php include "common/footer.php"; ?>