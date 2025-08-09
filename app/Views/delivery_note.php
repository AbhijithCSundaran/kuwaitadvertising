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
      /* margin: 40px; */
    }

    .invoice-container {
      width: 99%;
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
      text-align: right !important;
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
      color: #0a0a0a8d;
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
      color: #a1263a;
    }

    .delivery-date {
      font-size: 14px;
      font-weight: bold;
      color: #a1263a;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    table thead {
      background: #a1263a;
      color: white;
    }

    table th,
    table td {
      border: 1px solid #ccc;
      padding: 8px;
      font-size: 14px;
      text-align: center;
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
  .delivery-value {
    margin-left: 2px; 
    display: inline-block;
  }


    @media print {
    * {
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }

    .no-print,
    .header,
    .footer,
    .navbar,
    .sidebar {
      display: none !important;
    }

    .top-section {
      display: flex !important;
      justify-content: space-between !important;
      align-items: flex-start !important;
    }

    .logo-section {
      text-align: right !important;
      width: 50% !important;
    }

    .address {
      width: 50% !important;
    }

    .info-section {
      display: flex !important;
      justify-content: space-between !important;
    }

    .delivery-date {
      text-align: right !important;
      width: 50% !important;
    }

    .ship-to {
      width: 50% !important;
    }
      .signature-section, .signature-box {
    page-break-inside: avoid;
  }
  
    }
  </style>
  </head>
  <body>
    <div class="right_container">
      <div class="no-print" style="text-align: right; margin-bottom: 20px;">
    <button onclick="window.print()" class="btn btn-sm btn-primary"> Print</button>
  <button onclick="downloadPDF()" class="btn btn-sm btn-success"> Download PDF</button>
  </div>
    <div class="invoice-container">
      <div class="top-section">
        <div class="address">
          Al-Shuwaikh Area, 3<br>
          <em>Behind Sultan center - 4th ring Road</em><br>
          Tel: +965 600 60 102
        </div>
        <div class="logo-section">
          <img src="<?php echo ASSET_PATH; ?>assets/images/invoice-logo.png" alt="Invoicelogo">
        </div>
      </div>
  
      <div class="delivery-note-title">
        DELIVERY NOTE NO. <?= esc($invoice['invoice_id']) ?>
      </div>
      <div class="info-section">
        <div class="ship-to">
          <b>SHIP TO :</b><br>
          <div><?= nl2br(esc($invoice['shipping_address'] ?? '-')) ?></div>
        </div>
        <div class="delivery-date">
          Delivery Date: <span id="deliveryDate" class="delivery-value" style="color: black;"></span>
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
          <?php $i = 1;
          foreach ($items as $item): ?>
            <tr>
              <td style="text-align: left;"><?= $i++ ?></td>
              <td style="text-align: left;"><?= esc($item['item_name'] ?? '-') ?></td>
              <td style="text-align: left;"><?= esc($item['price'] ?? '-') ?></td>
              <td style="text-align: left;"><?= esc($item['quantity']) ?></td>
              <td>--</td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <div style="margin-top: 40px;">
          <table style="width: 100%; border: 1px solid #000; border-collapse: collapse;">
            <tr>
              <td style="width: 50%; border-right: 1px solid #000; padding: 10px;">
                <p style="text-align: left;"><strong>Received by :</strong></p>
                <p style="text-align: left;"><strong>Signature :</strong></p>
              </td>
              <td style="width: 50%; padding: 10px; position: relative;">
                <!-- <div
                  style="position: absolute; top: -12px; right: 10px; background: white; font-weight: bold; font-size: 13px; padding: 0 6px;">
                  For Al Shaya International Printng Co
                </div> -->
                <p style="text-align: left;"><strong>Issued by :</strong></p>
                <p style="text-align: left;"><strong>Signature :</strong></p>
              </td>
            </tr>
            <div class="d-flex w-100 position-relative">
              <div class="col-6 ms-auto">
                <hr>
                 <div class="text-center" style="font-size: 14px;">For Al Shaya International Printing Co</div>
              </div>
            </div>
          </div>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
<?php include "common/footer.php"; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
  function downloadPDF() {
    const element = document.querySelector('.invoice-container');
    const opt = {
      margin: [0.5, 0.5, 0.5, 0.5],
      filename: 'DeliveryNote-<?= $invoice['invoice_id'] ?>.pdf',
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save();
  }

  function formatDateToDDMMYYYY(date) {
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    return `${day}.${month}.${year}`;
  }

  
  document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('deliveryDate').textContent = formatDateToDDMMYYYY(new Date());
  });

  // window.onbeforeprint = function () {
  //   document.getElementById('deliveryDate').textContent = formatDateToDDMMYYYY(new Date());
  // };
</script>
