<?php include "common/header.php"; ?>
<div class="form-control"> 
	<div class="row">
		<div class="col-md-6">
			<h3>Company List</h3>
		</div>
		<div class="col-md-6 text-right">
			<a href="<?= base_url('addcompany') ?>"><button class="btn btn-secondary">Add New Company</button></a>
		</div>
		<div class="col-md-12"><hr/></div>
	</div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Address</th>
                <th>Tax Number</th>
                <th>Logo</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($companies as $company): ?>
                <tr>
                    <td><?= esc($company['company_name']) ?></td>
                    <td><?= esc($company['address']) ?></td>
                    <td><?= esc($company['tax_number']) ?></td>
                    <td>
                        <?php if ($company['company_logo']): ?>
                            <img src="<?= base_url('public/uploads/' . $company['company_logo']) ?>" width="60">
                        <?php endif; ?>
                    </td>
                     <td>
						<button class="btn btn-sm btn-primary edit-btn" data-id="<?= $company['company_id'] ?>">Edit</button>
						<button class="btn btn-sm btn-danger delete-btn" data-id="<?= $company['company_id'] ?>">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>

<!-- Optional Modal for Edit Form -->
<div class="modal fade modal-margin" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="edit-form" enctype="multipart/form-data">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Edit Company</h5></div>
        <div class="modal-body">
          <input type="hidden" name="uid" id="edit_uid">
          <div class="form-group">
            <label>Name</label>
            <input type="text" name="company_name" id="edit_name" class="form-control">
          </div>
          <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" id="edit_address" class="form-control">
          </div>
          <div class="form-group">
            <label>Tax Number</label>
            <input type="text" name="tax_number" id="edit_tax_number" class="form-control">
          </div>
          <div class="form-group">
            <label>Logo</label>
            <input type="file" name="company_logo" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Update</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>


<script>
$(document).ready(function () {
    $('.edit-btn').click(function () {
        let id = $(this).data('id');

        $.get('<?= base_url('managecompany/getCompany') ?>/' + id, function (data) {
            if (data) {
                $('#edit_uid').val(data.company_id);
                $('#edit_name').val(data.company_name);
                $('#edit_address').val(data.address);
                $('#edit_tax_number').val(data.tax_number);
                $('#editModal').modal('show');
            } else {
                alert('Failed to fetch data.');
            }
        }).fail(function () {
            alert('Error fetching company details.');
        });
    });

    $('#edit-form').on('submit', function (e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: '<?= base_url('managecompany/save') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    $('#editModal').modal('hide');
                    location.reload();
                } else {
                    alert(response.message || 'Update failed.');
                }
            },
            error: function () {
                alert('Something went wrong during update.');
            }
        });
    });	
	
	$(document).on('click', '.delete-btn', function () {
    let id = $(this).data('id');

		if (confirm('Are you sure you want to delete this company?')) {
			$.ajax({
				url: '<?= base_url('managecompany/delete') ?>/' + id,
				type: 'POST',
				data: { _method: 'DELETE' },
				dataType: 'json',
				success: function (response) {
					if (response.status === 'success') {
						alert('Company deleted successfully.');
						location.reload();
					} else {
						alert(response.message || 'Delete failed.');
					}
				},
				error: function () {
					alert('Something went wrong during deletion.');
				}
			});
		}
	});
});
</script>

<?php include "common/footer.php"; ?>
