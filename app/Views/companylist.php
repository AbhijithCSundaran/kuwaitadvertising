<?php include "common/header.php"; ?>

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
<?php include "common/footer.php"; ?>   
<script>
    let table="";
        $(document).ready(function () { 
            const alertBox = $('.alert');
            table = $('#companiesTable').DataTable({
                ajax: {
                    url: "<?= base_url('managecompany/companylistjson') ?>",
                    type: "POST",
                    dataSrc: "data" 
                },
                sort:true,
                searching:true,
                paging:true,
                processing: true,
                serverSide: true,
                dom: "<'row mb-3'<'col-sm-6'l><'col-sm-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
                columns: [
                {
                    data: "slno",
                    render: function (data) {
                        return data;
                    }
                },
                    { 
                        data: "company_name", 
                        className: "wrap-text",
                        render: function (data) {
                            return data ? data.replace(/\b\w/g, c => c.toUpperCase()) : '';
                        }
                    },
                    { 
                        data: "address", 
                        className: "wrap-text d-none d-md-table-cell",
                        render: function (data) {
                            return data ? data.replace(/\b\w/g, c => c.toUpperCase()) : '';
                        }
                    },
                    {
                        data: "tax_number", 
                        className: "d-none d-lg-table-cell",
                        render: function (data) {
                            return data && data.trim() !== "" ? data : '-N/A-';
                        }
                    },
                    { data: "email", className: "d-none d-xl-table-cell" },
                    { data: "phone", className: "d-none d-xxl-table-cell" },

                    {
                    data: "company_logo",
                    className: "logo-col",
                    render: function (data) {
                        return data ? '<img src="<?= base_url('public/uploads/') ?>' + data + '" width="30">' : '';
                    }
                },
                                    {
                        data: "company_id",
                        render: function (data) {
                            return `
                                <div class="d-flex align-items-center gap-3">
                                    <a href="<?= base_url('addcompany/') ?>${data}" title="Edit" style="color: rgb(13, 162, 199);">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="delete-all" data-id="${data}" title="Delete" style="color: #dc3545;">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </div>

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
                table.column(0, { search: 'applied', order: 'applied' }).nodes()
                .each(function (cell, i) {
                    var pageInfo = table.page.info();
                    cell.innerHTML = pageInfo.start + i + 1;
                });
            });

            let deleteId = null;
            let deleteModal; 

            $(document).ready(function () {
                const alertBox = $('.alert');
                deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));

                $(document).on('click', '.delete-all', function () {
                    deleteId = $(this).data('id');
                    deleteModal.show(); 
                });

                
                $(document).on('click', '#confirm-delete-btn', function () {
                    if (!deleteId) return;

                    $.ajax({
                        url: "<?= base_url('managecompany/delete') ?>", 
                        type: "POST",
                        data: {
                            id: deleteId,
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        },
                        dataType: "json",
                        success: function (res) {
                            if (res.status === 'success') {
                                alertBox.removeClass().addClass('alert alert-danger text-center position-fixed')
                                    .text('Company Deleted Successfully.').fadeIn();
                                setTimeout(() => alertBox.fadeOut(), 2000);
                                table.ajax.reload(null, false);
                            } else {
                                alertBox.removeClass().addClass('alert alert-warning text-center position-fixed')
                                    .text(res.message || 'Delete Failed.').fadeIn();
                                setTimeout(() => alertBox.fadeOut(), 3000);
                            }
                        },
                        error: function () {
                            alertBox.removeClass().addClass('alert alert-danger text-center position-fixed')
                                .text('Error Occurred While Deleting Company.').fadeIn();
                            setTimeout(() => alertBox.fadeOut(), 3000);
                        },
                        complete: function () {
                            deleteModal.hide();
                            deleteId = null;
                        }
                    });

                });
            });

        });
</script>
