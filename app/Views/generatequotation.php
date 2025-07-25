<?php include "common/header.php"; ?>
<style>
    @media print {
        .no-print,
        .navbar,
        .footer,
        .sidebar {
            display: none !important;
        }

        body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .estimate-header,
        .estimate-footer {
            text-align: center;
        }
        .company-logo{
            text-align: right;
        }
       
    }
     .company-logo img {
            max-height: 125px;
            width: auto;
        }
         .estimate-title {
               text-align: center;
                font-size: x-large;
        }
        .table-bordered th, .table-bordered td {
            border: 2px solid black;
            padding: 5px 10px;
            font-weight: bold;
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
    }

    .items-table td {
      border: 1px solid #ccc;
      padding: 6px;
      height: 30px;
    }
    .name-table{
        padding-top: 35px;
    }
     .amount-words {
      margin-top: 20px;
      margin-bottom: 20px;
      /* font-weight: bold; */
    }
    hr {
    margin: 0rem 0;
    padding:1px;
    }
</style>
 <div class="right_container">
    <div class="no-print" style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
      <button onclick="window.print()"
        style="background-color: #a1263a; color: white; padding: 8px 16px; border: none; border-radius: 5px;">
        🖨️ Print
      </button>
      <button onclick="window.location.href='<?= base_url('estimate/edit/' . $estimate['estimate_id']) ?>'"
        style="background-color: #a1263a; color: white; padding: 8px 16px; border: none; border-radius: 5px; margin-left: 10px;">
        Discard
      </button>

    </div>
    <div class="estimate-container">

      <!-- Header -->
      <div class="d-flex col-12 mb-3">
        <div class="col-6">
          <div class="address">
            <em>Al-Shuwaikh Area, 3</em><br>
            <em>Behind Sultan Center - 4th Ring Road</em><br>
            <em>Tel: +965 60060102</em>
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
        <div class="estimate-title"><strong><u>QUOTATION</u></strong></strong></div>
   
        <div class="col-md-6">
             <strong>TO: M/S. <?= esc($estimate['customer_name'] ?? 'Customer Name') ?></strong><br>
    </div>
    <hr style="height: 2px; background-color: black; border: none;">

    <div class="row">
        <div class="col-md-5">
            Person Name:
            <?= esc($estimate['customer_name'] ?? '') ?><br>
            Business Name:
            <?= esc($company_name) ?><br>
            Address:
            <?= esc($estimate['customer_address'] ?? '') ?><br>
            Contact Number:
             <?= esc($estimate['phone_number']) ?>
        </div>
       <div class="col-md-7 d-flex justify-content-end " style="margin-top: -3px">
          <table class="table table-bordered w-auto mb-0">
              <tr>
                  <th>Quote Date</th>
                  <td><?= date('d-m-Y', strtotime($estimate['date'])) ?></td>
              </tr>
              <tr>
                  <th>Quote No.</th>
                  <td><?= $estimate['estimate_id'] ?></td>
              </tr>
          </table>
        </div>

    </div>

    <table class=" items-table table-striped">
        <thead>
            <tr>
                <th>SR. NO</th>
                <th>DESCRIPTION</th>
                <th>QTY</th>
                <th>UNIT PRICE(KD)</th>
                <th>TOTAL AMOUNT(KD)</th>
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

   <!-- Terms -->
    <div class="mt-4">
        <strong>Advance 70% Balance 30% After Delivery</strong>
    </div>
    <div class="row mt-3">
        <!-- Left: In Words -->
        <div class="amount-words col-md-6">
            <b>In Words:</b><br><br><span id="amount-words"></span>
            <?= ucwords($amountInWords ?? '') ?>
        </div>

        <!-- Right: Totals and Signatures -->
        <div class="col-md-6 ">
          <div style="width: 100%; display: flex; justify-content: flex-end;">
            <div style="text-align: right;">
              <div style="font-weight: bold;  text-align: left;">SUBTOTAL
              <?= number_format($grandTotal, 2) ?> </div>
              <div style="font-weight: bold;  text-align: left;">DISCOUNT
              <?= number_format($discount) ?>%</div>
              <div style="border-top: 2px solid black; margin: 2px 0 4px 0;"></div>
              <div style="display: flex; align-items: center; justify-content: flex-end;">
                  <div style="color: red; font-weight: bold; margin-right: 5px;">Grand total</div>
                  <div style="background-color: #f08080; padding: 4px 10px; font-weight: bold;">
                      <?= number_format($totalAfterDiscount, 2) ?></div>
            </div>
          </div>
        </div>
         <div class="name-table col-md-12 d-flex justify-content-end">
            <table class="table table-bordered w-auto mb-0 pt-6">
                <tr>
                    <th class="text-center">Authorized Signatory</th>
                    <td></td>
                </tr>
                <tr>
                    <td><strong>Receipient Name:</strong></td>
                    <td><?= esc($user_name ?? '') ?></td>
                </tr>
                <tr>
                    <td><strong>Signature:</strong></td>
                    <td></td>
                </tr>
            </table>
        </div>
        </div>
    </div>

</div>
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

  const grandTotal = <?= json_encode(number_format($estimate['total_amount'] ?? 0, 3, '.', '')) ?>;

 let englishWords = numberToWords(grandTotal);
    englishWords = englishWords.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());

    const arabicWords = numberToArabicWords(grandTotal);

  document.getElementById("amount-words").innerHTML = `
    ${englishWords}<br><span style="font-family: 'Amiri', serif;">${arabicWords}</span>
  `;
</script>