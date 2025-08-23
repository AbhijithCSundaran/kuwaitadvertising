<?php include "common/header.php"; ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Payment Voucher</title>
        <style>
            body {
                font-family: Arial, sans-serif; 
                font-size: 14px; 
            }
            .voucher-container { 
                width: 800px; 
                margin-left: 25%; 
                /* padding: 20px; */
                position: relative;
                background: url('<?= ASSET_PATH ?>assets/images/invoice-bg.png') no-repeat;
                background-size: 44%;
                background-position: 52% 50%;
                background-color: white;
                border: 3px solid #a1263a;
                border-radius: 10px;
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
                font-size: 40px;  
            }
            .voucher-sub { 
                text-align: center;    
                font-size: 25px; 
                margin-bottom: 25px; 
            }    
            .amount-box {
                position: absolute;
                top: 120px;
                left: 40px;
                border: 1px solid #000;
                width: 175px;
                font-weight: bold;
                text-align: center;
                border-radius: 15px;
                overflow: hidden;
            }
            .amount-box table {
                width: 100%;
                border-collapse: collapse;
            }
            .amount-box td {
                border: 1px solid #000;
                padding: 4px 6px 35px; 
            }
            .amount-box th {
                border: 1px solid #000;
                font-size: 13px;
                background: #f9f9f9;
            }

            .field { 
                margin:25px 20px; 
                font-weight: bold; 
            }
            .label { 
                width: 200px; 
            }
            .dots { 
                border-bottom: 2px dotted #000; 
                display: inline-block; 
                width: 70%; 
                vertical-align: middle; 
            }
            .voucher-no{  
                position: absolute;
                top: 16%;
                left: 78%;
                font-weight: bold;
            }
            .voucher-meta {
                text-align: right; 
                margin-bottom: 45px; 
                font-weight: bold; 
                position: relative;
                right: 25px;
            }
            .signatures { 
                margin-top: 50px; 
                display: flex; 
                justify-content: space-between; 
            }
            .sign-box { 
                width: 45%; 
                text-align: center; 
                padding-top: 10px; 
                font-weight: bold; 
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
                        alt="Company Logo" style=" max-height: 55px; width: 40%; margin-top: 8px;">
                <?php endif; ?>
            </div>
            <div class="amount-box">
                <table>
                    <tr>
                        <th>K.D.دينار</th>
                        <th>Fils فلس</th>
                    </tr>
                    <tr>
                        <td><?= isset($invoice['amount']) ? sprintf("%02d", ($invoice['amount']*100)%100) : '' ?></td>
                        <td><?= isset($invoice['amount']) ? floor($invoice['amount']) : '' ?></td>
                    </tr>
                </table>
            </div>
            <div class="voucher-title"><strong>سند صرف</strong></div>
            <div class="voucher-sub"><strong>PAYMENT VOUCHER</strong></div>
            <div class=" col-6 voucher-no">
                <span class="label">No.:</span> 
                <span style="display:inline-block; width:22%;">&nbsp;</span> <?= esc($invoice['invoice_no'] ?? '') ?>: رقم  
            </div> 
            <div class="voucher-meta">
            
                Date<span class="dots" style=" width: 20%; text-align: center;"> <?= date('d-m-Y') ?></span>: التاريخ
            </div>
            <div class="field">
                <span class="label">Paid To Mr./Mrs.: </span>
                <span class="dots"><?= esc($invoice['customer_name'] ?? '') ?></span> :مدفوع للسيد/السيدة   
            </div>
            <div class="field">
                <span class="label">The Sum of K.D.:</span>
                <span class="dots" style="width:74%;"><?= esc($invoice['total_amount'] ?? '') ?></span> :مجموع K.D
            </div>
            <div class="field">
                <span class="label">Bank: </span>
                <span class="dots" style=" width: 33%;"><?= esc($invoice['bank'] ?? '') ?></span> :بنك Cash / Cheque No. / 
                <span class="dots" style=" width: 27%;"><?= esc($invoice['cheque_no'] ?? '') ?></span> :بموجب شيك رقم
            </div>
            <div class="field">
                <span class="label">Details:</span>
                <span class="dots" style="width: 86%;"><?= esc($invoice['details'] ?? '') ?></span> :تفاصيل
            </div>
            <div class="signatures">
                <div class="sign-box">Accountant Sig. / توقيع المحاسب
                    <span class="dots" style="width: 85%; margin-top: 45px;"></span>
                </div>
                <div class="sign-box">Receiver Sig. / توقيع المستلم
                    <span class="dots" style="width: 85%; margin-top: 45px;"></span>
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
