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
      color: #0a0a0a8d;    }

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


    .bill-to td,
    .ship-to td {
      padding: 2px 0;
    }

    .label {
      font-weight: bold;
      color: #a1263a;
    }

    .items-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    .items-table th {
      background-color: #a1263a;
      color: #fff;
      padding: 8px;
      font-size: 13px;
      border: 1px solid #ccc;
      text-align: center;
    }

    .items-table td {
      border: 1px solid #ccc;
      padding: 6px;
      height: 30px;
      text-align: center;
    }

    .ship-bold {
      text-decoration: underline solid #000;
      font-weight: bold;
      color: #a1263a;
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
      color: #a1263a;
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
      color: #a1263a;
      font-weight: bold;
      margin-top: 5px;
    }
    .recipient-box {
      width: 300px;
      border: 2px solid #000;
      margin-top: 20px;
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
     .tot{
    font-weight: bold;
    }

    .bill-to strong,
    .ship-to strong {
      color: black;
    }
   .partial-row {
    display: flex;
    justify-content: end;
    width: 300px;
    margin-bottom: 5px;
    gap: 53px;
  }

  .partial {
    font-weight: bold;
  }

.value {
  text-align: right;
  min-width: 100px;
}


    @media print {
      * {
        -webkit-print-color-adjust: exact !important;
        Safari/Chrome 
        print-color-adjust: exact !important;
        Firefox 
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

<?php if (strtolower($invoice['status']) !== 'paid'): ?>
  <button id="editinvoicebtn"
    onclick="window.location.href='<?= base_url('invoice/edit/' . $invoice['invoice_id']) ?>'"
    style="background-color: #991b36; color: white; padding: 8px 16px; border: none; border-radius: 5px; margin-left: 10px; cursor: pointer;">
    Edit Invoice
  </button>
<?php endif; ?>


 <!-- Always render the button, but control visibility using JS -->
<button id="deliveryNoteBtn"
  onclick="window.location.href='<?= base_url('invoice/delivery_note/' . $invoice['invoice_id']) ?>'"
  style="display: <?= (strtolower($invoice['status']) === 'paid') ? 'inline-block' : 'none' ?>; background-color: #991b36; color: white; padding: 8px 16px; border: none; border-radius: 5px; margin-left: 10px;">
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
    style="background-color: <?= $btnColor ?>; color: white; padding: 8px 16px; border-radius: 5px;"
    <?= $status === 'paid' ? 'disabled title="Fully paid invoice cannot be changed"' : 'onclick="toggleStatusOptions()"' ?>>
    <?= $btnLabel ?>
  </button>

  <?php if ($status === 'unpaid' || $status === 'partial paid'): ?>
    <div class="dropdown" style="position: relative;">
      <div id="statusOptions" class="dropdown-menu p-2" style="position: absolute; top: 100%; right: 0px; z-index: 1050; box-shadow: 0 4px 8px rgba(0,0,0,0.1); display: none;">
        <a href="#" class="dropdown-item text-success fw-semibold" onclick="updateStatus('paid')">
          <i class="fas fa-check-circle me-2"></i> Mark as Paid
        </a>
        <a href="#" class="dropdown-item text-warning fw-semibold" onclick="openPartialPayment()">
          <i class="fas fa-hourglass-half me-2"></i> Partial Payment
        </a>
      </div>
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
          <span>SHIP TO</span><br>
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
            <!-- <th style="width: 518px;"><?= ucwords(strtolower('description')) ?></th> -->
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
             <td style="text-align: left;"><?= esc($item['item_name'] ?? '-') ?></td>
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
      <?php
        $paidAmount = floatval($invoice['paid_amount'] ?? 0);
        $grandTotal = $subtotal - $totalDiscountAmount;
        $balanceAmount = $grandTotal - $paidAmount;
        $status = strtolower($invoice['status'] ?? 'unpaid');
      ?>

        <div style="display: flex; flex-direction: column; align-items: flex-end; margin-top: 10px;">
        <div class="partial-row" id="paidAmountRow" style="display: <?= ($status === 'partial paid' && $paidAmount > 0) ? 'flex' : 'none' ?>;">
          <div class="partial">Paid Amount</div>
          <div class="value" id="paidAmountValue"><?= number_format($paidAmount, 2) ?></div>
        </div>
        <div class="partial-row" id="balanceAmountRow" style="display: <?= ($status === 'partial paid' && $paidAmount > 0) ? 'flex' : 'none' ?>;">
          <div class="partial">Balance</div>
          <div class="value" id="balanceAmountValue"><?= number_format($balanceAmount, 2) ?></div>
        </div>
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
      <div id="partialPaymentModal" class="modal fade show style="display: none; background-color: rgba(235, 170, 71, 0.7); position: fixed; inset: 0; z-index: 1055; align-items: center; justify-content: center;">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content shadow-lg border-0 rounded-4 p-4">
            <div class="modal-header border-0">
              <h5 class="modal-title text-primary fw-bold">Partial Payment</h5>
              <button type="button" class="btn-close" onclick="closePartialModal()"></button>
            </div>
            <div class="modal-body">
              <label for="partialPaidInput" class="form-label">Enter Amount</label>
              <input type="number" id="partialPaidInput" class="form-control form-control-lg border-primary" min="1" placeholder="Enter partial amount">
              <small id="partialErrorMsg" style="color:red; display:none;">Entered amount exceeds balance.</small>
            </div>
            <div class="modal-footer border-0">
              <button class="btn btn-danger px-4" onclick="submitPartialPayment()">
               Submit
              </button>
              <button class="btn btn-secondary px-4" onclick="closePartialModal()">
                Cancel
              </button>
            </div>
          </div>
        </div>
    </div>
    </div>
  </div>
 </body>
</html>
</div>
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

 function toggleStatusOptions() {
    const dropdown = document.getElementById('statusOptions');
    dropdown.classList.toggle('show');
  }

  document.addEventListener('click', function (e) {
    const statusBtn = document.getElementById('statusBtn');
    const statusOptions = document.getElementById('statusOptions');

    if (!statusBtn.contains(e.target) && !statusOptions.contains(e.target)) {
      statusOptions.classList.remove('show');
    }
  });
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

// let grandTotal = <?= json_encode($invoice['total'] ?? 0) ?>;
function submitPartialPayment() {
 const paid = parseFloat(document.getElementById('partialPaidInput').value);
  const errorMsg = document.getElementById('partialErrorMsg');
  errorMsg.style.display = 'none'; // Hide any previous error

  // Assuming grandTotal is defined somewhere globally or above
  if (isNaN(paid) || paid <= 0 || paid > grandTotal) {
    errorMsg.innerText = 'Entered amount exceeds balance.';
    errorMsg.style.display = 'block';
    return;
  }


  const alreadyPaid = parseFloat(document.getElementById('paidAmountValue')?.innerText || 0);
  const balanceRemaining = grandTotal - alreadyPaid;

  if (paid > balanceRemaining) {
  document.getElementById('partialErrorMsg').style.display = 'block';
  return;
} else {
  document.getElementById('partialErrorMsg').style.display = 'none';
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
      const paidRow = document.getElementById('paidAmountRow');
      const balanceRow = document.getElementById('balanceAmountRow');
      const paidVal = document.getElementById('paidAmountValue');
      const balanceVal = document.getElementById('balanceAmountValue');

      if (paidRow && paidVal) {
        paidRow.style.display = 'flex';
        paidVal.innerText = parseFloat(data.paid_amount).toFixed(2);
      }

      if (balanceRow && balanceVal) {
        if (parseFloat(data.balance_amount) > 0) {
          balanceRow.style.display = 'flex';
          balanceVal.innerText = parseFloat(data.balance_amount).toFixed(2);
        } else {
          balanceRow.style.display = 'none';
        }
      }

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

       if (newStatus === 'paid') {
         statusBtn.disabled = true;
        statusBtn.setAttribute('title', 'Fully paid invoice cannot be changed');
        statusBtn.removeAttribute('onclick');

        document.getElementById('paidAmountRow')?.style.setProperty('display', 'none', 'important');
        document.getElementById('balanceAmountRow')?.style.setProperty('display', 'none', 'important');
        document.getElementById('deliveryNoteBtn')?.style.setProperty('display', 'inline-block');
       const editBtn = document.getElementById('editinvoicebtn');
  if (editBtn) editBtn.style.display = 'none';
      }


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


function downloadDeliveryNote() {
  deliveryNoteModal.style.display = 'none';
  window.location.href = '<?= base_url("invoice/delivery_note/" . $invoice["invoice_id"]) ?>';
}

</script>