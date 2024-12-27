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
                <li class="breadcrumb-item active" aria-current="page">Users</li>
            </ol>
        </nav>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title">Manage Users</h5>
                    <button type="button" class="btn btn-primary" onclick="createData()">
                        <i class="fas fa-plus"></i> Add User
                    </button>
                </div>

                <div class="form-group mb-3">
                    <label for="filterRole" class="form-label">Filter Role</label>
                    <select class="select form-select" id="filterRole">
                        <option value="" disabled selected>-- Select Role --</option>
                        @foreach($groupedRole as $role)
                            <option value="{{ $role->UR_ID }}">{{ $role->ROLE_NAME }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable">
                        <thead class="table-light">
                        <tr>
                            <th>User Name</th>
                            <th>User Role</th>
                            <th>User Sex</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create/Edit User -->
    <div class="modal fade" id="userModal"  tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Create New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="userForm" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="username" class="form-label">User Name</label>
                            <input type="text" id="username" name="U_NAME" class="form-control" placeholder="Enter user name" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">User Role</label>
                            <select id="role" name="UR_ID" class="form-control select2" required>
                                <option value="" disabled selected>Select Role</option>
                                @foreach($groupedRole as $role)
                                    <option value="{{ $role->UR_ID }}">{{ $role->ROLE_NAME }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="sex" class="form-label">User Sex</label>
                            <select id="sex" name="U_SEX" class="form-control select2" required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="U_EMAIL" class="form-control" placeholder="Enter email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" id="phone" name="U_PHONE" class="form-control" placeholder="Enter phone number">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea id="address" name="U_ADDRESS" class="form-control" rows="3" placeholder="Enter address"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" id="image" name="U_IMAGE_PROFILE" class="form-control">
                            <div id="imagePreview"></div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" id="password" name="U_PASSWORD" class="form-control" required>
                                <button type="button" id="togglePassword" class="btn btn-outline-secondary">
                                    <i id="eyeIcon" class="fas fa-eye"></i>
                                </button>
                            </div>
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
        const USER_TOKEN = '{{ $token }}';
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
                    url: "{{ url('backend/users/datatables') }}",
                    type: "POST",
                    data: function (d) {
                        d.groupRole = $('#filterRole').val();
                    }
                },
                columns: [
                    { data: "User Name" },
                    { data: "User Role" },
                    { data: "User Sex" },
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
            $('#filterRole').on('change', function() {
                var table = $('#dataTable').DataTable();
                table.ajax.reload();
            });
            $('#image').on('change', function () {
                const file = this.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $('#imagePreview').html(`
                    <img src="${e.target.result}" alt="Image Preview"
                         class="img-thumbnail" style="max-width: 100%; height: auto;">
                `);
                    };
                    reader.readAsDataURL(file);
                } else {
                    $('#imagePreview').html('<p class="text-danger">Invalid image file.</p>');
                }
            });
        });

        //OPEN MODAL
        function createData() {
            $('#modalTitle').text('Add User');
            $('#userForm')[0].reset();
            $('#imagePreview').html('');
            $('#userModal').modal('show');
        }

        function closeModal() {
            $('#userModal').modal('hide');
        }

        function getBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = () => resolve(reader.result);
                reader.onerror = (error) => reject(error);
                reader.readAsDataURL(file);
            });
        }

        $('#userForm').on('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this); // Ambil semua data dari form

            $.ajax({
                url: '{{ url("backend/users/create") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                processData: false, // Jangan proses data
                contentType: false, // Jangan set contentType
                data: formData,
                success: function (data) {
                    if (data.STATUS === 'SUCCESS') {
                        toastr.success(data.MESSAGE);
                        $('#userModal').modal('hide');
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


        function editData(rowData){
            console.log(rowData);
        }

        $(document).on('click', '.delete-action', function() {
            const userId = $(this).data('id');
            deleteData(userId);
        });

        function deleteData(U_ID) {
            createOverlay("Processing...");
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
                    // Lakukan AJAX request untuk menghapus data
                    $.ajax({
                        type: "POST",
                        url: '/backend/users/' + U_ID + '/delete',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            gOverlay.hide();
                            Swal.fire(
                                'Deleted!',
                                'Your data has been deleted.',
                                'success'
                            );
                            // Reload DataTable
                         reloadDataTable()
                        },
                        error: function (error) {
                            gOverlay.hide();
                            Swal.fire(
                                'Error!',
                                'There was an error deleting the data.',
                                'error'
                            );
                        }
                    });
                }
            });
        }


        function createOverlay(message) {
            $('body').append(`
            <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-50 flex items-center justify-center">
                <div class="text-white bg-gray-800 p-4 rounded">
                    <div class="spinner-border text-light" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mt-2">${message}</div>
                </div>
            </div>
        `);
        }

        var gOverlay = {
            hide: function() {
                $('#overlay').remove();
            }
        };

        $('#generatePassword').on('click', function() {
            const password = Password.generate(16);
            $('#password').val(password);
        });

        var Password = {
            _pattern: /[a-zA-Z0-9_\-\+\.]/,
            _getRandomByte: function () {
                if (window.crypto && window.crypto.getRandomValues) {
                    var result = new Uint8Array(1);
                    window.crypto.getRandomValues(result);
                    return result[0];
                } else if (window.msCrypto && window.msCrypto.getRandomValues) {
                    var result = new Uint8Array(1);
                    window.msCrypto.getRandomValues(result);
                    return result[0];
                } else {
                    return Math.floor(Math.random() * 256);
                }
            },

            generate: function(length) {
                return Array.apply(null, { 'length': length })
                    .map(function() {
                        var result;
                        while (true) {
                            result = String.fromCharCode(this._getRandomByte());
                            if (this._pattern.test(result)) {
                                return result;
                            }
                        }
                    }, this)
                    .join('');
            }
        };

        $('#togglePassword').on('click', function() {
            const passwordField = $('#password');
            const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
            passwordField.attr('type', type);

            // Change eye icon
            const eyeIcon = $('#eyeIcon');
            if (type === 'text') {
                eyeIcon.html(`
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825c-1.5.825-3.75.825-5.25 0C4.5 17.325 3 15.375 3 12c0-3.375 1.5-5.325 4.125-6.825 1.5-.825 3.75-.825 5.25 0C19.5 6.675 21 8.625 21 12c0 3.375-1.5 5.325-4.125 6.825z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            `);
            } else {
                eyeIcon.html(`
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12s2-4 9-4 9 4 9 4-2 4-9 4-9-4-9-4z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            `);
            }
        });

        function reloadDataTable() {
            var table = $('#dataTable').DataTable();
            $("#userModal").modal("hide");
            gOverlay.hide();
            table.ajax.reload();
        }
</script>
@endsection
