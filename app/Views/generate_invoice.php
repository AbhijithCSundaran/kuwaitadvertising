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
      .header,
      .footer,
      .navbar,
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
    Edit Invoice
  </button>

 <button onclick="window.location.href='<?= base_url('invoice/delivery_note/' . $invoice['invoice_id']) ?>'"
    style="background-color: #991b36; color: white; padding: 8px 16px; border: none; border-radius: 5px; margin-left: 10px;">
   Delivery Note
</button>



 <?php
  $status = strtolower($invoice['status'] ?? 'unpaid');
  $btnLabel = ucfirst($status);
  $btnColor = $status === 'paid' ? '#28a745' : ($status === 'partial paid' ? '#ffc107' : '#991b36');
?>
<div class="btn-group ml-2 position-relative" style="z-index: 1000; margin-left: 10px;">
  <button
    id="statusBtn"
    type="button"
    class="btn btn-sm"
    style="background-color: <?= $btnColor ?>; color: white; padding: 8px 16px; border-radius: 5px;">
    <?= $btnLabel ?>
  </button>

  <?php if ($status === 'unpaid'): ?>
    <div id="statusOptions" class="dropdown-menu" style="position: absolute; top: 100%; left: 0; min-width: 100%; z-index: 9999;">
      <a href="#" class="dropdown-item" onclick="updateStatus('paid')">Paid</a>
      <a href="#" class="dropdown-item" onclick="openPartialPayment()">Partial Paid</a>
    </div>
  <?php endif; ?>
</div>

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
          <div class="label">
            BILL TO: <strong><?= ucwords(strtolower(esc($customer['name'] ?? '-'))) ?></strong>
          </div>
          <div>
            Person Name: <strong><?= ucwords(strtolower(esc($customer['name'] ?? '-'))) ?></strong>
          </div>
          <div>
            Business Name: <strong><?= ucwords(strtolower(esc($invoice['company_name'] ?? 'Company Name Not Found'))) ?></strong>
          </div>
          <div>
            Address: <strong><?= ucwords(strtolower(esc($invoice['customer_address'] ?? '-'))) ?></strong>
          </div>
          <div>
            Contact Number: <strong><?= esc($invoice['phone_number'] ?? '-') ?></strong>
          </div>
        </div>
        <!-- Ship To -->
        <div class="ship-to col-4 pr-3">
         <div class="label">
          <span>SHIPPING ADDRESS</span><br>
          <span style="color: #000;">
            <?= nl2br(ucwords(strtolower(esc($invoice['shipping_address'] ?? '-')))) ?>
          </span>
        </div>
          <!-- <div>Address:<strong><?= esc($invoice['shipping_address'] ?? '-') ?></strong></div> -->
          <!-- <div>Person Name:<strong><?= esc($customer['name'] ?? '-') ?></strong></div>
          <div>Business Name:<strong><?= esc($invoice['company_name'] ?? 'Company Name Not Found') ?></strong></div>
          <div>Contact Number:<strong><?= esc($invoice['phone_number'] ?? '-') ?></strong></div> -->
        </div>

        <!-- Invoice Info -->
        <div class="col-4 pl-2 ml-auto">
          <table class="info-table table-sm  justify-content-end">
            <tr>
              <td>Invoice Date:</td>
              <td><?= date('d.m.Y', strtotime($invoice['invoice_date'])) ?></td>
            </tr>
            <tr>
              <td>Delivery No:</td>
              <td><?= esc($invoice['invoice_id']) ?></td>
            </tr>
            <tr>
              <td>Delivery Date:</td>
              <td><span id="deliveryDateCell"></span></td>
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
            <th style="width: 518px;"><?= ucwords(strtolower('description')) ?></th>
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
              <td><?= number_format($item['price'], 2) ?></td>
              <td><?= number_format($lineTotal, 2) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
      </table>

      <!-- Totals -->
      <div class="totals">
        <div class="totals-row">
          <div class="tot">SUBTOTAL</div>
          <div class="value"><?= number_format($subtotal, 2) ?></div>
        </div>
        <div class="totals-row">
          <div class="tot">DISCOUNT</div>
          <div class="value"><?= $discountPercent ?>%</div>
        </div>
      </div>
      <div class="totals grand-total">
        <div class="totals-row">
          <strong>Grand Total</strong>
          <span>KD<?= number_format($subtotal - $totalDiscountAmount, 2) ?></span>
        </div>
      </div>
     <?php $grandTotal = $subtotal - $totalDiscountAmount; ?>
      <div class="amount-words">
       In Words:: <span id="amount-words"></span>
      </div>
      <div class="totals-row" id="paidAmountRow" style="display:none;">
  <div class="tot">Paid Amount</div>
  <div class="value" id="paidValue"></div>
