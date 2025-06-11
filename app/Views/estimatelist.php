<?php include "common/header.php"; ?>
<div class="form-control mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h3 class="mb-0">Estimate Directory</h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= base_url('add_estimate') ?>" class="btn btn-secondary">Add New Estimate</a>
        </div>
    </div>
    <hr>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-bordered" id="estimateTable">
                <thead>
                    <tr>
                        <th>SI</th>
                        <th>Customer Name</th>
                        <th>Customer Address</th>
                        <th>Total Amount</th>
                        <th>Discount</th>
                        <th>Date</th>
                        <th>Description</th> 
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Populated via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
<?php include "common/footer.php"; ?>

<script>
function loadEstimates() {
    $.ajax({
        url: "<?= base_url('estimate/estimatelistajax') ?>",
        method: "GET",
        dataType: "json",
        success: function(data) {
            let table = $('#estimateTable').DataTable();
            table.clear();

            if (data.data && data.data.length > 0) {
                data.data.forEach(function(e, index) {
                    table.row.add([
						index + 1,
						e.customer_name,
						e.customer_address,
						parseFloat(e.total_amount).toFixed(2) + " KWD",
						parseFloat(e.discount).toFixed(2) + " KWD",
						new Date(e.date).toLocaleDateString(),
						e.description ? e.description : '-',
						`
							<a href="<?= base_url('estimate/edit/') ?>${e.estimate_id}" class="btn btn-sm btn-primary-edit">Edit</a>
							<button class="btn btn-sm btn-danger" onclick="deleteEstimate(${e.estimate_id})">Delete</button>
						`
					]);
                });
            }

            table.draw();
        },
        error: function() {
            alert('Failed to load estimates.');
        }
    });
}

function deleteEstimate(id) {
    if (confirm('Are you sure you want to delete this estimate?')) {
        $.ajax({
            url: "<?= base_url('estimate/delete') ?>",
            method: "POST",
            data: { estimate_id: id },
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    loadEstimates();
                } else {
                    alert('Failed to delete estimate.');
                }
            },
            error: function() {
                alert('Error occurred while deleting estimate.');
            }
        });
    }
}

$(document).ready(function() {
    $('#estimateTable').DataTable({
        destroy: true,
        paging: true,
        searching: true,
        ordering: false,
        info: false
    });

    loadEstimates();
});
</script>
