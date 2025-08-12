<?php include "common/header.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Cash Estimate</title>
  <style>
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
      margin-top: 10px;
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
      /* font-weight: bold; */
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
    .table-footer div {
      width: 48%;
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
        <span style="font-size: 14px; font-weight: bold;">
            <?= esc($company['company_name']) ?>
        </span>

        <?php if (!empty($company['company_logo'])): ?>
            <img src="<?= base_url('public/uploads/' . $company['company_logo']) ?>" 
                alt="Company Logo" style="max-height: 50px; width: 25%;">
        <?php endif; ?>

        <span style="font-size: 14px; font-weight: bold; direction: rtl;">
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
              style="width: 87px; height: 23px; text-align: center;">
          </div>
        </div>
      </div>
       <div class="col-md-6">
             <strong>TO: M/S / إلى: السيدة/السيدة: </strong><?= esc($estimate['customer_name'] ?? 'Customer Name') ?><br>
    </div>
    <div style="height: 2px; background-color: #ddd;"></div>

      <div class="row mt-2">
                <div class="col-8" style=" font-size: 13px;">
            Person Name:
            <?= esc($estimate['customer_name'] ?? '') ?><br>
            Business Name:
            <?= esc($company_name) ?><br>
            Address:
            <?= esc($estimate['customer_address'] ?? '') ?><br>
            Contact Number:
             <?= esc($estimate['phone_number']) ?>
        </div>
            </div>

      <!-- Invoice Table -->
     <table class="generate-table ">
                    <thead class="thead-dark">
                        <tr>
                            <th rowspan="2" style="width: 10%;">رقم<br>SR. No</th>
                            <th rowspan="2" style="width: 38%;"> التفاصيل<br>Description</th>
                            <th rowspan="2" style="width: 10%;">الكمية<br>QTY</th>
                            <th rowspan="2" style="width: 18%;">سعر الوحدة<br>Unit Price(KD)</th>
                            <th rowspan="2" style="width: 24%;">المبلغ الإجمالي<br>Total Amount(KD)</th>
                        </tr>
                    </thead>
                     <tbody>
            <?php
            $si = 1;
            $grandTotal = 0;
            foreach ($items as $item):
                $grandTotal += $item['total'];
            ?>
            
            <?php
              $discount = $estimate['discount'] ?? 0;
              $discountAmount = ($grandTotal * $discount) / 100;
              $totalAfterDiscount = $grandTotal - $discountAmount;
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

               <div class="mt-2" style="font-size: 13px;">
        <strong>Advance 70% Balance 30% After Delivery</strong>
    </div>
    <div class="row mt-2">
        <!-- Left: In Words -->
        <div class="amount-words col-6" style="font-size: 13px;">
            <b>In Words:</b><br><span id="amount-words" style="font-size: 13px; "></span>
            <?= ucwords($amountInWords ?? '') ?>
        </div>

        <!-- Right: Totals and Signatures -->
        <div class="col-6 ">
          <div style="width: 100%; display: flex; justify-content: flex-end; font-size: 13px;">
            <div style="text-align: right;">
              <div style="font-weight: bold;  text-align: left;">SUBTOTAL
              <?= number_format($grandTotal, 2) ?> </div>
              <div style="font-weight: bold;  text-align: left;">DISCOUNT
              <?= number_format($discount) ?>%</div>
              <div style="border-top: 2px solid black; margin: 2px 0 4px 0;"></div>
              <div style="display: flex; align-items: center; justify-content: flex-end;">
                  <div style="color: #a1263a; font-weight: bold; margin-right: 5px;">Grand total</div>
                  <div style="background-color: #cfc7c7ff; padding: 4px 10px; font-weight: bold;">
                      <?= number_format($totalAfterDiscount, 2) ?></div>
            </div>
          </div>
        </div>
      </div>
        
       <div class="table-footer" style="font-size:13px;">
        <div><b>Receipient Name /اسم المستلم:</b><br><?= esc($user_name ?? '') ?></div>
        <div style="text-align: right;"><b>Receipient Signature / توقيع المستلم</b></div>
      </div>
            </div>
              </div>
   <!-- /.container -->
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

 

</script>