</div>
<div class="totals-row" id="balanceAmountRow" style="display:none;">
  <div class="tot">Balance</div>
  <div class="value" id="balanceValue"></div>
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

      <!-- Partial Payment Modal -->
      <div id="partialPaymentModal" style="display: none;">
  <div class="modal-content">
    <h5>Enter Partial Payment Amount</h5>
    <input type="number" id="partialPaidInput" class="form-control" min="1" placeholder="Amount">
    <div class="mt-3">
      <button class="btn btn-success" onclick="submitPartialPayment()">Submit</button>
      <button class="btn btn-secondary" onclick="closePartialModal()">Cancel</button>
    </div>
  </div>
</div>


      <!-- Delivery Note Popup -->
      <!-- <div id="deliveryNoteModal" class="modal" style="display:none; position: fixed; z-index: 9999; left: 0; top: 0;
        width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
      <div
        style="background-color: #fff; margin: 15% auto; padding: 20px; border-radius: 10px; width: 300px; text-align: center;">
          <p>Do you want to download the delivery note?</p>
          <button onclick="downloadDeliveryNote()"
            style="background-color: #28a745; color: white; border: none; padding: 8px 12px; border-radius: 5px; margin: 5px;">
            Yes
          </button>
          <button onclick="closeModal()"
            style="background-color: #dc3545; color: white; border: none; padding: 8px 12px; border-radius: 5px; margin: 5px;">
            Cancel
          </button>
        </div>
      </div> -->
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

  dinars = parseInt(dinars || '0', 10);
  fils = parseInt((fils || '0').padEnd(2, '0').slice(0, 2));

  const convert = (n) => {
    if (n === 0) return 'zero';
    if (n < 20) return a[n];
    if (n < 100) return b[Math.floor(n / 10)] + (n % 10 ? '-' + a[n % 10] : '');
    if (n < 1000) {
      const rem = n % 100;
      return a[Math.floor(n / 100)] + ' hundred' + (rem ? ' and ' + convert(rem) : '');
    }
    if (n < 1000000) {
      const rem = n % 1000;
      return convert(Math.floor(n / 1000)) + ' thousand' + (rem ? ' ' + convert(rem) : '');
    }
    if (n < 1000000000) {
      const rem = n % 1000000;
      return convert(Math.floor(n / 1000000)) + ' million' + (rem ? ' ' + convert(rem) : '');
    }
    return '';
  };

  let words = '';
  if (dinars > 0) {
    const dinarWord = convert(dinars);
    words += dinarWord + ' Kuwaiti ' + (dinars === 1 ? 'Dinar' : 'Dinars');
  }

  if (fils > 0) {
    const filsWord = convert(fils);
    words += (words ? ' and ' : '') + filsWord + ' Fils';
  }

  return words
    ? words.replace(/\b\w/g, c => c.toUpperCase())
    : 'Zero';
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
  fils = parseInt((fils || '0').padEnd(2, '0').slice(0, 2));

  let words = '';
  if (dinars > 0) words += convertNumber(dinars) + ' دينار';
  if (fils > 0) words += (words ? ' و ' : '') + convertNumber(fils) + ' فلس';
  return words || 'صفر';
}

  const grandTotal = <?= json_encode(number_format($grandTotal, 2, '.', '')) ?>;

  const englishWords = numberToWords(grandTotal);
  const arabicWords = numberToArabicWords(grandTotal);

  document.getElementById("amount-words").innerHTML = `
    ${englishWords}<br><span style="font-family: 'Amiri', serif;">${arabicWords}</span>
  `;


  const deliveryNoteModal = document.getElementById('deliveryNoteModal');

  function showModal() {
    deliveryNoteModal.style.display = 'block';
  }

  function closeModal() {
    deliveryNoteModal.style.display = 'none';
  }

  function downloadDeliveryNote() {
    deliveryNoteModal.style.display = 'none';
    window.location.href = '<?= base_url("invoice/delivery_note/" . $invoice["invoice_id"]) ?>';
  }
  function formatDateToDDMMYYYY(date) {
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    return `${day}.${month}.${year}`;
  }

  document.addEventListener('DOMContentLoaded', function () {
    const deliveryCell = document.getElementById('deliveryDateCell');
    if (deliveryCell) {
      deliveryCell.textContent = formatDateToDDMMYYYY(new Date());
    }
  });

  window.onbeforeprint = function () {
    const deliveryCell = document.getElementById('deliveryDateCell');
    if (deliveryCell) {
      deliveryCell.textContent = formatDateToDDMMYYYY(new Date());
    }
  };

  
