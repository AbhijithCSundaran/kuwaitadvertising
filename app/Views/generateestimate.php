<?php include "common/header.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Cash Estimate</title>
  <style>
    .outer-container {
      width: fit-content;
      margin: auto;
      padding: 15px;
      background-color: #991b36;
    }

    .container {
      width: 720px;
      border: 5px solid #000;
      border-radius: 23px;
      padding: 20px;
      position: relative;
      background: url('<?= ASSET_PATH ?>assets/images/invoice-bg.png') no-repeat;
      background-size: 30%;
      background-position: 52% 60%;
      background-color: white;
    }

    .top-heading {
      text-align: center;
      margin-bottom: 5px;
    }

    .top-heading img {
      width: 138px;
    }
    .bottom-bar {
      text-align: center;
      font-size: 12px;
      color: white;
      background-color: #991b36;
      padding: 3px;
      margin-top: 0px;
    }

     .total-tab{
        width: auto; float: right; 
        border-collapse: collapse;
    }
    .totals{
        padding: 4px 12px; 
        text-align: right;
    }

     .total-td{
        background-color: #cfc7c7ff; 
        color: #131212ff; font-weight: 
        bold; padding: 6px 12px; 
        text-align: right;
        border: 1px solid black;
    }

    @media print {
      * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
      }

      .no-print,
      .header,
      .footer,
      .sidebar,
      .navbar {
        display: none !important;
      }

      body {
        margin: 0;
        padding: 0;
        font-size: 12px;
        line-height: 1;
      }
    }
  </style>
</head>
<body>
  <div class="outer-container">
    <div class="no-print" style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
      <button onclick="window.print()"
        style="background-color: #991b36; color: white; padding: 8px 16px; border: none; border-radius: 5px;">
        Print
      </button>
      <button onclick="window.location.href='<?= base_url('estimate/edit/' . $estimate['estimate_id']) ?>'"
            style="background-color: #a1263a; color: white; padding: 8px 16px; border: none; border-radius: 5px; margin-left: 10px;">
            Discard
        </button>
      <?php if (isset($estimate['is_converted']) && $estimate['is_converted'] == 1): ?>
        <button disabled
            style="background-color:white; color: black; padding: 8px 16px; border: none; border-radius: 5px; margin-left: 10px;">
            Converted
        </button>
        <?php else: ?>
        <button onclick="window.location.href='<?= base_url('invoice/convertFromEstimate/' . $estimate['estimate_id']) ?>'"
            style="background-color: #a1263a; color: white; padding: 8px 16px; border: none; border-radius: 5px; margin-left: 10px;">
            Convert Invoice
        </button>
      <?php endif; ?>
    </div>
    <div class="container">
      <div class="top-heading" style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
        <span style="font-size: 15px; font-weight: bold;">
            <?= esc($company['company_name']) ?>
        </span>

        <?php if (!empty($company['company_logo'])): ?>
            <img src="<?= base_url('public/uploads/' . $company['company_logo']) ?>" 
                alt="Company Logo" style="max-height: 50px; width: 25%;">
        <?php endif; ?>

        <span style="font-size: 15px; font-weight: bold; direction: rtl;">
            <?= esc($company['company_name_ar'] ?? '') ?>
        </span>
      </div>
      <hr>
      <div class="row align-items-center" style="margin-bottom: 10px;">
        <div class="col-4 text-start">
          <div>
            <label style="font-weight: bold; margin-right: 4px;">No / رقم :</label>
            <input type="text" readonly value="<?= esc($estimate['estimate_id']) ?>"
              style="display: inline-block; width: 87px; height: 23px; text-align:left;">
          </div>
        </div>
        <div class="col-4 text-center">
          <div
            style="background-color: #991b36; color: white; font-weight: bold; padding: 3px 30px; display: inline-block; border-radius: 4px; font-size: 13px;">
            تسعيرة <br>QUOTATION
          </div>
        </div>
        <div class="col-4 text-end">
          <div style="white-space: nowrap;">
            <label style="font-weight: bold; margin-right: 6px;">Date / التاريخ:</label>
            <input type="text" readonly value="<?= date('d-m-Y', strtotime($estimate['date'])) ?>"
              style="width: 90px; height: 23px; text-align: center;">
          </div>
        </div>
      </div>
      <div class="row mt-3">
        <div class=" col-6">
            <p><strong>To/إلى:</strong></p>
            <p><?= esc($estimate['customer_name'] ?? '') ?></p>
            <p><?= esc($estimate['customer_address'] ?? '') ?></p>
        </div>
        <div class=" col-6 text-end">
            <p><strong>From/ من:</strong></p>
            <p><?= ucwords(strtolower(esc($user_name ?? ''))) ?></p>
            <p><?= ucwords(strtolower(esc($role_name ?? ''))) ?></p>
        </div>
      </div>
      <table class="generate-table ">
        <thead class="thead-dark">
            <tr>
                <th rowspan="2" style="width: 10%;">رقم<br>Sl No</th>
                <th rowspan="2" style="width: 38%;"> التفاصيل<br>Description</th>
                <th rowspan="2" style="width: 15%;">سعر الوحدة<br>Unit Price</th>
                <th rowspan="2" style="width: 10%;">الكمية<br>Quantity</th>
                <th rowspan="2" style="width: 20%;">المبلغ الإجمالي<br>Total Amount</th>
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
      <div class=" d-flex">
        <div class="col-6 terms mt-3" style="font-size:11px;">
            <strong>TERMS & CONDITIONS</strong><br>
            1. This estimate is valid for 60 days.<br>
            2. Additional amount will be added according to the requirements.<br>
            3. Full payment is required to process the order.<br>
            4. Cancellation of processed order will not be accepted.
        </div>
        <div class="col-6 mt-3" style="font-size:12px;">
          <div class="text-end ">
            <table class="total-tab">
              <tbody>
                <tr>
                    <td class="totals">SUBTOTAL</td>
                    <td class="totals"><?= number_format($grandTotal, 2) ?> KWD</td>
                </tr>
                <tr>
                    <td class="totals">DISCOUNTS</td>
                    <td class="totals"><?= number_format($estimate['discount'] ?? 0, 2) ?> KWD</td>
                </tr>
                <tr>
                    <td class="total-td">
                        TOTAL   
                    </td>
                    <td class="total-td">
                        <?= number_format($estimate['total_amount'] ?? 0, 2) ?> KWD
                    </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="footer-f">
        If you have any queries about this estimate, please contact<br>
            (<?= esc($company['company_name']) ?>,
            <?= esc($company['email']) ?>,
            <?= esc($company['phone']) ?>)<br>
        <strong>Thank You For Your Business!</strong>
      </div>
    </div>
    <!-- Bottom Bar -->
    <div class="bottom-bar">
      <div style="direction: rtl; text-align: center;"><?= esc($company['address_ar'] ?? '') ?></div>
      <div style="direction: ltr; text-align: center;"><?= esc($company['address'] ?? '') ?></div>
      <div style="margin-top: 5px;">
        📞 <?= esc($company['phone'] ?? '') ?> &nbsp;&nbsp; | &nbsp;&nbsp;
        📧 <a href="mailto:<?= esc($company['email'] ?? '') ?>" style="color: white; text-decoration: none;">
              <?= esc($company['email'] ?? '') ?>
            </a>
      </div>
    </div>
  </div>
</body>
</html>
</div>
<?php include "common/footer.php"; ?>
