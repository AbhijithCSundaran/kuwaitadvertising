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
      background-position: 52% 50%;
      background-color: white;
    }

    .top-heading {
      text-align: center;
      margin-bottom: 5px;
    }

    .top-heading img {
      width: 138px;
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
      padding: 5px 6px;
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
      padding: 3px;
      margin-top: 0px;

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
        line-height: 1.4;
      }

      table {
        border-collapse: collapse;
        width: 100%;
        table-layout: fixed;
      }

      table th,
      table td {
        border: 1px solid #000;
        padding: 4px;
        font-size: 10px;
        word-break: break-word;
      }

      td:nth-child(2) {
        max-width: 250px;
        white-space: normal;
      }


      tr {
        page-break-inside: avoid;
      }


      body,
      table {
        background: none !important;
      }

      .col-4,
      {
      flex: 0 0 auto !important;
      width: auto !important;
      max-width: none !important;
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
      <!-- Edit Invoice Button -->
      <button id="editInvoiceBtn"
        onclick="window.location.href='<?= base_url('invoice/edit/' . $invoice['invoice_id']) ?>'"
        style="background-color: #991b36; color: white; padding: 8px 16px; border: none; border-radius: 5px; margin-left: 10px; cursor: pointer;
              display: <?= in_array(strtolower($invoice['status']), ['paid', 'partial paid']) ? 'none' : 'inline-block' ?>;">
        Edit Invoice
      </button>

      <!-- Delivery Note Button -->
      <button id="deliveryNoteBtn"
        onclick="window.location.href='<?= base_url('invoice/delivery_note/' . $invoice['invoice_id']) ?>'"
        style="background-color: #991b36; color: white; padding: 8px 16px; border: none; border-radius: 5px; margin-left: 10px;
              display: <?= in_array(strtolower($invoice['status']), ['paid', 'partial paid']) ? 'inline-block' : 'none' ?>;">
        Delivery Note
      </button>

      <!-- Payment Button -->
        <?php
        $paymentMode = isset($invoice['payment_mode']) && $invoice['payment_mode'] !== '' ? strtolower($invoice['payment_mode']) : 'cash';
        $btnLabel = ($paymentMode === 'cash') ? 'Receipt' : 'Voucher';
        $btnUrl   = ($paymentMode === 'cash')
            ? base_url('receiptvoucher/' . $invoice['invoice_id'])
            : base_url('paymentvoucher/' . $invoice['invoice_id']);
        $invoiceStatus = strtolower($invoice['status']);
        ?>
        <button id="paymentBtn"
          style="background-color: #991b36; color: white; padding: 8px 16px; border: none; border-radius: 5px; margin-left: 10px;
                display: <?= $invoiceStatus === 'unpaid' ? 'none' : 'inline-block' ?>;"
          onclick="window.location.href='<?= $btnUrl ?>'">
          <?= $btnLabel ?>
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
              <a href="javascript:void(0);" class="dropdown-item text-success fw-semibold"
                onclick="openMarkPaidModal(<?= $invoice['invoice_id']; ?>)">
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
      <div class="d-flex align-items-center text-center" style="margin-bottom: 5px;">
        <div class="col-4 text-start">
          <span style="font-size: 12px; font-weight: bold;">
            <?= esc(ucwords(strtolower($company['company_name']))) ?>
          </span>
        </div>
        <div class="col-4">
          <?php if (!empty($company['company_logo'])): ?>
            <img src="<?= base_url('public/uploads/' . $company['company_logo']) ?>" alt="Company Logo"
              style="max-height: 30px;">
          <?php endif; ?>
        </div>
        <div class="col-4 text-end">
          <span style="font-size: 14px; font-weight: bold; direction: rtl;">
            <?= esc($company['company_name_ar'] ?? '') ?>
          </span>
        </div>
      </div>
      <div style="height: 3px; background-color:#a1263a"></div>
      <div class="row align-items-center" style="margin-bottom: 10px;">
        <div class="col-4 text-start">
          <div>
            <label style="font-weight: bold; margin-right: 4px; margin-top: 17px;">No / رقم :</label>
            <input type="text" readonly value="<?= esc($invoice['invoice_id']) ?>"
              style="display: inline-block; width: 87px; height: 23px; text-align:left;">
          </div>
          <div style="margin-top: 4px;">
            <label style="font-weight: bold; margin-right: 4px;">LPO No :</label>
            <span><?= esc($invoice['lpo_no']) ?></span>
          </div>
        </div>
        <div class="col-4 text-center">
          <div
            style="background-color: #991b36; color: white; font-weight: bold; padding: 3px 15px; display: inline-block; border-radius: 4px; font-size: 13px; margin-top: 11px;">
            فاتورة / نقداً / بالحساب<br>CASH / CREDIT INVOICE
          </div>
        </div>
        <div class="col-4 text-end">
          <div style="white-space: nowrap;">
            <label style="font-weight: bold; margin-right: 6px; margin-top: 17px;">Date / التاريخ:</label>
            <input type="text" readonly value="<?= date('d-m-Y', strtotime($invoice['invoice_date'])) ?>"
              style="width: 87px; height: 23px; text-align: center;">
          </div>
          <div style="margin-top: 4px; white-space: nowrap;">
            <label style="font-weight: bold; margin-right: 6px;">Delivery Date :</label>
            <span id="deliveryDateCell">
              <?= !empty($invoice['delivery_date']) ? date('d-m-Y', strtotime($invoice['delivery_date'])) : '' ?>
            </span>
          </div>
        </div>
      </div>

      <div class="invoice-header">
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
            <th rowspan="2" style="width: 38%;"> التفاصيل<br>Description</th>
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
        المبلغ (بالكلمات): <span id="amount-words"></span>
      </div>

      <?php
      $paidAmount = floatval($invoice['paid_amount'] ?? 0);
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
        <div>Receivers Name & Signature / اسم المستلمين والتوقيع</div>
        <div style="text-align: right;">Accountant Name & Signature / اسم المحاسب والتوقيع</div>
      </div>


    </div> <!-- /.container -->
    <!-- Bottom Bar -->
    <div class="bottom-bar">
      <div style="direction: rtl; text-align: center;">
        <?= esc($company['address_ar'] ?? '') ?>
      </div>
      <div style="direction: ltr; text-align: center;">
        <?= esc($company['address'] ?? '') ?>
      </div>
      <div style="margin-top: 5px;">
        📞 <?= esc($company['phone'] ?? '') ?> &nbsp;&nbsp; | &nbsp;&nbsp;
        📧 <a href="mailto:<?= esc($company['email'] ?? '') ?>" style="color: white; text-decoration: none;">
          <?= esc($company['email'] ?? '') ?>
        </a>
      </div>
    </div>
  </div>

  <!-- Partial Payment Modal -->
  <div id="partialPaymentModal" class="modal fade show" style="display: none; background-color: rgba(235, 170, 71, 0.7);
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
          <div class="mt-3">
            <label for="paymentMode" class="form-label">Payment Mode <span style="color:red;">*</span></label>
            <select id="paymentMode" class="form-control form-control-lg border-primary">
              <option value="" selected disabled>Select payment mode</option>
              <option value="cash">Cash</option>
              <option value="bank_transfer">Bank Transfer</option>
              <option value="bank_link">Bank Link</option>
              <option value="wamd">WAMD</option>
            </select>
            <small id="paymentModeError" style="color:red; display:none;">Please select a payment mode.</small>
          </div>
        </div>

        <div class="modal-footer border-0">
          <button class="btn btn-danger px-4" onclick="submitPartialPayment()">Submit</button>
          <button class="btn btn-secondary px-4" onclick="closePartialModal()">Cancel</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Mark as Paid Modal -->
  <div id="markPaidModal" class="modal fade" tabindex="-1" aria-labelledby="markPaidModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow-lg border-0 rounded-4 p-4">
        <div class="modal-header border-0">
          <h5 class="modal-title text-success fw-bold" id="markPaidModalLabel">Mark Invoice as Paid</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label for="markPaidPaymentMode" class="form-label">
              Payment Mode <span style="color:red;">*</span>
            </label>
            <select id="markPaidPaymentMode" class="form-control form-control-lg border-success">
              <option value="" selected disabled>Select payment mode</option>
              <option value="cash">Cash</option>
              <option value="bank_transfer">Bank Transfer</option>
              <option value="bank_link">Bank Link</option>
              <option value="wamd">WAMD</option>
            </select>
            <small id="markPaidError" style="color:red; display:none;">Please select a payment mode.</small>
          </div>
        </div>

        <div class="modal-footer border-0">
          <button type="button" class="btn btn-success" id="confirmMarkPaid">Submit</button>
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
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
    const a = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven',
      'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    const b = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
    num = num.toString().replace(/,/g, '');

    let [dinars, fils] = num.split('.');

    if (dinars.length > 9) return 'overflow';
    dinars = parseInt(dinars, 10);
    fils = parseInt((fils || '0').padEnd(3, '0').slice(0, 2)); // Handle fils up to 2 decimal places

    const convert = (n) => {
      if (n < 20) return a[n];
      if (n < 100) return b[Math.floor(n / 10)] + (n % 10 ? '-' + a[n % 10] : '');
      if (n < 1000) return a[Math.floor(n / 100)] + ' Hundred' + (n % 100 ? ' ' + convert(n % 100) : '');
      if (n < 1000000) return convert(Math.floor(n / 1000)) + ' Thousand' + (n % 1000 ? ' ' + convert(n % 1000) : '');
      if (n < 1000000000) return convert(Math.floor(n / 1000000)) + ' Million' + (n % 1000000 ? ' ' + convert(n % 1000000) : '');
      return '';
    };

    let words = '';
    if (dinars > 0) words += convert(dinars) + ' Kuwaiti Dinar';
    if (fils > 0) words += (words ? ' And ' : '') + convert(fils) + ' Fils';
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

  let isFirstPartialPayment = localStorage.getItem('firstPartialDone_<?= $invoice['invoice_id'] ?>') !== 'true';

  function openPartialPayment() {
    const modalTitle = document.querySelector('#partialPaymentModal .modal-title');
    const inputLabel = document.querySelector('label[for="partialPaidInput"]');
    const partialPaidInput = document.getElementById('partialPaidInput');
    const paymentModeSelect = document.getElementById('paymentMode');

    if (partialPaidInput) partialPaidInput.value = '';
    if (paymentModeSelect) paymentModeSelect.value = '';

    if (isFirstPartialPayment) {
      modalTitle.innerText = "Advance Payment";
      inputLabel.innerText = "Enter Amount";
    } else {
      modalTitle.innerText = "Partial Payment";
      inputLabel.innerText = "Enter Amount";
    }

    // Finally, show modal
    document.getElementById('partialPaymentModal').style.display = 'block';
  }

  function closePartialModal() {
    document.getElementById('partialPaymentModal').style.display = 'none';
  }

  function submitPartialPayment() {
    const paid = parseFloat(document.getElementById('partialPaidInput').value);
    const paymentMode = document.getElementById('paymentMode').value;
    const errorMsg = document.getElementById('partialErrorMsg');
    const paymentModeError = document.getElementById('paymentModeError');
    errorMsg.style.display = 'none';
    if (paymentModeError) paymentModeError.style.display = 'none';

    //  Validate amount
    if (isNaN(paid) || paid <= 0 || paid > grandTotal) {
      errorMsg.innerText = 'Entered Amount Exceeds Balance.';
      errorMsg.style.display = 'block';
      return;
    }

    //Validate payment mode
    if (!paymentMode) {
      if (!paymentModeError) {
        const error = document.createElement("small");
        error.id = "paymentModeError";
        error.style.color = "red";
        error.innerText = "Please select a payment mode.";
        document.getElementById("paymentMode").insertAdjacentElement("afterend", error);
      } else {
        paymentModeError.style.display = "block";
      }
      document.getElementById("paymentMode").focus();
      return;
    }

    const alreadyPaid = parseFloat(document.getElementById('paidAmountValue')?.innerText || 0);
    const balanceRemaining = grandTotal - alreadyPaid;

    if (paid > balanceRemaining) {
      errorMsg.innerText = 'Entered Amount Exceeds Balance.';
      errorMsg.style.display = 'block';
      return;
    } else {
      errorMsg.style.display = 'none';
    }


    fetch("<?= base_url('invoice/update_partial_payment') ?>", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest"
      },
      body: JSON.stringify({
        invoice_id: <?= $invoice['invoice_id'] ?>,
        paid_amount: paid,
        payment_mode: paymentMode
      })
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
       setTimeout(function () {
          window.location.reload();
           }, 200);
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


        if (isFirstPartialPayment) {
          document.querySelector('#paidAmountRow .partial').innerText = "Advance Amount";
          localStorage.setItem('firstPartialDone_<?= $invoice['invoice_id'] ?>', 'true');
          isFirstPartialPayment = false;
        } else {
          document.querySelector('#paidAmountRow .partial').innerText = "Paid Amount";
        }
      })
      .catch(err => {
        alert("Network or server error.");
        console.error("Fetch failed:", err);
      });
  }


