
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
      width: 100%;
      min-width: 900px;
      margin: auto;
    }

    .invoice-header {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
    }

    .address {
      font-size: 16px;
      line-height: 1.8;
    }

    .company-logo {
      text-align: right;
    }

    .company-logo img {
      height: 150px;

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
      font-size: 1.75rem;
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

    .bill-to,
    .ship-to {
      width: 48%;
    }

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
      border: 1px solid #000;
      margin-top: 20px;
      float: right;
    }

    .recipient-box table {
      width: 100%;
      border-collapse: collapse;
    }

    .recipient-box td {
      border: 1px solid #000;
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
      <div class="invoice-header">
        <div class="address">
          <em>Al-Shuwaikh Area, 3</em><br>
          <em>Behind Sultan Center - 4th Ring Road</em><br>
          <em>Tel: +965 600 60 102</em>
        </div>
        <div class="company-logo">
          <img src="<?php echo ASSET_PATH; ?>assets/images/invoice-logo.png" alt="Invoicelogo">
          <!-- <div class="company-name">Al Shaya International Printing Co</div>
        <div class="company-arabic">شركة الشايع للطباعة الدولية</div> -->
        </div>
      </div>

      <h2 class="invoice-title">INVOICE NO. 1009</h2>

      <!-- Invoice Info -->
      <table class="info-table">
        <tr>
          <td>Invoice Date:</td>
          <td>1.5.2025</td>
        </tr>
        <tr>
          <td>Delivery Date:</td>
          <td>30.4.2025</td>
        </tr>
        <tr>
          <td>Delivery Note No:</td>
          <td>109</td>
        </tr>
        <tr>
          <td>LPO No:</td>
          <td>242501637</td>
        </tr>
      </table>

      <!-- Billing & Shipping -->
      <div class="bill-ship">
        <div class="bill-to">
          <div class="label">BILL TO: <strong>ALBATAIN AUTO (GULFEX)</strong></div>
          <div>Person Name: <strong>Mr. Ajith Abraham</strong></div>
          <div>Business Name:<strong> ALBATAIN AUTO (GULFEX)</strong></div>
          <div>Address: <strong>Al-Rai</strong></div>
          <div>Contact Number:<strong>97747515585</strong></div>
        </div>

        <div class="ship-to">
          <div class="label">SHIP TO:<strong> ALBATAIN AUTO (GULFEX)</strong></div>
          <div>Person Name:<strong> Mr. Ajith</strong></div>
          <div>Business Name:<strong> M/S. Al-Babtain Auto - Al Rai</strong></div>
          <div>Address:<strong>Al Rai Albatain Auto Al Shai Gulf Dubai China America, Al Rai Albatain Auto Al Shai Gulf
              Dubai China America </strong></div>
          <div>Contact Number:<strong>147852693</strong></div>
        </div>
      </div>


      <!-- Items Table -->
      <table class="items-table table-striped">
        <thead>
          <tr>
            <th>SR. NO</th>
            <th>DESCRIPTION</th>
            <th>QTY</th>
            <th>UNIT PRICE (KD)</th>
            <th>TOTAL (KD)</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>ABA Spring Offer Roll up changing</td>
            <td>10</td>
            <td>7.000</td>
            <td>70.000</td>
          </tr>
          <tr>
            <td>2</td>
            <td>ABA Spring Offer new Roll up supply</td>
            <td>2</td>
            <td>10.000</td>
            <td>20.000</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
        </tbody>
      </table>

      <!-- Totals -->
      <div class="totals">
        <div class="totals-row">
          <div class="tot">SUBTOTAL</div>
          <div class="value">90.000</div>
        </div>
        <div class="totals-row">
          <div class="tot">DISCOUNT</div>
          <div class="value">0.000</div>
        </div>
      </div>



      <div class="totals grand-total">
        <div class="totals-row">
          <strong>Grand Total</strong>
          <span>KD90.000</span>
        </div>
      </div>
      <div class="words"><strong>In Words: Kuwaiti Dinars Ninety Only</strong></div>
      <!-- Footer -->
      <div class="invoice-footer-text">
        <div class="thanks">Thank you for your business!</div>
        <div class="cheque">Please issue the cheque in the favor of:</div>
        <div class="compname"><strong>Al Shaya International Printing Co</strong></div>
      </div>

      <!-- Recipient -->
      <div class="recipient-box">
        <table>
          <tr>
            <td colspan="2">Received the above in good order</td>
          </tr>
          <tr>
            <td>Recipient Name:</td>
            <td>Accountant:</td>
          </tr>
          <tr>
            <td colspan="2">Signature:</td>
          </tr>
        </table>
      </div>
<!-- Delivery Note Popup -->
<div id="deliveryNoteModal" class="modal" style="display:none; position: fixed; z-index: 9999; left: 0; top: 0;
  width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
  <div style="background-color: #fff; margin: 15% auto; padding: 20px; border-radius: 10px; width: 300px; text-align: center;">
    <p>Do you want to download the delivery note?</p>
    <button onclick="downloadDeliveryNote()" style="background-color: #28a745; color: white; border: none; padding: 8px 12px; border-radius: 5px; margin: 5px;">
      Download
    </button>
    <button onclick="closeModal()" style="background-color: #dc3545; color: white; border: none; padding: 8px 12px; border-radius: 5px; margin: 5px;">
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
