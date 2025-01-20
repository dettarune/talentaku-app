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

        #imagePreview {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
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
                <li class="breadcrumb-item active" aria-current="page">Students</li>
            </ol>
        </nav>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title">Manage Students</h5>
                    <button type="button" class="btn btn-primary" onclick="createData()">
                        <i class="fas fa-plus"></i> Add Student
                    </button>
                </div>
{{--                <div class="mb-3">--}}
{{--                    <label for="parent" class="form-label">Parent</label>--}}
{{--                    <select id="parent" name="STUDENT_PARENT_U_ID" class="form-control " required>--}}
{{--                        <option value="" disabled selected>Select Parent</option>--}}
{{--                        @foreach($unusedParents as $parent)--}}
{{--                            <option value="{{ $parent->U_ID }}" {{ old('STUDENT_PARENT_U_ID') == $parent->U_ID ? 'selected' : '' }}>--}}
{{--                                {{ $parent->U_NAME }}--}}
{{--                            </option>--}}
{{--                        @endforeach--}}

{{--                        <!-- Then show used parents below, disabled -->--}}
{{--                        @foreach($usedParents as $parent)--}}
{{--                            <option value="{{ $parent->U_ID }}" disabled>--}}
{{--                                {{ $parent->U_NAME }} (Used)--}}
{{--                            </option>--}}
{{--                        @endforeach--}}
{{--                    </select>--}}
{{--                </div>--}}

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable">
                        <thead class="table-light">
                        <tr>
                            <th>Student Name</th>
                            <th>Absence</th>
                            <th>Parent</th>
                            <th>Gender</th>
                            <th>Classroom</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create/Edit Student -->
    <!-- Modal Create/Edit Student -->
    <div class="modal fade" id="studentModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Create New Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="studentForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="studentId" name="S_ID">

                        <!-- Student Name -->
                        <div class="mb-3">
                            <label for="studentName" class="form-label">Student Name</label>
                            <input type="text" id="studentName" name="STUDENT_NAME" class="form-control" placeholder="Enter student name" required>
                        </div>

                        <!-- Roll Number -->
                        <div class="mb-3">
                            <label for="rollNumber" class="form-label">Roll Number</label>
                            <input type="text" id="rollNumber" name="STUDENT_ROLL_NUMBER" class="form-control" placeholder="Enter roll number">
                        </div>

                        <!-- Parent Selection -->
                        <!-- Parent Selection -->
                        <div class="mb-3">
                            <label for="parent" class="form-label">Parent</label>
                            <select id="parent" name="STUDENT_PARENT_U_ID" class="form-control " required>
                                <option value="" disabled selected>Select Parent</option>
                                @foreach($unusedParents as $parent)
                                    <option value="{{ $parent->U_ID }}" {{ old('STUDENT_PARENT_U_ID') == $parent->U_ID ? 'selected' : '' }}>
                                        {{ $parent->U_NAME }}
                                    </option>
                                @endforeach

                                <!-- Then show used parents below, disabled -->
                                @foreach($usedParents as $parent)
                                    <option value="{{ $parent->U_ID }}" disabled>
                                        {{ $parent->U_NAME }} (Used)
                                    </option>
                                @endforeach
                            </select>
                        </div>



                        <!-- Gender -->
                        <div class="mb-3">
                            <label for="sex" class="form-label">Gender</label>
                            <select id="sex" name="STUDENT_SEX" class="form-control" required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="male" {{ old('STUDENT_SEX', $student->STUDENT_SEX ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('STUDENT_SEX', $student->STUDENT_SEX ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>

                        <!-- Classroom -->
                        <div class="mb-3">
                            <label for="classroom" class="form-label">Classroom</label>
                            <select id="classroom" name="CLSRM_ID" class="form-control " required>
                                <option value="" disabled selected>Select Classroom</option>
                                @foreach($classrooms as $classroom)
                                    <option value="{{ $classroom->CLSRM_ID }}" {{ old('CLSRM_ID', $student->CLSRM_ID ?? '') == $classroom->CLSRM_ID ? 'selected' : '' }}>
                                        {{ $classroom->CLSRM_NAME }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Profile Image -->
                        <div class="mb-3">
                            <label for="image" class="form-label">Profile Image</label>
                            <input type="file" id="image" name="STUDENT_IMAGE_PROFILE" class="form-control" accept="image/*" onchange="previewImage(event)">
                            <img id="imagePreview" src="#" alt="Image Preview" style="display:none;">
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
                aaSorting: [[0, 'asc']],
                iDisplayLength: 50,
                bLengthChange: true,
                bFilter: true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    url: "{{ url('backend/student/datatables') }}",
                    type: "POST",
                },
                columns: [
                    { data: "Student Name" },
                    { data: "Roll Number" },
                    { data: "Parent" },
                    { data: "Gender" },
                    { data: "Classroom" },
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

        function createData() {
            $('#modalTitle').text('Add Student');
            $('#studentForm')[0].reset();
            $('#studentId').val('');
            $('#imagePreview').hide(); // Hide the image preview
            $('#parent').prop('disabled', false); // Enable the  dropdown for creation
            $('#parentName').hide(); // Hide the parent name input field
            $('#studentModal').modal('show');
        }


        function editData(rowData) {
            $('#modalTitle').text('Edit Student');
            $('#studentId').val(rowData.S_ID);
            $('#studentName').val(rowData.STUDENT_NAME);
            $('#rollNumber').val(rowData.STUDENT_ROLL_NUMBER);
            // For editing: Show the parent's name in a disabled input field and hide the
            if (rowData.STUDENT_PARENT_U_ID) {
                $('#parent').val(rowData.STUDENT_PARENT_U_ID).trigger('change'); // Select the parent in the select dropdown
                $('#parent').prop('disabled', true); // Disable the  dropdown
            } else {
                $('#parent').prop('disabled', false); // Enable if no parent is assigned
            }

            $('#sex').val(rowData.STUDENT_SEX).trigger('change');
            $('#classroom').val(rowData.CLSRM_ID).trigger('change');
            $('#imagePreview').attr('src', rowData.STUDENT_IMAGE_PROFILE).show(); // Show the image preview
            $('#studentModal').modal('show');
        }




        $('#studentForm').on('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const studentId = $('#studentId').val();
            const url = studentId ? '{{ url("backend/student") }}/' + studentId + '/update' : '{{ url("backend/student/create") }}';

            $.ajax({
                url: url,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                processData: false,
                contentType: false,
                data: formData,
                success :
                function (data) {
                    if (data.STATUS === 'SUCCESS') {
                        toastr.success(data.MESSAGE);
                        $('#studentModal').modal('hide');
                        location.reload();
                        reloadDataTable();
                    } else {
                        toastr.error(data.MESSAGE);
                    }
                },
                error: function (xhr) {
                    const errors = xhr.responseJSON?.errors || ['Unknown error occurred'];
                    toastr.error(errors.join('<br>'));
                },
            });
        });

        function detailStudent(rowData){
            window.location.href = '/backend/student/' + rowData.S_ID;
        }

        $(document).on('click', '.delete-action', function() {
            const studentId = $(this).data('id');
            deleteData(studentId);
        });

        function deleteData(studentId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: '{{ url("backend/student") }}/' + studentId,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire('Deleted!', 'Your data has been deleted.', 'success');
                            reloadDataTable();
                        },
                        error: function (error) {
                            Swal.fire('Error!', 'There was an error deleting the data.', 'error');
                        }
                    });
                }
            });
        }

        function reloadDataTable() {
            var table = $('#dataTable').DataTable();
            $("#studentModal").modal("hide");
            table.ajax.reload();
        }

        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('imagePreview');
                output.src = reader.result;
                output.style.display = 'block'; // Show the image preview
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
