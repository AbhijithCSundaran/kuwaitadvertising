<?php include "common/header.php"; ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Receipt Voucher</title>
        <style>
            body {
                font-family: Arial, sans-serif; 
                font-size: 14px; 
            }
            .voucher-container { 
                width: 800px; 
                margin-left: 25%; 
                padding: 20px;
                position: relative;
                background: url('<?= ASSET_PATH ?>assets/images/invoice-bg.png') no-repeat;
                background-size: 44%;
                background-position: 52% 50%;
                background-color: white;
            }
            .header { 
                text-align: center; 
                margin-bottom: 10px; 
            }
            .header img {
                max-height: 70px; 
                display: block; 
                margin: auto; 
            }
            .company-name { 
                font-size: 20px; 
                font-weight: bold; 
                margin-top: 10px; 
            }
            .voucher-title { 
                text-align: center;
                font-size: 33px;  
            }
            .voucher-sub { 
                text-align: center;    
                font-size: 21px; 
                margin-bottom: 64px; 
            }    
            .amount-box {
                display: flex;
                margin-top: 10px;
                gap: 1px;
                position: absolute;
                top: 120px;
                left: 43px;
            }

            .amount-field {
                text-align: center;
            }

            .amount-label {
                font-weight: bold;
                display: block;
                margin-bottom: 5px;
            }

            .amount-value {
                border: 2px solid black;
                border-radius: 12px;
                padding: 15px 30px;
                min-width: 100px;
                font-size: 18px;
                font-weight: bold;
            }
            .field { 
                margin:25px 15px; 
                font-weight: bold; 
            }
            .label { 
                width: 200px; 
            }
            .dots { 
                border-bottom: 2px dotted #000; 
                display: inline-block; 
                width: 72%; 
                vertical-align: middle; 
            }
            .voucher-no{  
                position: absolute;
                top: 20%;
                left: 70%;
            }
            .voucher-meta {
                text-align: right; 
                margin-bottom: 45px; 
                font-weight: bold; 
                position: relative;
                right: 35px;
            }
            .signatures { 
                margin-top: 25px; 
                display: flex; 
                justify-content: space-between; 
                margin-left: 20px;
            }
            .sign-box { 
                width: 40%; 
                text-align: left; 
                padding-top: 10px; 
                font-weight: bold; 
            }
            .sign-label {
                display: flex;
                justify-content: left;
                gap: 65px; 
            }
            .sign-cash{
                 display: flex;
                justify-content: right;
                gap: 85px; 
                margin-right: 56px;
            }
            .bottom-footer { 
                text-align: center; 
                font-size: 12px; 
                margin-top: 40px; 
                line-height: 1.5; 
                background:  #a1263a; 
                color: #fff; 
                padding: 10px; 
            }
            .label{
                font-size:15px;
            }
        </style>
    </head>
    <body>
        <div class="voucher-container">
            <div class="header">
                <?php if (!empty($company['company_logo'])): ?>
                    <img src="<?= base_url('public/uploads/' . $company['company_logo']) ?>" 
                        alt="Company Logo" style=" max-height: 70px; width: 45%;">
                <?php endif; ?>
            </div>
            <div class="voucher-title"><strong> سند قبض</strong></div>
            <div class="voucher-sub"><strong>Receipt Voucher</strong></div>
            <div class="amount-box">
              <div class="amount-field">
                  <span class="amount-label">K.D. دينار</span>
                  <div class="amount-value">
                      <?= isset($invoice['amount']) ? floor($invoice['amount']) : '-' ?>
                  </div>
              </div>
              <div class="amount-field">
                  <span class="amount-label">Fils فلس</span>
                  <div class="amount-value">
                      <?= isset($invoice['amount']) ? sprintf("%02d", ($invoice['amount']*100)%100) : '-' ?>
                  </div>
              </div>
          </div>
            <div class=" col-6 voucher-no">
                <span class="label" style="font-size: 20px;"><strong>No:</strong></span> 
            </div> 
            <div class="voucher-meta">
            
                Date:<span class="dots" style=" width: 20%; text-align: center;"> <?= date('d-m-Y') ?></span> التاريخ:
            </div>
            <div class="field">
                <span class="label">Received From : </span>
                <span class="dots"><?= esc($customer['customer_name'] ?? '') ?></span>تم الاستلام من:
            </div>
            <div class="field">
                <span class="label">The Sum of K.D.</span>
                <span class="dots" style="width:73%;"><?= esc($invoice['amount'] ?? '') ?></span>مجموع K.D:
            </div>
            <div class="field">
                <span class="label"> Cash / Cheque No.</span>
                <span class="dots" style=" width: 69%;"><?= esc($invoice['cheque_no'] ?? '') ?></span>بموجب شيك رقم:
            </div>
            <div class="field">
                <span class="label">Being Of.</span>
                <span class="dots" style="width: 83%;"><?= esc($invoice['details'] ?? '') ?></span>كونه من
            </div>
            <div class="col-12 signatures">
                <div class="col-6 sign-box">
                  <div class="sign-label">
                      <span>Receiver</span>
                      <span>المتلقي</span>
                  </div>
                    <span class="dots" style="width: 70%; margin-top: 50px;"></span>
                </div>
                <div class="col-6 sign-box">
                   <div class="sign-cash">
                       <span>Cashier </span>
                      <span> أمين الصندوق</span>
                   </div>  
                    <span class="dots" style="width: 83%; margin-top: 50px;"></span>
                </div>
            </div>
            <!-- Footer -->
            <div class="bottom-footer">
            <div style="direction: rtl; text-align: center;"><?= esc($company['address_ar'] ?? '') ?></div>
            <div style="direction: ltr; text-align: center;"><?= esc($company['address'] ?? '') ?></div>
            <div style="margin-top: 5px;">
                📞 <?= esc($company['phone'] ?? '') ?> &nbsp;&nbsp; | &nbsp;&nbsp;
                📧 <a href="mailto:<?= esc($company['email'] ?? '') ?>" style="color: white; text-decoration: none;">
                    <?= esc($company['email'] ?? '') ?>
                    </a>
            </div>
            </div>
        </div>
        </div>
    </body>
</html>
<?php include "common/footer.php"; ?>
