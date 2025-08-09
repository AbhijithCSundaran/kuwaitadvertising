<?php include "common/header.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Cash Invoice</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 14px;
      margin: 0;
      padding: 0;
      background-color: #fff;
    }

    /* New outer brown container */
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
      width: 280px;
    }

    .invoice-type {
      background-color: #991b36;
      color: white;
      font-weight: bold;
      padding: 5px 20px;
      display: inline-block;
      border-radius: 4px;
      margin: 5px auto;
      font-size: 14px;
    }

    .invoice-header {
      display: flex;
      flex-direction: column;
      gap: 5px;
      justify-content: space-between;
      margin-top: 15px;
    }

    .invoice-header>div {
      display: flex;
      font-weight: bold;
      width: 100%;
    }


    .invoice-header .half {
      width: 50%;
    }

    .invoice-header input {
      border: 1px solid #000;
      width: 100px;
      height: 35px;
    }

    .invoice-header span {
      width: 90%;
      /* text-decoration: underline; */
      border-bottom: 1px solid black;
      margin: 0 5px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    table,
    th,
    td {
      border: 1px solid black;
    }

    table.min_height {
      min-height: 350px;
    }

    table.min_height tbody td {
      vertical-align: top;
      padding: 5px 0;
      height: 20px !important;
    }

    tbody td {
      border-top: 1px solid transparent;
      border-bottom: 1px solid transparent;
    }

    tbody tr:last-child td {
      border-bottom: 1px solid black;
    }

    th {
      background-color: #cfc7c7ff;
      text-align: center;
      font-weight: bold;
      padding: 2px;
    }

    td {
      text-align: center;
      height: 25px;
      padding: 4px;
      word-wrap: break-word;
      word-break: break-word;
      white-space: normal;
    }

    .table-footer {
      display: flex;
      justify-content: space-between;
      margin-top: 30px;
      font-weight: bold;
    }

    .amount-words {
      margin-top: 20px;
      margin-bottom: 20px;
      font-weight: bold;
    }

    .table-footer div {
      width: 48%;
    }

    .bottom-bar {
      text-align: center;
      font-size: 12px;
      color: white;
      background-color: #991b36;
      padding: 10px;
      margin-top: 20px;

    }

    .tfoot {
      background-color: #cfc7c7ff;
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
        /* Safari/Chrome */
        print-color-adjust: exact !important;
        /* Firefox */
      }

      .no-print,
      .header,
      .footer,
      .sidebar,
      .navbar {
        display: none !important;
      }

      table td {
        font-size: 10px;
      }

      td:nth-child(2) {
        max-width: 250px;
        word-wrap: break-word;
        word-break: break-word;
        white-space: normal;
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
      <?php if (strtolower($invoice['status']) !== 'paid'): ?>
        <button id="editinvoicebtn"
          onclick="window.location.href='<?= base_url('invoice/edit/' . $invoice['invoice_id']) ?>'"
          style="background-color: #991b36; color: white; padding: 8px 16px; border: none; border-radius: 5px; margin-left: 10px; cursor: pointer;">
          Edit Invoice
        </button>
      <?php endif; ?>
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
        <button id="statusBtn" type="button" class="btn btn-sm"
          style="background-color: <?= $btnColor ?>; color: white; padding: 8px 16px; border-radius: 5px;"
          <?= $status === 'paid' ? 'disabled title="Fully paid invoice cannot be changed"' : 'onclick="toggleStatusOptions()"' ?>>
          <?= $btnLabel ?>
        </button>

        <?php if ($status === 'unpaid' || $status === 'partial paid'): ?>
          <div class="dropdown" style="position: relative;">
            <div id="statusOptions" class="dropdown-menu p-2"
              style="position: absolute; top: 100%; right: 0px; z-index: 1050; box-shadow: 0 4px 8px rgba(0,0,0,0.1); display: none;">
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
    <div class="container">
      <div class="top-heading">
        <img src="<?php echo ASSET_PATH; ?>assets/images/invoice-heading.png" alt="Invoice Heading">
        <hr>
        <div
          style="background-color: #991b36;color: white;font-weight: bold;padding: 5px 20px;display: inline-block;border-radius: 4px;margin: 5px auto;font-size: 14px;">
          فاتورة / نقداً / بالحساب<br>CASH / CREDIT INVOICE
        </div>
      </div>
      <div class="invoice-header">
        <div class="row">
          <div class="half">
            No.: <input type="text" readonly value="<?= esc($invoice['invoice_id']) ?>">
          </div>
          <div class="half" style="text-align: right;">
            Date: <input type="text" value="<?= date('d-m-Y', strtotime($invoice['invoice_date'])) ?>" readonly><br><br>
          </div>
        </div>
        <div class="col-12">
          Mr./Mrs: <span><?= esc($invoice['customer_name'] ?? '') ?></span>:السيد
        </div>
        <div class="col-12">
          Address: <span> <?= esc($invoice['customer_address'] ?? '') ?></span>:عنوان
        </div>
      </div>

      <!-- Invoice Table -->
      <table class="min_height">
        <thead>
          <tr>
            <th rowspan="2" style="width: 6%;">رقم<br>No.</th>
            <th rowspan="2" style="width: 38%;">Description التفاصيل</th>
            <th rowspan="2" style="width: 8%;">الكمية<br>Qty.</th>
            <th colspan="2" style="width: 24%;">سعر الوحدة<br>Unit Price</th>
            <th colspan="2" style="width: 24%;">المبلغ الإجمالي<br>Total Amount</th>
          </tr>
          <tr>
            <th style="width: 12%;">دينار<br>K.D</th>
            <th style="width: 12%;">فلس<br>Fils</th>
            <th style="width: 12%;">دينار<br>K.D</th>
            <th style="width: 12%;">فلس<br>Fils</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $totalAmount = 0;
          foreach ($items as $index => $item):
            $lineTotal = $item['quantity'] * $item['price']; // NO DISCOUNT
            $kd = floor($item['price']);
            $fils = str_pad(number_format(($item['price'] - $kd) * 100, 0), 2, '0', STR_PAD_LEFT);

            $lineKd = floor($lineTotal);
            $lineFils = str_pad(number_format(($lineTotal - $lineKd) * 100, 0), 2, '0', STR_PAD_LEFT);

            $totalAmount += $lineTotal; // this can remain
            ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td style="text-align: left;"><?= esc($item['item_name'] ?? '-') ?></td>
              <td><?= $item['quantity'] ?></td>
              <td><?= $kd ?></td>
              <td><?= $fils ?></td>
              <td><?= $lineKd ?></td> <!-- shows quantity * price -->
              <td><?= $lineFils ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <?php $grandTotal = $totalAmount; ?>
        <?php
        $subtotal = 0;
        foreach ($items as $item) {
          $lineTotal = $item['quantity'] * $item['price'];
          $subtotal += $lineTotal;
        }
        $discountPercent = isset($invoice['discount']) ? floatval($invoice['discount']) : 0;
        $totalDiscount = ($subtotal * $discountPercent) / 100;
        $grandTotal = $subtotal - $totalDiscount;
        ?>
        <tfoot class="tfoot">
          <?php if ($discountPercent > 0): ?>
            <tr>
              <td colspan="5" style="text-align: right; font-weight: bold;">Subtotal</td>
              <td colspan="2" style="text-align: right;"> <?= number_format($subtotal, 2) ?> KD</td>
            </tr>
            <tr>
              <td colspan="5" style="text-align: right; font-weight: bold;">
                Discount
              </td>
              <td colspan="2" style="text-align: right;">
                <?= $discountPercent ?>%
              </td>

            </tr>
          <?php endif; ?>
          <tr>
            <td colspan="5" style="text-align: right; font-weight: bold;">Total Amount</td>
            <td colspan="2" style="text-align: right;" id="total-amount">
              <?= number_format($grandTotal, 2) ?> KD
            </td>
          </tr>
        </tfoot>
      </table>

      <div class="amount-words">
        Amount Chargeable (in words): <span id="amount-words"></span>
      </div>

      <?php
      $paidAmount = floatval($invoice['paid_amount'] ?? 0);
      // $grandTotal = $subtotal - $totalDiscountAmount;
      $balanceAmount = $grandTotal - $paidAmount;
      $status = strtolower($invoice['status'] ?? 'unpaid');
      ?>

      <div style="display: flex; flex-direction: column; align-items: flex-end; margin-top: 10px;">
        <div class="partial-row" id="paidAmountRow"
          style="display: <?= ($status === 'partial paid' && $paidAmount > 0) ? 'flex' : 'none' ?>;">
          <div class="partial">Paid Amount</div>
          <div class="value" id="paidAmountValue"><?= number_format($paidAmount, 2) ?></div>
        </div>
        <div class="partial-row" id="balanceAmountRow"
          style="display: <?= ($status === 'partial paid' && $paidAmount > 0) ? 'flex' : 'none' ?>;">
          <div class="partial">Balance</div>
          <div class="value" id="balanceAmountValue"><?= number_format($balanceAmount, 2) ?></div>
        </div>
      </div>

      <div class="table-footer">
        <div>Received by. المستلم</div>
        <div style="text-align: right;">Salesman Signature. توقيع البائع</div>
      </div>


    </div> <!-- /.container -->
    <!-- Bottom Bar -->
    <div class="bottom-bar">
      الراي ، قطعة ٣ ، شارع ٣٢ ، مبنى رقم ٤٣٧ ، محل رقم ٤ ، بالقرب من زجاج الروان ، الشويخ - الكويت<br>
      Al-Rai, Block 3, Street 32, Build No. 437, Shop No. 4, Near Al Rawan Glass, Shuwaik - Kuwait<br>
      📞 +965 6006 0102 &nbsp;&nbsp; | &nbsp;&nbsp;
      📧 <a href="mailto:alraiprintpress@gmail.com" style="color: white; text-decoration: none;">
        alraiprintpress@gmail.com
      </a>
    </div>
  </div>

  <!-- Partial Payment Modal -->
  <div id="partialPaymentModal" class="modal fade show style=" display: none; background-color: rgba(235, 170, 71, 0.7);
    position: fixed; inset: 0; z-index: 1055; align-items: center; justify-content: center;">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow-lg border-0 rounded-4 p-4">
        <div class="modal-header border-0">
          <h5 class="modal-title text-primary fw-bold">Partial Payment</h5>
          <button type="button" class="btn-close" onclick="closePartialModal()"></button>
        </div>
        <div class="modal-body">
          <label for="partialPaidInput" class="form-label">Enter Amount</label>
          <input type="number" id="partialPaidInput" class="form-control form-control-lg border-primary" min="1"
            placeholder="Enter partial amount">
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