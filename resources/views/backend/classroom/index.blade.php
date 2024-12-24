@extends('backend.layout-backend')

@section('content')
    <style>
        .select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f9f9f9;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .select:focus {
            border-color: #007bff;
            outline: none;
        }
    </style>

    <div class="container my-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="bg-light shadow-sm rounded p-3 mb-4">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="#" class="text-decoration-none">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Classrooms</li>
            </ol>
        </nav>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title">Manage Classrooms</h5>
                    <button type="button" class="btn btn-primary" onclick="showCreateModal()">
                        <i class="fas fa-plus"></i> Add Classroom
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable">
                        <thead class="table-light">
                        <tr>
                            <th>Classroom Name</th>
                            <th>Classroom Type</th>
                            <th>Classroom Grade</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create/Edit Classroom -->
    <div class="modal fade" id="classroomModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Create New Classroom</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="classroomForm">
                    <div class="modal-body">
                        <input type="hidden" id="EDIT_CLSRM_ID" name="CLSRM_ID">
                        <div class="mb-3">
                            <label for="CLSRM_NAME" class="form-label">Classroom Name</label>
                            <input type="text" id="CLSRM_NAME" name="CLSRM_NAME" class="form-control" placeholder="Enter classroom name" required>
                        </div>
                        <div class="mb-3">
                            <label for="CLSRM_TYPE" class="form-label">Classroom Type</label>
                            <input type="text" id="CLSRM_TYPE" name="CLSRM_TYPE" class="form-control" placeholder="Enter classroom type" required>
                        </div>
                        <div class="mb-3">
                            <label for="CLSRM_GRADE" class="form-label">Classroom Grade</label>
                            <input type="text" id="CLSRM_GRADE" name="CLSRM_GRADE" class="form-control" placeholder="Enter classroom grade" required>
                        </div>
                        <div class="mb-3">
                            <label for="CLSRM_DESCRIPTION" class="form-label">Description</label>
                            <textarea id="CLSRM_DESCRIPTION" name="CLSRM_DESCRIPTION" class="form-control" placeholder="Enter classroom description" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', () => {
            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [[ 0, 'asc']],
                iDisplayLength: 50,
                bLengthChange: true,
                bFilter: true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    url: "{{ url('backend/classroom/datatables') }}",
                    type: "POST",
                },
                columns: [
                    { data: "CLASSROOM_NAME" },
                    { data: "CLSRM_TYPE" },
                    { data: "CLSRM_GRADE" },
                    { data: "CLSRM_DESCRIPTION" },
                    { data: "Action" }
                ],
                dom: '<"datatable-header"lf>t<"datatable-footer"ip>',
                responsive: true,
                language: {
                    search: '<span class="text-xs font-medium mb-4 mt-4">Search:</span> _INPUT_',
                    lengthMenu: '<span class="text-xs font-medium mb-4">Show:</span> _MENU_',
                    paginate: {
                        first: '<button class="btn btn-secondary btn-sm">First</button>',
                        last: '<button class="btn btn-secondary btn-sm">Last</button>',
                        next: '<button class="btn btn-secondary btn-sm">&rarr;</button>',
                        previous: '<button class="btn btn-secondary btn-sm">&larr;</button>'
                    },
                    info: '<span class="text-xs">Showing <b>_START_</b> to <b>_END_</b> of <b>_TOTAL_</b> entries</span>',
                    emptyTable: '<i class="text-xs">No data available</i>'
                },
            });
        });

        function showCreateModal() {
            $('#classroomModal').modal('show');
            $('#modalTitle').text('Create New Classroom');
            $('#classroomForm')[0].reset();
            $('#EDIT_CLSRM_ID').val('');
        }

        $('#classroomForm').on('submit', function(e) {
            e.preventDefault();
            // Add your AJAX call here to save the classroom data
        });

        function createOrUpdateData(formData, url, modalId) {
            createOverlay("{{ trans('generic.OverlayProcess') }}");
            $.ajax({
                url: url,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                processData: false,
                contentType: false,
                data: formData,
                success: function (data) {
                    if (data["STATUS"] === "SUCCESS") {
                        toastr.success(data["MESSAGE"]);
                        gOverlay.hide();
                        $('#' + modalId).modal('hide');
                        $('#dataTable').DataTable().ajax.reload();
                    } else {
                        toastr.error(data["MESSAGE"]);
                        gOverlay.hide();
                    }
                },
                error: function (error) {
                    gOverlay.hide();
                    toastr.error("{{ trans('generic.NetworkOrServerError') }}\r\n" + error);
                }
            });
        }
    </script>
@endsection
