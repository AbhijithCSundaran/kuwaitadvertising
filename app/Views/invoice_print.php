<?php include "common/header.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Cash Invoice</title>
  <style>
    .right_container {
      width: 100%;
      margin-left: auto;
      padding: 25px;
      transform: scale(0.5);
    }

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
      background-size: 33%;
      background-position: 52% 64%;
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
      height: 20px;
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
      min-height: 380px;
    }

    table.min_height tbody td {
      vertical-align: top;
      padding: 5px 0;
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
      padding: 4px;
    }

    td {
      text-align: center;
      height: 30px;
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
        🖨️ Print
      </button>
      <button onclick="window.location.href='<?= base_url('invoice/edit/' . $invoice['invoice_id']) ?>'"
        style="background-color: #991b36; color: white; padding: 8px 16px; border: none; border-radius: 5px; margin-left: 10px;">
        Discard
      </button>
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
            <th rowspan="2" style="width: 6%;">No.<br>رقم</th>
            <th rowspan="2" style="width: 38%;">Description<br>التفاصيل</th>
            <th rowspan="2" style="width: 8%;">Qty.<br>الكمية</th>
            <th colspan="2" style="width: 24%;">Unit Price<br>سعر الوحدة</th>
            <th colspan="2" style="width: 24%;">Total Amount<br>المبلغ الإجمالي</th>
          </tr>
          <tr>
            <th style="width: 12%;">K.D<br>دينار</th>
            <th style="width: 12%;">Fils<br>فلس</th>
            <th style="width: 12%;">K.D<br>دينار</th>
            <th style="width: 12%;">Fils<br>فلس</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $totalAmount = 0;
            $discountPercent = isset($invoice['discount']) ? $invoice['discount'] : 0;
            foreach ($items as $index => $item):
            $originalLineTotal = $item['quantity'] * $item['price'];
            $lineTotal = $originalLineTotal - ($originalLineTotal * $discountPercent / 100);
            $kd = floor($item['price']);
            $fils = str_pad(number_format(($item['price'] - $kd) * 100, 0), 3, '0', STR_PAD_LEFT);
            $lineKd = floor($lineTotal);
            $lineFils = str_pad(number_format(($lineTotal - $lineKd) * 100, 0), 3, '0', STR_PAD_LEFT);
             $totalAmount += $lineTotal;
        ?>
          <tr>
            <td><?= $index + 1 ?></td>
            <td><?= esc($item['item_name']) ?></td>
            <td><?= $item['quantity'] ?></td>
            <td><?= $kd ?></td>
            <td><?= $fils ?></td>
            <td><?= $lineKd ?></td> <!-- discounted total -->
            <td><?= $lineFils ?></td>
          </tr>
          <?php endforeach; ?>
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
                <td colspan="2" style="text-align: right;"> <?= number_format($subtotal, 3) ?> KD</td>
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
                <?= number_format($grandTotal, 3) ?> KD 
              </td>
            </tr>
          </tfoot>
      </table>
     
      <div class="amount-words">
        Amount Chargeable (in words): <span id="amount-words"></span>
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
</script>