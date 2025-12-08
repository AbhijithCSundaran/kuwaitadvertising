<?php include "common/header.php"; ?>

<!-- <div class="container mt-4"> -->
<div class="form-control mb-3 right_container">
    <div class="alert d-none text-center position-fixed" role="alert"></div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Edit Receipt Voucher</h3>
        </div>
        <hr class="d-none d-md-block">

        <div class="card-body p-3 px-md-4">
            <form method="post" action="<?= base_url('receiptvoucher/update/' . $transaction_id) ?>">
                 <input type="hidden" name="transaction_id" value="<?= $transaction_id ?>">
                <input type="hidden" name="customer_id" value="<?= esc($customer['customer_id']) ?>">

                <div class="row">
                            <div class="form-group mb-3">
            <label>Received From Mr/Ms:</label>
            <input type="text" class="form-control" value="<?= esc($customer['customer_name'] ?? '') ?>" disabled>
        </div>



                    <div class="col-md-3 mb-3">
            <label>K.D. Amount</label>
            <input type="number" name="kd" class="form-control" value="<?= esc($kd) ?>" min="0">
        </div>

                    <div class="col-md-3 mb-3">
            <label>Fils Amount</label>
            <input type="number" name="fils" class="form-control" value="<?= esc($fils) ?>" min="0" max="999">
        </div>


                    <div class="col-md-6 mb-3">
            <label>Cash / Cheque No. / K-Net</label>
            <input type="text" name="cash_cheque_knet" class="form-control" value="<?= esc($cash_cheque_knet) ?>">
        </div>


                    <div class="col-md-6 mb-3">
            <label>Being Of:</label>
            <input type="text" name="being_of" class="form-control" value="<?= esc($being_of) ?>">
        </div>
                </div>

                <div class="form-group mt-3 text-end">
        <a href="<?= base_url('receiptvoucher/index/' . $transaction_id) ?>" class="btn btn-secondary">Discard</a>
        <button type="submit" class="btn btn-success">Save</button>
    </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>

<?php include "common/footer.php"; ?>