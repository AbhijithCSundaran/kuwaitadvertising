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
      margin: 40px auto;
      border: 10px solid #991b36;
      padding: 15px;

      background-color: #991b36 ;
    }

    .container {
      width: 720px;
      border: 5px solid #000;
      border-radius: 23px;
      padding: 20px;
      position: relative;
      background: url('/kuwaitadvertising/public/assets/images/invoice-bg.png.png') no-repeat center center;
      background-size: contain;
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
      justify-content: space-between;
      margin-top: 15px;
    }

    .invoice-header .left,
    .invoice-header .right {
      width: 48%;
      font-weight: bold;
    }

    .invoice-header input {
      border: 1px solid #000;
      width: 100px;
      height: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    table, th, td {
      border: 1px solid black;
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
    }

    .footer {
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

    .footer div {
      width: 48%;
    }

    .bottom-bar {
      text-align: center;
      font-size: 12px;
      color: white;
      background-color: #991b36;
      padding: 10px;
      margin-top: 20px;
      /* border-top:  solid white; */
    }
  </style>
</head>
<body>

<!-- ✅ Outer brown container starts here -->
<div class="outer-container">

  <div class="container">

    <!-- Top Logo and Title -->
    <div class="top-heading">
      <img src="<?= base_url('assets/images/invoice-heading.png') ?>" alt="Invoice Heading"><br>
      ____________________________________________________________________________________________
      <div class="invoice-type">فاتورة / نقداً / بالحساب<br>Cash / Credit Invoice</div>
    </div>

    <!-- Header Info -->
    <div class="invoice-header">
      <div class="left">
        No.: <input type="text"><br><br>
        Mr./Mrs:____________________________________________________________________________________ <br><br>
        Address:___________________________________________________________________________________
      </div>
      <div class="right" style="text-align: right;">
        Date: <input type="text"><br><br>
      </div>
    </div>

    <!-- Invoice Table -->
    <table>
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
      <tr>
        <td>1</td><td>Product Example</td><td>2</td><td>5</td><td>250</td><td>10</td><td>500</td>
      </tr>
      </tbody>
      <tfoot>
      <tr>
        <td colspan="5"><strong>Total Amount</strong></td>
        <td colspan="2" id="total-amount">KD 10.500</td>
      </tr>
      </tfoot>
    </table>

    <!-- Amount in words -->
    <div class="amount-words">
      Amount Chargeable (in words): <span id="amount-words"></span>
    </div>

    <!-- Footer -->
    <div class="footer">
      <div>Received by. المستلم</div>
      <div style="text-align: right;">Salesman Signature. توقيع البائع</div>
    </div>


  </div> <!-- /.container -->
<!-- Bottom Bar -->
    <div class="bottom-bar">
      الراي ، قطعة ٣ ، شارع ٣٢ ، مبنى رقم ٤٣٧ ، محل رقم ٤ ، بالقرب من زجاج الروان ، الشويخ - الكويت<br>
      Al-Rai, Block 3, Street 32, Build No. 437, Shop No. 4, Near Al Rawan Glass, Shuwaik - Kuwait<br>
      📞 +965 6006 0102 &nbsp;&nbsp; | &nbsp;&nbsp; 📧 alraiprintpress@gmail.com
    </div>
</div> <!-- /.outer-container -->

<!-- JS to convert number to words -->
<script>
  function numberToWords(num) {
    const a = ['','one','two','three','four','five','six','seven','eight','nine','ten','eleven',
    'twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen'];
    const b = ['', '', 'twenty','thirty','forty','fifty','sixty','seventy','eighty','ninety'];

    if ((num = num.toString()).length > 9) return 'overflow';

    let [dinars, fils] = num.split('.');
    dinars = parseInt(dinars);
    fils = parseInt(fils || '0');

    const convert = (n) => {
      if (n < 20) return a[n];
      if (n < 100) return b[Math.floor(n/10)] + (n % 10 ? '-' + a[n % 10] : '');
      if (n < 1000) return a[Math.floor(n/100)] + ' hundred ' + (n % 100 ? convert(n % 100) : '');
      if (n < 1000000) return convert(Math.floor(n/1000)) + ' thousand ' + convert(n % 1000);
      return '';
    };

    let words = '';
    if (dinars > 0) words += convert(dinars) + ' Kuwaiti Dinar';
    if (fils > 0) words += (words ? ' and ' : '') + convert(fils) + ' Fils';
    return words || 'Zero';
  }

  document.getElementById("amount-words").innerText = numberToWords(10.5);
</script>

</body>
</html>
