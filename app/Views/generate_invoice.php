<?php include "common/header.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Invoice</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #fff;
      color: #000;
      font-size: 14px;
      /* margin: 40px; */
    }

    .invoice-container {
      /* width: 100%; */
      /* width: 730px; */
      margin: auto;
      border: 1px solid #ddd;
      padding: 30px; 
    }

    .invoice-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
    }

    .address {
      font-size: 14px;
      line-height: 1.25rem;
    }

    .company-logo {
      text-align: right;
    }

    .company-logo img {
      height: 100px;

    }

    .company-name {
      font-weight: bold;
      font-size: 16px;
    }

    .company-arabic {
      font-size: 13px;
    }

    h2.invoice-title {
      text-align: center;
      text-decoration: underline;
      margin: 20px 0 40px;
      margin-top: -1px;
      font-size: 1.45rem;
    }

    .info-table {
      border-collapse: collapse;
      font-size: 13px;
      float: right;
      margin-bottom: 20px;
    }

    .info-table td {
      border: 2px solid black;
      padding: 5px 10px;
      font-weight: bold;
    }

    .bill-ship {
      display: flex;
      justify-content: space-between;
      margin-top: 10px;
      margin-bottom: 10px;
    }

    /* .bill-to,
    .ship-to {
      width: 48%;
    } */

    .bill-to td,
    .ship-to td {
      padding: 2px 0;
    }

    .label {
      font-weight: bold;
      color: #da1c1c;
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

    .ship-bold {
      text-decoration: underline solid #000;
      font-weight: bold;
      color: #da1c1c;
    }

    .totals {
      width: 240px;
      margin-top: 10px;
      margin-left: auto;
      display: flex;
      flex-direction: column;
      justify-content: end;
    }

    .totals .totals-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .grand-total {
      font-weight: bold;
      color: #da1c1c;
      font-size: 14px;
      border-top: 2px solid #000;
    }

    .grand-total span {
      background: #fba0a0;
      color: black;
      padding: 4px 10px;
      border-bottom: 2px solid #000;
    }

    .invoice-footer-text {
      margin-top: 20px;
      font-size: 13px;
      line-height: 1.2rem;
    }

    .invoice-footer-text .cheque {
      color: #da1c1c;
      font-weight: bold;
      margin-top: 5px;
    }

    /* .invoice-footer-text .thanks {
      margin-top: 5px;
    } */

    .recipient-box {
      width: 300px;
      border: 2px solid #000;
      margin-top: 20px;
      /* float: right; */
    }

    .recipient-box.table {
      width: 100%;
      border-collapse: collapse;
    }

    .recipient-box td {
      border: 2px solid #000;
      padding: 6px;
      font-size: 13px;
    }

    .compname {
      font-size: 16px;
      text-decoration: underline 1px solid #000;
    }

    .bill-to strong,
    .ship-to strong {
      color: black;
      /* Ensure strong tags are black */
    }

    /* .totals {
  margin-top: 20px;
  max-width: 300px;
  font-size: 14px;
} */

    /* .totals-row {
  display: right;
  justify-content: space-between;
  padding: 4px 0;
  border-bottom: 1px solid #ccc;
} */

    .tot {
      font-weight: bold;
    }

    .value {
      text-align: right;
      min-width: 80px;
    }

    @media print {
      * {
        -webkit-print-color-adjust: exact !important;
        /* Safari/Chrome */
        print-color-adjust: exact !important;
        /* Firefox */
      }

      .no-print,
      header,
      footer,
      .sidebar {
        display: none !important;
      }
    }
  </style>
</head>
<body>

  <div class="right_container">
    <div class="no-print" style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
      <button onclick="window.print()"
        style="background-color: #991b36; color: white; padding: 8px 16px; border: none; border-radius: 5px;">
        🖨️ Print
      </button>
      <button onclick="window.location.href='<?= base_url('invoice/edit/' . $invoice['invoice_id']) ?>'"
        style="background-color: #991b36; color: white; padding: 8px 16px; border: none; border-radius: 5px; margin-left: 10px;">
        Discard
      </button>
      <!-- Paid/Unpaid Toggle Button -->
      <button id="paymentStatusBtn"
        style="background-color: #991b36; color: white; padding: 8px 16px; border: none; border-radius: 5px; margin-left: 10px;">
        Unpaid
      </button>
    </div>
    <div class="invoice-container">

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

      <h2 class="invoice-title">INVOICE NO. <?= esc($invoice['invoice_id']) ?></h2>



      <!-- Billing & Shipping -->
      <div class="d-flex col-12 mb-3">
        <!-- Bill To -->
        <div class="bill-to col-4 pr-3">
          <div class="label">BILL TO: <strong><?= esc($customer['name'] ?? '-') ?></strong></div>
          <div>Person Name: <strong><?= esc($customer['name'] ?? '-') ?></strong></div>
          <div>Business Name:<strong><?= esc($invoice['company_name'] ?? 'Company Name Not Found') ?></strong></div>
          <div>Address: <strong><?= esc($invoice['customer_address'] ?? '-') ?></strong></div>
          <div>Contact Number:<strong><?= esc($invoice['phone_number'] ?? '-') ?></strong></div>
        </div>

        <!-- Ship To -->
        <div class="ship-to col-4">
          <div class="label">SHIP TO:<strong><?= esc($customer['name'] ?? '-') ?></strong></div>
          <div>Person Name:<strong><?= esc($customer['name'] ?? '-') ?></strong></div>
          <div>Business Name:<strong><?= esc($invoice['company_name'] ?? 'Company Name Not Found') ?></strong></div>
          <div>Address:<strong><?= esc($invoice['shipping_address'] ?? '-') ?></strong></div>
          <div>Contact Number:<strong><?= esc($invoice['phone_number'] ?? '-') ?></strong></div>
        </div>

        <!-- Invoice Info -->
        <div class="col-4 pl-3 ml-auto">
          <table class="info-table table-sm  justify-content-end">
            <tr>
              <td>Invoice Date:</td>
              <td><?= date('d.m.Y', strtotime($invoice['invoice_date'])) ?></td>
            </tr>
            <tr>
              <td>Delivery Date:</td>
              <td>--</td>
            </tr>
            <tr>
              <td>Delivery Note No:</td>
              <td>--</td>
            </tr>
            <tr>
              <td>LPO No:</td>
              <td><?= esc($invoice['lpo_no']) ?></td>
            </tr>
          </table>
        </div>
      </div>
      <!-- Items Table -->
      <table class="items-table table-striped">
        <thead>
          <tr>
            <th>SR. NO</th>
            <th style="width: 518px;">DESCRIPTION</th>
            <th>QTY</th>
            <th>UNIT PRICE (KD)</th>
            <th>TOTAL (KD)</th>
          </tr>
          <?php
          $i = 1;
          $subtotal = 0;
          $totalDiscountAmount = 0;
          $discountPercent = isset($invoice['discount']) ? floatval($invoice['discount']) : 0;
          foreach ($items as $item):
          $price = $item['price'];
          $qty = $item['quantity'];
          $lineTotal = $price * $qty;
          $discountAmount = ($lineTotal * $discountPercent) / 100;
          $finalTotal = $lineTotal - $discountAmount;
          $subtotal += $lineTotal;
          $totalDiscountAmount += $discountAmount;
          ?>
        </thead>
          <tbody>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= esc($item['item_name'] ?? '-') ?></td>
              <td><?= esc($item['quantity']) ?></td>
              <td><?= number_format($item['price'], 3) ?></td>
              <td><?= number_format($finalTotal, 3) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
      </table>

      <!-- Totals -->
      <div class="totals">
        <div class="totals-row">
          <div class="tot">SUBTOTAL</div>
          <div class="value"><?= number_format($subtotal, 3) ?></div>
        </div>
        <div class="totals-row">
          <div class="tot">DISCOUNT</div>
          <div class="value"><?= number_format($totalDiscountAmount, 3) ?></div>
        </div>
      </div>



      <div class="totals grand-total">
        <div class="totals-row">
          <strong>Grand Total</strong>
          <span><?= number_format($subtotal - $totalDiscountAmount, 3) ?></span>
        </div>
      </div>
     <?php $grandTotal = $subtotal - $totalDiscountAmount; ?>
      <div class="amount-words">
       In Words:: <span id="amount-words"></span>
      </div>


      <!-- Footer -->
      <div class="d-flex col-12">
        <div class="invoice-footer-text col-6">
          <div class="thanks">Thank you for your business!</div>
          <div class="cheque">Please issue the cheque in the favor of:</div>
          <div class="compname"><strong>Al Shaya International Printing Co</strong></div>
        </div>
        <!-- Recipient -->
        <div class="col-6 d-flex justify-content-end">
          <table class="recipient-box">
            <tr>
              <td><strong><div class="rec-label">Received the above in good order</div></strong></td>
              <td><strong><div class="rec-label">Accountant:</div></strong></td>
            </tr>
            <tr>
              <td><strong><div class="rec-label">Recipient Name:</div></strong></td>
              <td></td>
            </tr>
            <tr>
              <td><strong><div class="rec-label">Signature:</div></strong></td>
              <td></td>
            </tr>
          </table>
        </div>
      </div>
      <!-- Delivery Note Popup -->
      <div id="deliveryNoteModal" class="modal" style="display:none; position: fixed; z-index: 9999; left: 0; top: 0;
        width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
      <div
        style="background-color: #fff; margin: 15% auto; padding: 20px; border-radius: 10px; width: 300px; text-align: center;">
          <p>Do you want to download the delivery note?</p>
          <button onclick="downloadDeliveryNote()"
            style="background-color: #28a745; color: white; border: none; padding: 8px 12px; border-radius: 5px; margin: 5px;">
            Download
          </button>
          <button onclick="closeModal()"
            style="background-color: #dc3545; color: white; border: none; padding: 8px 12px; border-radius: 5px; margin: 5px;">
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
</div>
<?php include "common/footer.php"; ?>
<script>
  function numberToWords(num) {
    const a = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
      'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
    const b = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
    num = num.toString().replace(/,/g, '');
    
    let [dinars, fils] = num.split('.');
    
    if (dinars.length > 9) return 'overflow';
    dinars = parseInt(dinars, 10);
    fils = parseInt((fils || '0').padEnd(3, '0').slice(0, 2)); // Handle fils up to 2 decimal places

      const convert = (n) => {
        if (n < 20) return a[n];
        if (n < 100) return b[Math.floor(n / 10)] + (n % 10 ? '-' + a[n % 10] : '');
        if (n < 1000) return a[Math.floor(n / 100)] + ' hundred' + (n % 100 ? ' ' + convert(n % 100) : '');
        if (n < 1000000) return convert(Math.floor(n / 1000)) + ' thousand' + (n % 1000 ? ' ' + convert(n % 1000) : '');
        if (n < 1000000000) return convert(Math.floor(n / 1000000)) + ' million' + (n % 1000000 ? ' ' + convert(n % 1000000) : '');
        return '';
    };

    let words = '';
    if (dinars > 0) words += convert(dinars) + ' Kuwaiti Dinar';
    if (fils > 0) words += (words ? ' and ' : '') + convert(fils) + ' Fils';
    return words || 'Zero';
  }


  function numberToArabicWords(num) {
  const ones = ['', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة'];
  const tens = ['', 'عشرة', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'];
  const teens = ['أحد عشر', 'اثنا عشر', 'ثلاثة عشر', 'أربعة عشر', 'خمسة عشر', 'ستة عشر', 'سبعة عشر', 'ثمانية عشر', 'تسعة عشر'];

  function convert_hundreds(n) {
    let result = '';
    const hundred = Math.floor(n / 100);
    const remainder = n % 100;

    if (hundred > 0) {
      if (hundred === 1) result += 'مائة';
      else if (hundred === 2) result += 'مائتان';
      else result += ones[hundred] + 'مائة';
    }

    if (remainder > 0) {
      if (result) result += ' و ';
      result += convert_tens(remainder);
    }

    return result;
  }

  function convert_tens(n) {
    if (n < 10) return ones[n];
    if (n >= 11 && n <= 19) return teens[n - 11];
    const ten = Math.floor(n / 10);
    const one = n % 10;

    if (one === 0) return tens[ten];
    return ones[one] + ' و ' + tens[ten];
  }

  function convert_group(n, groupName, dualName, pluralName) {
    if (n === 0) return '';
    if (n === 1) return groupName;
    if (n === 2) return dualName;
    if (n >= 3 && n <= 10) return convert_hundreds(n) + ' ' + pluralName;
    return convert_hundreds(n) + ' ' + groupName;
  }

  function convertNumber(n) {
    if (n === 0) return 'صفر';

    const million = Math.floor(n / 1000000);
    const thousand = Math.floor((n % 1000000) / 1000);
    const rest = n % 1000;

    let parts = [];
    if (million > 0) parts.push(convert_group(million, 'مليون', 'مليونان', 'ملايين'));
    if (thousand > 0) parts.push(convert_group(thousand, 'ألف', 'ألفان', 'آلاف'));
    if (rest > 0) parts.push(convert_hundreds(rest));

    return parts.join(' و ');
  }

  num = num.toString().replace(/,/g, '');
  let [dinars, fils] = num.split('.');
  dinars = parseInt(dinars || '0', 10);
  fils = parseInt((fils || '0').padEnd(3, '0').slice(0, 2));

  let words = '';
  if (dinars > 0) words += convertNumber(dinars) + ' دينار';
  if (fils > 0) words += (words ? ' و ' : '') + convertNumber(fils) + ' فلس';
  return words || 'صفر';
}

  const grandTotal = <?= json_encode(number_format($grandTotal, 3, '.', '')) ?>;

  const englishWords = numberToWords(grandTotal);
  const arabicWords = numberToArabicWords(grandTotal);

  document.getElementById("amount-words").innerHTML = `
    ${englishWords}<br><span style="font-family: 'Amiri', serif;">${arabicWords}</span>
  `;


  const paymentStatusBtn = document.getElementById('paymentStatusBtn');
  const deliveryNoteModal = document.getElementById('deliveryNoteModal');

  paymentStatusBtn.addEventListener('click', function () {
    if (paymentStatusBtn.textContent === 'Unpaid') {
      paymentStatusBtn.textContent = 'Paid';
      paymentStatusBtn.style.backgroundColor = '#28a745'; // green for paid
      showModal();
    } else {
      paymentStatusBtn.textContent = 'Unpaid';
      paymentStatusBtn.style.backgroundColor = '#991b36'; // back to original
    }
  });

  function showModal() {
    deliveryNoteModal.style.display = 'block';
  }

  function closeModal() {
    deliveryNoteModal.style.display = 'none';
  }

  function downloadDeliveryNote() {
    deliveryNoteModal.style.display = 'none';
    // Replace with actual download logic
    window.open('<?= base_url("invoice/delivery_note/" . $invoice["invoice_id"]) ?>', '_blank');
  }
</script>