function updateStatus(newStatus) {
    const invoiceId = <?= $invoice['invoice_id'] ?>;
    const paymentBtn = document.getElementById("paymentBtn");
    const statusBtn = document.getElementById("statusBtn");
    const editBtn = document.getElementById("editInvoiceBtn");
    const deliveryBtn = document.getElementById("deliveryNoteBtn");

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
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // ✅ Update the status button instantly
            statusBtn.innerText = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);

            // ✅ If Paid or Partial Paid → Show Delivery + Payment, Hide Edit
            if (newStatus === 'paid' || newStatus === 'partial paid') {
                if (editBtn) editBtn.classList.add("d-none");
                if (deliveryBtn) deliveryBtn.classList.remove("d-none");
                if (paymentBtn) paymentBtn.classList.remove("d-none");
            } 
            // ✅ If Unpaid → Show Edit, Hide Delivery + Payment
            else if (newStatus === 'unpaid') {
                if (editBtn) editBtn.classList.remove("d-none");
                if (deliveryBtn) deliveryBtn.classList.add("d-none");
                if (paymentBtn) paymentBtn.classList.add("d-none");
            }
        } else {
            alert("Status update failed!");
            console.error("Update status failed:", data);
        }
        if (data.success) {
    statusBtn.innerText = 'Partial Paid';
    statusBtn.style.backgroundColor = '#ffc107';

    // ✅ Instantly show Delivery + Payment buttons, hide Edit
    document.getElementById("editInvoiceBtn")?.classList.add("d-none");
    document.getElementById("deliveryNoteBtn")?.classList.remove("d-none");
    document.getElementById("paymentBtn")?.classList.remove("d-none");
}
    })
    .catch(error => {
        console.error("Error updating status:", error);
        alert("An error occurred while updating status.");
    });
}



  let selectedInvoiceId = null;

  function openMarkPaidModal(invoiceId) {
    selectedInvoiceId = invoiceId;
    $('#invoice_id').val(invoiceId);
    $('#markPaidModal').modal('show');
  }

  $('#confirmMarkPaid').on('click', function () {
    const paymentMode = $('#markPaidPaymentMode').val();
    const errorMsg = $('#markPaidError');

    if (!paymentMode || paymentMode.trim() === '') {
      errorMsg.show();
      $('#markPaidPaymentMode').focus();
      return;
    } else {
      errorMsg.hide();
    }

    $('#confirmMarkPaid').prop('disabled', true).text('Processing...');

    $.ajax({
      url: "<?= base_url('invoice/update_status') ?>",
      type: "POST",
      contentType: "application/json",
      data: JSON.stringify({
        invoice_id: selectedInvoiceId,
        status: "paid",
        payment_mode: paymentMode
      }),
      success: function (response) {
        const modalElement = document.getElementById('markPaidModal');
        const modal = bootstrap.Modal.getInstance(modalElement);
        modal.hide();

        const statusBtn = $('#statusBtn');
        if (statusBtn.length) {
          statusBtn.text('Paid');
          statusBtn.css('background-color', '#28a745');
          statusBtn.prop('disabled', true);
          statusBtn.attr('title', 'Fully paid invoice cannot be changed');
        }


        const paymentBtn = $('#paymentBtn');
        if (paymentBtn.length) {
          if (paymentMode === 'cash') {
            paymentBtn.text('Receipt');
            paymentBtn.off('click').on('click', function () {
              window.location.href = "<?= base_url('receiptvoucher/' . $invoice['invoice_id']) ?>";
            });
          } else {
            paymentBtn.text('Voucher');
            paymentBtn.off('click').on('click', function () {
              window.location.href = "<?= base_url('paymentvoucher/' . $invoice['invoice_id']) ?>";
            });
          }
        }
        $('#editinvoicebtn').hide();
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", status, error, xhr.responseText);
        alert("Something went wrong. Please try again.");
      },
      complete: function () {
        $('#confirmMarkPaid').prop('disabled', false).text('Submit');
      }
    });
  });

  document.getElementById('paymentMode').addEventListener('change', function () {
    const mode = this.value;
    const paymentBtn = document.getElementById('paymentBtn');
    if (mode === 'cash') {
      paymentBtn.innerText = 'Receipt';
      paymentBtn.onclick = () => window.location.href = '<?= base_url('receiptvoucher/' . $invoice['invoice_id']) ?>';
    } else {
      paymentBtn.innerText = 'Voucher';
      paymentBtn.onclick = () => window.location.href = '<?= base_url('paymentvoucher/' . $invoice['invoice_id']) ?>';
    }
  });

  function downloadDeliveryNote() {
    deliveryNoteModal.style.display = 'none';
    window.location.href = '<?= base_url("invoice/delivery_note/" . $invoice["invoice_id"]) ?>';
  }

</script>