<?php include "common/header.php";?>
<div class="form-control mb-3 right_container"> 
    <div class="alert d-none text-center position-fixed" role="alert"></div>
    <div class="row align-items-center">
        <div class="col-md-6">
            <h3 class="mb-0">Manage Companies</h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?= base_url('addcompany') ?>" class="btn btn-secondary">Add New Company</a>
        </div>
    </div>
    <hr>

    <!-- <div id="companyDeleteAlert" class="alert alert-success w-50 mx-auto text-center top mt-3" style="display:none;"></div> -->
    
    <style>
    table.dataTable thead th {
        padding-top: 6px !important;
        padding-bottom: 6px !important;
        font-size: 14px;
        line-height: 1.2 !important;
        vertical-align: middle !important;
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
            { data: "company_name", className: "wrap-text",
                render: function (data) {
                    return data.replace(/\b\w/g, c => c.toUpperCase());
                }
             },
            { data: "address", className: "wrap-text d-none d-md-table-cell",
                render: function (data) {
                    return data.replace(/\b\w/g, c => c.toUpperCase());
                }
             },
             
            {
                data: "tax_number", className: "d-none d-lg-table-cell",
                render: function (data) {
                    return data && data.trim() !== "" ? data : '-N/A-';
                }
            },
         
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
        { searchable: false, orderable: false, targets: [0, 2, 4, 6, 7] }
    ]
    });

  
    table.on('order.dt search.dt draw.dt', function () {
        table.column(0, { search: 'applied', order: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1;
        });
    });
	
	
   $(document).on('click', '.delete-btn', function () {
    const id = $(this).data('id');
    
    if (confirm('Are you sure you want to delete this company?')) {
        $.ajax({
            url: "<?= base_url('managecompany/delete') ?>",
            type: "POST",
            data: { id: id },
            dataType: "json",
            success: function (res) {
                const alertBox = $('.alert');
                if (res.status === 'success') {
                    alertBox.removeClass('d-none alert-success alert-warning alert-danger')
                             .addClass('alert-danger')
                             .text('Company deleted successfully!')
                             .fadeIn();

                    setTimeout(() => {
                        alertBox.fadeOut(() => {
                            alertBox.addClass('d-none').text('');
                        });
                    }, 2000);

                    table.ajax.reload(null, false); 
                } else {
                    alertBox.removeClass('d-none alert-success alert-danger alert-warning')
                             .addClass('alert-warning')
                             .text(res.message || 'Delete failed.')
                             .fadeIn();

                    setTimeout(() => {
                        alertBox.fadeOut(() => {
                            alertBox.addClass('d-none').text('');
                        });
                    }, 3000);
                }
            },
            error: function () {
                const alertBox = $('.alert');
                alertBox.removeClass('d-none alert-success alert-warning')
                        .addClass('alert-danger')
                        .text('Error deleting company.')
                        .fadeIn();

                setTimeout(() => {
                    alertBox.fadeOut(() => {
                        alertBox.addClass('d-none').text('');
                    });
                }, 3000);
            }
        });
    }
});

});
</script>

<?php include "common/footer.php"; ?>