const statusBtn = document.getElementById('statusBtn');
const statusOptions = document.getElementById('statusOptions');

if (statusBtn && statusOptions) {
  statusBtn.addEventListener('click', function () {
    statusOptions.style.display = (statusOptions.style.display === 'block') ? 'none' : 'block';
  });

  document.addEventListener('click', function (e) {
    if (!statusBtn.contains(e.target) && !statusOptions.contains(e.target)) {
      statusOptions.style.display = 'none';
    }
  });
}

function openPartialPayment() {
  document.getElementById('partialPaymentModal').style.display = 'block';
}

function closePartialModal() {
  document.getElementById('partialPaymentModal').style.display = 'none';
}

function submitPartialPayment() {
  const paid = parseFloat(document.getElementById('partialPaidInput').value);
  if (isNaN(paid) || paid <= 0 || paid > grandTotal) {
    alert("Invalid amount.");
    return;
  }

  fetch("<?= base_url('invoice/update_partial_payment') ?>", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest"
    },
    body: JSON.stringify({
      invoice_id: <?= $invoice['invoice_id'] ?>,
      paid_amount: paid
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      document.getElementById('paidAmountRow').style.display = 'flex';
      document.getElementById('balanceAmountRow').style.display = 'flex';
      document.getElementById('paidValue').innerText = paid.toFixed(2);
      document.getElementById('balanceValue').innerText = (grandTotal - paid).toFixed(2);
      statusBtn.innerText = 'Partial Paid';
      statusBtn.style.backgroundColor = '#ffc107';
      closePartialModal();
    } else {
      alert("Failed to update.");
      console.error("Partial update error:", data);
    }
  })
  .catch(err => {
    alert("Network or server error.");
    console.error("Fetch failed:", err);
  });
}

function updateStatus(newStatus) {
  const invoiceId = <?= $invoice['invoice_id'] ?>;

  console.log("Updating invoice:", invoiceId, "to status:", newStatus);

  fetch("<?= base_url('invoice/update_status') ?>", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest"
    },
    body: JSON.stringify({
      invoice_id: invoiceId,
      status: newStatus
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      statusBtn.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
      statusBtn.style.backgroundColor = newStatus === 'paid' ? '#28a745' : '#991b36';
      statusOptions.style.display = 'none';

      // if (newStatus === 'paid') {
      //   alert("Status updated to Paid. You can now download the Delivery Note.");
      //   // Optionally show delivery note button here
      // }
    } else {
      alert("Status update failed.");
      console.error("Update status failed:", data);
    }
  })
  .catch(err => {
    alert("Network or server error.");
    console.error("Fetch error:", err);
  });
}





// document.getElementById('statusBtn').addEventListener('click', function () {
//   const btn = this;
//   const currentStatus = btn.textContent.trim().toLowerCase();

//   if (currentStatus === 'unpaid') {
//     // First update to 'Paid', then show modal
//     updateStatus('paid', function () {
//       showModal();
//     });
//   } else if (currentStatus === 'paid') {
//     // Already paid, just show modal again
//     showModal();
//   }
// });

// function updateStatus(newStatus, callback = null) {
//   const btn = document.getElementById('statusBtn');

//   fetch("<?= base_url('invoice/update_status') ?>", {
//     method: "POST",
//     headers: {
//       "Content-Type": "application/json",
//       "X-Requested-With": "XMLHttpRequest"
//     },
//     body: JSON.stringify({
//       invoice_id: <?= $invoice['invoice_id'] ?>,
//       status: newStatus
//     })
//   })
//   .then(response => response.json())
//   .then(data => {
//     if (data.success) {
//       // Update UI status
//       btn.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
//       btn.style.backgroundColor = newStatus === 'paid' ? '#28a745' : '#991b36';

//       if (callback) callback(); 
//     } else {
//       alert("Failed to update invoice status.");
//     }
//   })
//   .catch(error => {
//     alert("Error while updating status.");
//     console.error(error);
//   });
// }
function downloadDeliveryNote() {
  deliveryNoteModal.style.display = 'none';
  window.location.href = '<?= base_url("invoice/delivery_note/" . $invoice["invoice_id"]) ?>';
}

</script>