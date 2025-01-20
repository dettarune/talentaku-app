@extends('backend.layout-backend')

@section('content')
    <style>
        .student-profile-header {
            background: linear-gradient(135deg, #15283c 0%, #071222 100%);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-image-container {
            position: relative;
            width: 200px;
            height: 200px;
            margin: 0 auto;
        }

        .profile-image {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .profile-image:hover {
            transform: scale(1.05);
        }

        .info-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            height: 100%;
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .info-label {
            color: #666;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .info-value {
            color: #333;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .badge-custom {
            padding: 0.5em 1em;
            border-radius: 20px;
            font-weight: 500;
        }

        .timestamp {
            font-size: 0.875rem;
            color: #666;
            font-style: italic;
        }

        /*detail report*/
        /* Custom styles for the report modal */
        .modal-content {
            border-radius: 1rem;
            border: none;
        }

        .modal-header {
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
        }

        .activity-card {
            transition: transform 0.2s;
        }

        .activity-card:hover {
            transform: translateY(-2px);
        }

        .activity-card .card-header {
            border-top-left-radius: 0.5rem !important;
            border-top-right-radius: 0.5rem !important;
        }

        .table td {
            padding: 0.75rem 0;
            vertical-align: middle;
        }

        /* Status colors with hover effect */
        .text-success, .text-warning, .text-danger {
            padding: 0.3rem 0.6rem;
            border-radius: 0.5rem;
            transition: background-color 0.2s;
        }

        .text-success {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .text-warning {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .text-danger {
            background-color: rgba(220, 53, 69, 0.1);
        }

        /* Add some animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal.show .modal-dialog {
            animation: fadeIn 0.3s ease-out;
        }

        .bg-title{
            background: #ff5722;
        }
        .text-title{
            color: #214162;
        }
    </style>
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">
                <!-- Student Profile Header -->
                <div class="student-profile-header text-center text-white mb-4">
                    <div class="profile-image-container mb-4">
                        <img src=" {{URL::asset('storage/'.$student->STUDENT_IMAGE_PROFILE)}}"

                             alt="{{ $student->STUDENT_NAME }}"
                             class="profile-image"
                             onerror="this.src='https://via.placeholder.com/200x200?text=No+Image'">
                    </div>
                    <h1 class="display-4 fw-bold mb-2">{{ $student->STUDENT_NAME }}</h1>
                    <p class="lead mb-0" id="studentId">Student ID: {{ $student->S_ID }}</p>
                </div>

                <!-- Student Information Grid -->
                <div class="row g-4">
                    <!-- Basic Information -->
                    <div class="col-12 col-md-6">
                        <div class="info-card">
                            <h3 class="card-title mb-4"><i class="fas fa-user-graduate me-2"></i>Basic Information</h3>
                            <div class="mb-3">
                                <div class="info-label">Roll Number</div>
                                <div class="info-value">{{ $student->STUDENT_ROLL_NUMBER }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="info-label">Gender</div>
                                <div class="info-value">
                                <span class="badge badge-custom {{ $student->STUDENT_SEX == 'male' ? 'bg-primary' : 'bg-danger' }}">
                                    <i class="fas fa-{{ $student->STUDENT_SEX == 'male' ? 'mars' : 'venus' }} me-1"></i>
                                    {{ ucfirst($student->STUDENT_SEX) }}
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Class Information -->
                    <div class="col-12 col-md-6">
                        <div class="info-card">
                            <h3 class="card-title mb-4"><i class="fas fa-chalkboard-teacher me-2"></i>Class Information</h3>
                            <div class="mb-3">
                                <div class="info-label">Classroom</div>
                                <div class="info-value">{{ $student->classroom->CLSRM_NAME }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="info-label">Grade & Type</div>
                                <div class="info-value">
                                    <span class="badge bg-success badge-custom">Grade {{ $student->classroom->CLSRM_GRADE }}</span>
                                    <span class="badge bg-info badge-custom ms-2">{{ $student->classroom->CLSRM_TYPE }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Parent Information -->
                    <div class="col-12 col-md-6">
                        <div class="info-card">
                            <h3 class="card-title text-xl font-bold text-gray-800 mb-4">
                                <i class="fas fa-users me-2 text-blue-600"></i> Parent Information
                            </h3>
                            <div class="mb-4">
                                <div class="info-label text-sm font-semibold text-gray-600 mb-1">
                                    <i class="fas fa-id-card me-2 text-green-600"></i> Parent Name
                                </div>
                                <div class="info-value text-lg font-medium text-gray-800 mb-2">{{ $student->parent->U_NAME }}</div>
                            </div>
                            <div class="mb-4">
                                <div class="info-label text-sm font-semibold text-gray-600 mb-1">
                                    <i class="fas fa-envelope me-2 text-indigo-600"></i> Email
                                </div>
                                <div class="info-value text-lg font-medium text-gray-800 mb-2">{{ $student->parent->U_EMAIL }}</div>
                            </div>
                            <div class="mb-4">
                                <div class="info-label text-sm font-semibold text-gray-600 mb-1">
                                    <i class="fas fa-map-marker-alt me-2 text-orange-600"></i> Address
                                </div>
                                <div class="info-value text-lg font-medium text-gray-800 mb-2">{{ $student->parent->U_ADDRESS }}</div>
                            </div>
                            <div class="mb-4">
                                <div class="info-label text-sm font-semibold text-gray-600 mb-1">
                                    <i class="fas fa-phone me-2 text-teal-600"></i> Phone
                                </div>
                                <div class="info-value text-lg font-medium text-gray-800 mb-2">{{ $student->parent->U_PHONE }}</div>
                            </div>
                        </div>
                    </div>




                    <!-- System Information -->
                    <div class="col-12 col-md-6">
                        <div class="info-card">
                            <h3 class="card-title mb-4"><i class="fas fa-clock me-2"></i>System Information</h3>
                            <div class="mb-3">
                                <div class="info-label">Created At</div>
                                <div class="info-value timestamp">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    {{ \Carbon\Carbon::parse($student->SYS_CREATE_AT)->format('d F Y, H:i') }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="info-label">Last Updated</div>
                                <div class="info-value timestamp">
                                    <i class="far fa-clock me-1"></i>
                                    {{ \Carbon\Carbon::parse($student->SYS_UPDATE_AT)->format('d F Y, H:i') }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="info-label">Active </div>
                                <div class="info-value timestamp flex items-center space-x-2">
                                    @if(is_null($student->deleted_at))
                                        <!-- Status: Active (Green) -->
                                        <span class="text-green-600 font-semibold">
                                            <i class="fas fa-check-circle me-1"></i> Active
{{--                                        </span>--}}
{{--                                            <span class="text-sm text-gray-500">--}}
{{--                                            <i class="far fa-clock me-1"></i>--}}
{{--                                            {{ \Carbon\Carbon::parse($student->SYS_CREATE_AT)->format('d F Y, H:i') }}--}}
{{--                                        </span>--}}
                                            @else
                                        <!-- Status: Inactive (Red) -->
                                        <span class="text-red-600 font-semibold">
                                            <i class="fas fa-times-circle me-1"></i> Inactive
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            <i class="far fa-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($student->deleted_at)->format('d F Y, H:i') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

{{--                    <div class="row mb-4">--}}
{{--                        <div class="col-12 col-md-4">--}}
{{--                            <label for="filterYearMonth" class="form-label">Filter by Year and Month</label>--}}
{{--                            <input type="month" id="filterYearMonth" class="form-control">--}}
{{--                        </div>--}}
{{--                    </div>--}}
                    <table id="dataTable" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                        <tr>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Teacher Name</th>
                            <th>Report Date</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- DataTable akan mengisi baris ini dengan data -->
                        </tbody>
                    </table>

                    <!-- Action Buttons -->
                    <div class="col-12 text-center mt-4">
                        <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                        <button type="button" class="btn btn-primary me-2" onclick="editStudentData('{{ json_encode($student) }}')">
                            <i class="fas fa-edit me-1"></i> Edit Student
                        </button>
                        @if($student->deleted_at)
                            <!-- Tombol Restore jika student sudah di-soft delete -->
                            <button type="button" class="btn btn-success" onclick="confirmRestore('{{ $student->S_ID }}')">
                                <i class="fas fa-undo me-1"></i> Restore Student
                            </button>
                        @else
                            <!-- Tombol Suspend jika student belum di-soft delete -->
                            <button type="button" class="btn btn-danger" onclick="confirmDelete('{{ $student->S_ID }}')">
                                <i class="fas fa-trash-alt me-1"></i> Suspend Student
                            </button>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Student -->
    <div class="modal fade" id="editStudentModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editStudentForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" id="studentId" name="S_ID"> <!-- Hidden field for editing -->

                        <!-- Student Name -->
                        <div class="mb-3">
                            <label for="studentName" class="form-label">Student Name</label>
                            <input type="text" id="studentName" name="STUDENT_NAME" class="form-control" placeholder="Enter student name" >
                        </div>

                        <!-- Roll Number -->
                        <div class="mb-3">
                            <label for="rollNumber" class="form-label">Roll Number</label>
                            <input type="text" id="rollNumber" name="STUDENT_ROLL_NUMBER" class="form-control" placeholder="Enter roll number">
                        </div>


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
                            <select id="sex" name="STUDENT_SEX" class="form-control" >
                                <option value="" disabled selected>Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>

                        <!-- Classroom -->

                        <div class="mb-3">
                            <label for="classroom" class="form-label">Classroom</label>
                            <select id="classroom" name="CLSRM_ID" class="form-control " >
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
                        <button type="submit" class="btn btn-primary" >Edit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        const USER_TOKEN = '{{ $token }}'; // Gunakan token yang sesuai jika diperlukan
        const s_id = '{{ $student->S_ID  }}';

        document.addEventListener('DOMContentLoaded', () => {
            $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [[0, 'asc']], // Urutkan berdasarkan kolom pertama
                iDisplayLength: 50,
                bLengthChange: true,
                bFilter: true,
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    },
                    url: "{{ url('student-report/datatables') }}", // Sesuaikan dengan rute Anda
                    type: "POST",
                    data: function (d) {
                        d.student_id = s_id;
                        d.date = $('#filterDate').val();
                    }
                },
                columns: [
                    { data: "SR_TITLE" },
                    { data: "SR_CONTENT" },
                    { data: "TEACHER_NAME" },
                    { data: "FORMATTED_DATE" },
                    { data: "Action" },
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

            // Reload data when the filter is changed
            $('#filterRole').on('change', function() {
                var table = $('#dataTable').DataTable();
                table.ajax.reload();
            });
            $('#filterYearMonth').on('change', function() {
                var table = $('#dataTable').DataTable();
                table.ajax.reload();
            });
        });

        $(function () {
            $('#date').datetimepicker({
                viewMode: 'years'
            });
        });

        function editStudentData(rowData) {
            // Pastikan rowData sudah berupa objek JavaScript
            if (typeof rowData === 'string') {
                rowData = JSON.parse(rowData); // Jika dalam bentuk string, parse ke objek
                console.log(rowData)
            }

            $('#modalTitle').text('Edit Student');

            // Set the form fields with the rowData values
            $('#studentId').val(rowData.S_ID).data('original-value', rowData.S_ID);
            $('#studentName').val(rowData.STUDENT_NAME).data('original-value', rowData.STUDENT_NAME);
            $('#rollNumber').val(rowData.STUDENT_ROLL_NUMBER).data('original-value', rowData.STUDENT_ROLL_NUMBER);
            $('#parent').val(rowData.STUDENT_PARENT_U_ID).data('original-value', rowData.STUDENT_PARENT_U_ID);
            $('#sex').val(rowData.STUDENT_SEX).data('original-value', rowData.STUDENT_SEX);
            $('#classroom').val(rowData.CLSRM_ID).data('original-value', rowData.CLSRM_ID);

            // Display the student image if available
            if (rowData.STUDENT_IMAGE_PROFILE) {
                $('#imagePreview').attr('src', '{{ URL::asset('storage/') }}' + '/' + rowData.STUDENT_IMAGE_PROFILE).show();
            } else {
                $('#imagePreview').hide();
            }

            // Show the modal
            $('#editStudentModal').modal('show');
        }


        $('#editStudentForm').on('submit', function (e) {
            e.preventDefault();

            let hasChanges = false;
            const formData = new FormData();
            const id = $('#studentId').val();
            const url = '{{ url("backend/student") }}/' + id + '/update';

            // Loop through all input and select elements
            $('#editStudentForm input, #editStudentForm select').each(function () {
                const originalValue = $(this).data('original-value');
                const currentValue = $(this).val();
                console.log('lala ' + originalValue + 'lala ' + currentValue)

                // Handle select inputs that need to be converted to integer
                if ($(this).attr('name') === 'STUDENT_PARENT_U_ID' || $(this).attr('name') === 'CLSRM_ID') {
                    const intValue = parseInt(currentValue, 10);  // Convert to integer
                    if (!isNaN(intValue) && intValue !== parseInt(originalValue, 10)) {
                        hasChanges = true;
                        formData.append($(this).attr('name'), intValue); // Append integer value
                    }
                } else if (currentValue !== originalValue) {
                    hasChanges = true;
                    formData.append($(this).attr('name'), currentValue); // Append value
                }
            });

            // Handle image file input
            const imageFile = $('#image')[0].files[0];
            if (imageFile) {
                hasChanges = true;
                formData.append('STUDENT_IMAGE_PROFILE', imageFile); // Append image file
            }

            // If no changes, notify and return
            if (!hasChanges) {
                toastr.info('No changes to save.');
                return;
            }

            // Log FormData content for debugging
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }

            // Make the request to update data
            createOrUpdateData(formData, url, 'editStudentModal');
        });



        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function () {
                $('#imagePreview').attr('src', reader.result).show();
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function createOrUpdateData(formData, url, modalId) {
            createOverlay('Processing...'); // Show overlay while processing
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
                    if (data.STATUS === 'SUCCESS') {
                        toastr.success(data.MESSAGE);
                        gOverlay.hide();
                        $('#' + modalId).modal('hide');
                        $('#dataTable').DataTable().ajax.reload();
                        location.reload();
                    } else {
                        toastr.error(data.MESSAGE);
                        gOverlay.hide();
                    }
                },
                error: function (error) {
                    gOverlay.hide();
                    toastr.error('Network or server error: ' + error);
                },
            });
        }

        function detailStudentReport(rowdata) {
           id = rowdata.SR_ID
            // Show loading overlay
            createOverlay('Loading...');

            $.ajax({
                url: "{{ url('/api/student-report/') }}"+ '/'+id,
                method: 'GET',
                headers: {
                    'Talentaku-token': USER_TOKEN
                },
                success: function(response) {
                    if (response.STATUS === 'SUCCESS') {
                        const data = response.PAYLOAD;

                        // Convert links in content to clickable links
                        const content = data.SR_CONTENT.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" class="text-title">$1</a>');

                        // Prepare the modal content
                        let modalContent = `
                    <div class="modal fade" id="detailReportModal" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header bg-title text-white">
                                    <h5 class="modal-title">
                                        <i class="fas fa-file-alt me-2"></i> ${data.SR_TITLE}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="card mb-3 border-0 shadow-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div>
                                                    <h6 class="text-title mb-1">
                                                        <i class="fas fa-calendar me-2"></i>Report Date
                                                    </h6>
                                                    <p class="mb-0">${data.FORMATTED_DATE}</p>
                                                </div>
                                                <button onclick="deleteReport(${data.SR_ID})" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash-alt me-1"></i> Delete Report
                                                </button>
                                            </div>

                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <h6 class="text-title mb-1">
                                                        <i class="fas fa-user-graduate me-2"></i>Student
                                                    </h6>
                                                    <p class="mb-0">${data.STUDENT.STUDENT_NAME}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-title mb-1">
                                                        <i class="fas fa-users me-2"></i>Parent
                                                    </h6>
                                                    <p class="mb-0">${data.STUDENT.PARENT.STUDENT_PARENT_NAME}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6 class="text-title mb-1">
                                                        <i class="fas fa-chalkboard-teacher me-2"></i>Teacher
                                                    </h6>
                                                    <p class="mb-0">${data.TEACHER.TEACHER_NAME}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card mb-3 border-0 shadow-sm">
                                        <div class="card-body">
                                            <h6 class="text-title mb-3">
                                                <i class="fas fa-clipboard-list me-2"></i>Report Content
                                            </h6>
                                            <p class="mb-0">${content}</p>
                                        </div>
                                    </div>

                                    <div class="activities-container">`;

                        data.ACTIVITIES.forEach(activity => {
                            modalContent += `
                        <div class="card mb-3 border-0 shadow-sm activity-card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0 text-title">
                                    <i class="fas fa-star me-2"></i>${activity.ACTIVITY_NAME}
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tbody>`;

                            activity.REF_ACTIVITIES.forEach(ref => {
                                let statusClass = '';
                                let statusIcon = '';
                                let activityTypeHtml = '';  // Variable to hold activity type HTML

                                // Check if ACTIVITY_TYPE is not null or empty
                                if (ref.ACTIVITY_TYPE) {
                                    activityTypeHtml = `
                                <div class="activity-type-header">
                                    <span class="badge bg-info p-2 text-white rounded-3">${ref.ACTIVITY_TYPE}</span>
                                </div>`;
                                }
                                switch(ref.STATUS) {
                                    case 'MUNCUL':
                                        statusClass = 'text-success';
                                        statusIcon = 'check-circle';
                                        badgeClass = 'badge bg-success';
                                        break;
                                    case 'KURANG':
                                        statusClass = 'text-warning';
                                        statusIcon = 'exclamation-circle';
                                        badgeClass = 'badge bg-warning';
                                        break;
                                    case 'BELUM MUNCUL':
                                        statusClass = 'text-danger';
                                        statusIcon = 'times-circle';
                                        badgeClass = 'badge bg-danger';
                                        break;
                                }


                                modalContent += `
                            <tr>
                                <td>
                                    ${activityTypeHtml}
                                    <i class="fas fa-angle-right text-primary me-2"></i>
                                    ${ref.ACTIVITY_NAME}

                                </td>
                                <td class="text-end">
                                    <span class="${badgeClass}"><i class="fas fa-${statusIcon} me-1"></i>${ref.STATUS}</span>
                                </td>
                            </tr>`;
                            });

                            modalContent += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>`;
                        });

                        modalContent += `
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;

                        // Remove existing modal if any
                        $('#detailReportModal').remove();

                        // Add the new modal to the body
                        $('body').append(modalContent);

                        // Show the modal
                        $('#detailReportModal').modal('show');
                    } else {
                        toastr.error('Failed to load report details');
                    }
                    gOverlay.hide();
                },
                error: function() {
                    toastr.error('Error loading report details');
                    gOverlay.hide();
                }
            });
        }

        // Delete report function
        function deleteReport(reportId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This report will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                createOverlay('Loading...');
                if (result.isConfirmed) {
                    // Add your delete API call here
                    $.ajax({
                        url: `/backend/student/${reportId}/delete/report`,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            console.log('status '+response.STATUS)
                            if (response.STATUS === 'SUCCESS') {
                                $('#detailReportModal').modal('hide');
                                gOverlay.hide();
                                location.reload();
                                toastr.success('Report deleted successfully');

                            } else {
                                gOverlay.hide();
                                toastr.error('Failed to delete report');
                            }
                        },
                        error: function() {
                            gOverlay.hide();
                            toastr.error('Error deleting report');
                        }
                    });
                }
            });
        }

        function confirmDelete(studentId) {
            console.log(studentId)
            Swal.fire({
                title: 'Are you sure?',
                text: "You will disable this student!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                createOverlay('Loading...');
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/backend/student/${studentId}/delete`,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            gOverlay.hide();
                            if (response.STATUS === 'SUCCESS') {
                                // location.reload();
                                toastr.success('Student disable successfully');
                                // Refresh your data table or list here
                            } else {
                                gOverlay.hide();
                                toastr.error('Failed to disable student');
                            }
                        },
                        error: function() {
                            gOverlay.hide();
                            toastr.error('Error disable student');
                        }
                    });
                }
            });
        }
        function confirmRestore(studentId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You will restore this student!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, restore it!'
            }).then((result) => {
                createOverlay('Loading...');
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/backend/student/${studentId}/restore`,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            gOverlay.hide();
                            if (response.STATUS === 'SUCCESS') {
                                location.reload();
                                toastr.success('Student restored successfully');
                                // Refresh your data table or list here
                            } else {
                                gOverlay.hide();
                                toastr.error('Failed to restore student');
                            }
                        },
                        error: function() {
                            gOverlay.hide();
                            toastr.error('Error restoring student');
                        }
                    });
                }
            });
        }

    </script>
@stop
