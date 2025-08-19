<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <meta charset="UTF-8">
  <title>Receipt Voucher</title>
  <style>
    @page {
      size: A5;
      margin: 20mm;
    }
    body {
      font-family: Arial, sans-serif;
      background: #fff;
      color: #000;
      font-size: 14px;
    }
    .voucher {
      border: 1px solid #000;
      padding: 15px;
      position: relative;
    }
    /* Header */
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .logo {
      width: 120px;
      height: 50px;
      background: #ddd;
      text-align: center;
      line-height: 50px;
      font-size: 12px;
    }
    .company {
      text-align: center;
      flex: 1;
      font-weight: bold;
    }
    .company .en {
      font-size: 16px;
    }
    .company .ar {
      font-size: 16px;
      font-weight: bold;
    }
    .voucher-title {
      text-align: center;
      margin-top: 10px;
      font-size: 18px;
      font-weight: bold;
    }
    .voucher-title .ar {
      display: block;
      font-size: 16px;
    }
    /* Currency boxes */
    .currency-boxes {
      border: 1px solid #000;
      display: inline-block;
      margin-top: 10px;
    }
    .currency-row {
      display: flex;
      border-bottom: 1px solid #000;
    }
    .currency-cell {
      flex: 1;
      border-left: 1px solid #000;
      text-align: center;
      padding: 5px;
      font-weight: bold;
    }
    .currency-label {
      text-align: center;
      font-size: 12px;
    }
    /* Top-right No/Date */
    .top-right {
      position: absolute;
      top: 20px;
      right: 20px;
      text-align: right;
      font-size: 14px;
    }
    /* Fields */
    .fields {
      margin-top: 20px;
    }
    .field {
      display: flex;
      justify-content: space-between;
      margin-bottom: 15px;
    }
    .field .label {
      flex: 0 0 200px;
    }
    .field .line {
      flex: 1;
      border-bottom: 1px dotted #000;
      margin: 0 10px;
    }
    /* Signatures */
    .signatures {
      display: flex;
      justify-content: space-between;
      margin-top: 30px;
    }
    .signature {
      text-align: center;
      flex: 0 0 45%;
    }
    .signature .label {
      margin-top: 40px;
      font-weight: bold;
    }
    /* Footer */
    .footer {
      background: #7c2d35;
      color: #fff;
      text-align: center;
      font-size: 12px;
      padding: 8px;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="voucher">
    <div class="header">
      <div class="logo">LOGO</div>
      <div class="company">
        <div class="ar">مطـبـعــة الـرأي لأعمـــال الطباعة</div>
        <div class="en">Alrai Printing Press</div>
      </div>
    </div>

    <div class="voucher-title">
      Receipt Voucher
      <span class="ar">سند قبض</span>
    </div>

    <div class="currency-boxes">
      <div class="currency-row">
        <div class="currency-cell">K.D.</div>
        <div class="currency-cell">Fils</div>
      </div>
      <div class="currency-label">دينار / فلس</div>
    </div>

    <div class="top-right">
      No: ______ <br>
      Date: __________ <br>
      التاريخ :
    </div>

    <div class="fields">
      <div class="field">
        <div class="label">Received From</div>
        <div class="line"></div>
        <div class="label">استلمنا من السيد / السادة</div>
      </div>
      <div class="field">
        <div class="label">The Sum Of K.D.</div>
        <div class="line"></div>
        <div class="label">مبلغا وقدره دينار</div>
      </div>
      <div class="field">
        <div class="label">Cash / Cheque No.</div>
        <div class="line"></div>
        <div class="label">نقداً / شيك رقم</div>
      </div>
      <div class="field">
        <div class="label">Being of</div>
        <div class="line"></div>
        <div class="label">وذلك عن</div>
      </div>
    </div>

    <div class="signatures">
      <div class="signature">
        <div class="label">Receiver<br>توقيع المستلم</div>
      </div>
      <div class="signature">
        <div class="label">Cashier<br>أمين الصندوق</div>
      </div>
    </div>

    <div class="footer">
      Al-Rai, Block 3, Street 32, Build No. 437, Shop No. 4, Near Al Rawan Glass, Shuwaik - Kuwait <br>
      +965 6006 0102 &nbsp; | &nbsp; alraiprintpress@gmail.com
    </div>
  </div>
</body>
</html>
