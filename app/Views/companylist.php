<?php include "common/header.php";?>
<div class="form-control mb-3 right_container"> 
    <div class="row align-items-center">
        <div class="col-md-6">
            <h3 class="mb-0">Manage Companies</h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= base_url('addcompany') ?>" class="btn btn-secondary">Add New Company</a>
        </div>
    </div>
    <hr>

    <div id="companyDeleteAlert" class="alert alert-success w-25 mx-auto text-center fixed top mt-3" style="display:none;"></div>
    
    <style>
    table.dataTable thead th {
        padding-top: 6px !important;
        padding-bottom: 6px !important;
        font-size: 14px;
        line-height: 1.2;
        vertical-align: middle;
    }

    table.dataTable tbody td {
        vertical-align: middle;
        font-size: 14px;
    }

    
    table.dataTable .btn-sm {
        padding: 2px 6px;
        font-size: 12px;
    }

     
    table.dataTable th:nth-child(1),  /* SI NO */
    table.dataTable td:nth-child(1) {
        width: 50px;
        text-align: center;
    }

    table.dataTable th:nth-child(3),  /* Address */
    table.dataTable td:nth-child(3) {
        width: 250px;  
        word-break: break-word;
    }
    
    </style>


    <table class="table table-bordered fixed-table" id="companiesTable" style="width:100%">
        <thead>
            <tr>
                <th style="width: 30px !important;">SI NO</th>
                <th>Name</th>
                <th style="width: 200px;">Address</th>
                <th>Tax Number</th>
                <th style="width: 150px;">Email</th>
                <th>Phone</th>
                <th>Logo</th>
                <th>Action</th>
                <th class="d-none">ID</th> 
            </tr>
        </thead>
        <tbody></tbody> 
    </table>
</div>
</div>

<script>
$(document).ready(function () {
    var table = $('#companiesTable').DataTable({    
        ajax: {
            url: "<?= base_url('managecompany/getAllCompanies') ?>",
            type: "GET",
            dataSrc: ""
        },
        dom: "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
        lengthMenu: [5, 10, 15, 20, 25, 30, 35, 40, 45, 50],
        pageLength: 10,
        columns: [
            { data: null },
            { data: "company_name", className: "wrap-text" },
            { data: "address", className: "wrap-text d-none d-md-table-cell" },// hide on small
            { data: "tax_number", className: "d-none d-lg-table-cell" },         
            { data: "email", className: "d-none d-xl-table-cell" }, 
            { data: "phone", className: "d-none d-xxl-table-cell" },              
            {
                data: "company_logo",
                render: function (data) {
                    return data ? '<img src="<?= base_url('public/uploads/') ?>' + data + '" width="60">' : '';
                }
            },
            {
                data: "company_id",
                render: function (data) {
                    return `
                        <a href="<?= base_url('addcompany/') ?>${data}" class="btn btn-sm btn-primary">Edit</a>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${data}">Delete</button>
                    `;
                }
            },
            { data: "company_id", visible: false }
        ],
        order: [[8, 'desc']],
        
        columnDefs: [
        { searchable: false, orderable: false, targets: [0, 2, 3, 4, 5, 6, 7] }
    ]
    });

    // Serial numbers
    table.on('order.dt search.dt draw.dt', function () {
        table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    });
	
	// Handle delete functionality
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
                        $('#companyDeleteAlert')
                            .text('Company deleted successfully!')
                            .fadeIn()
                            .delay(3000)
                            .fadeOut();
                        table.ajax.reload(null, false); // Reload table without resetting pagination
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
