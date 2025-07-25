<?php include "common/header.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Delivery Note</title>
  <style>

    body {
      background: #FFFFFF;
      margin: 40px;
    }
    .invoice-container {
      /* width: 100%; 
      width: 730px;  */
      margin: auto;
      border: 1px solid #ddd;
      padding: 30px; 
    }
    .top-section {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
    }

    .address {
      font-size: 14px;
      line-height: 1.5;
    }

    .logo-section {
      text-align: right;
    }

    .logo-section img {
    width: 265px;
    margin-top: -29px;
}

    .company-name {
      font-size: 18px;
      font-weight: bold;
      line-height: 1.2;
    }

    .delivery-note-title {
      text-align: center;
      color: #0a0a0aff;
      font-weight: bold;
      font-size: 18px;
      margin: 20px 0;
    }

    .info-section {
      display: flex;
      justify-content: space-between;
      margin-bottom: 20px;
    }

   .ship-to {
  font-size: 14px;
  word-wrap: break-word;
  word-break: break-word;
  white-space: normal;
  width: 50%;
}


    .ship-to b {
      color: #D32F2F;
    }

    .delivery-date {
      font-size: 14px;
      font-weight: bold;
      color: #D32F2F;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    table thead {
      background: #D32F2F;
      color: white;
    }

    table th, table td {
      border: 1px solid #ccc;
      padding: 8px;
      font-size: 14px;
      text-align: left;
    }

    .signature-section {
      display: flex;
      justify-content: space-between;
      margin-top: 40px;
      font-size: 14px;
    }

    .signature-box {
      width: 48%;
      border-top: 1px solid #000;
      padding-top: 10px;
    }

    .signature-box p {
      margin: 5px 0;
    }

    

    @media (max-width: 768px) {
      .top-section,
      .info-section,
      .signature-section {
        flex-direction: column;
        gap: 20px;
      }

      .signature-box {
        width: 100%;
      }

      .logo-section {
        text-align: left;
      }
    }
  </style>
  
</head>
<body>
  <div class="invoice-container">
  <div class="top-section">
    <div class="address">
      Al-Shuwaikh Area, 3<br>
      <em>Behind Sultan center - 4th ring Road</em><br>
      Tel: +965 600 60 102
    </div>
    <div class="logo-section">
      <img src="<?php echo ASSET_PATH; ?>assets/images/invoice-logo.png" alt="Invoicelogo">
      <!-- <div class="company-name">
        شركة مطبعة الشايع الدولية<br>
        Al Shaya <span style="color:#1976D2">International Printing</span> <span style="color:#388E3C">Co</span>
      </div> -->
    </div>
  </div>

  <div class="delivery-note-title">
  DELIVERY NOTE NO. <?= esc($invoice['invoice_id']) ?>
</div>


  <div class="info-section">
    <div class="ship-to">
  <b>SHIP TO :</b><br>
  <div><?= nl2br(esc($invoice['shipping_address'] ?? '-')) ?></div>
<!-- Person Name: <?= esc($customer['contact_person'] ?? '-') ?><br> -->
<!-- Address: <?= esc($invoice['shipping_address'] ?? '-') ?><br> -->
<!-- Contact Number: <?= esc($customer['phone'] ?? '-') ?> -->

    </div>
    <div class="delivery-date">
  Delivery Date: <?= date('d.m.Y', strtotime($invoice['invoice_date'] ?? '')) ?>
</div>

  </div>

  <table>
    <thead>
      <tr>
        <th>SR. NO</th>
        <th>DESCRIPTION</th>
        <th>Unit</th>
        <th>Qty</th>
        <th>LOCATION</th>
      </tr>
    </thead>
    <tbody>
      <?php $i = 1; foreach ($items as $item): ?>
      <tr>
        <td><?= $i++ ?></td>
        <td><?= esc($item['item_name']) ?></td>
        <td><?= esc($item['price'] ?? '-') ?></td>
        <td><?= esc($item['quantity']) ?></td>
        <td>--</td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  <div style="margin-top: 40px;">
  <table style="width: 100%; border: 1px solid #000; border-collapse: collapse;">
    <tr>
      <td style="width: 50%; border-right: 1px solid #000; padding: 10px;">
        <p><strong>Received by :</strong></p>
        <p><strong>Signature :</strong></p>
      </td>
      <td style="width: 50%; padding: 10px; position: relative;">
        <div style="position: absolute; top: -12px; right: 10px; background: white; font-weight: bold; font-size: 13px; padding: 0 6px;">
          For Al Shaya International Printng Co
        </div>
        <p><strong>Issued by :</strong></p>
        <p><strong>Signature :</strong></p>
      </td>
    </tr>
  </table>
</div>
  </div>
</body>
</html>
<?php include "common/footer.php"; ?>