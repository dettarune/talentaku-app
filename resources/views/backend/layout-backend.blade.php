<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>TALENTAKU</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-..." crossorigin="anonymous">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

    <!-- Bootstrap Bundle JS (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-..." crossorigin="anonymous"></script>

    <!-- Toastr JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{--    Validate--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
    {{--select2--}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <!-- Or for RTL support -->
    {{--    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />--}}
    {{--    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>--}}
    {{--    --}}

    <style>
        :root {
            --sidebar-width: 250px;
        }

        /*.sidebar {*/
        /*    width: var(--sidebar-width);*/
        /*    height: 100vh;*/
        /*    position: fixed;*/
        /*    left: 0;*/
        /*    top: 0;*/
        /*    padding-top: 0;*/
        /*    background: #15283c;*/
        /*    color: white;*/
        /*    transition: all 0.3s;*/
        /*    z-index: 1000;*/
        /*}*/
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px; /* Sesuaikan dengan lebar sidebar */
            height: 100vh;
            background: url('{{ asset("storage/uploads/images/pattern.png") }}') no-repeat center center fixed;
            background-size: cover;
            background-blend-mode: overlay;
            padding-top: 0;
            background: #15283c;
            color: white;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar a {
            color: white;
        }

        .sidebar a:hover {
            color: #ccc;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: calc(100vh - 60px);
        }

        .sidebar-header {
            padding: 20px;
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar-nav {
            padding: 0;
        }

        .sidebar-nav .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 15px 20px;
            transition: all 0.3s;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-nav .nav-link i {
            width: 25px;
        }

        .topbar {
            height: 60px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
            margin-left: var(--sidebar-width);
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .user-dropdown .dropdown-toggle::after {
            display: none;
        }

        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0,0,0,.1);
            border-radius: 10px;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0,0,0,.1);
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(var(--sidebar-width) * -1);
            }

            .sidebar.active {
                margin-left: 0;
            }

            .main-content, .topbar {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
<!-- Sidebar -->
<nav class="sidebar">
    <div class="sidebar-header">
        <a href="/" class="text-decoration-none text-white">
            <img src="{{URL::asset('storage/uploads/images/logo-talentaku.png')}}" alt="Logo Perusahaan" class="login-logo">
            <text x="50" y="90" font-size="90" fill="#ffffff" font-family="Arial, sans-serif" font-weight="bold">TALENTAKU</text>
        </a>
    </div>

    <ul class="sidebar-nav nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="/">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/backend/users">
                <i class="fas fa-users"></i>
                Users
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/backend/classroom">
                <i class="fas fa-chalkboard"></i>
                Classrooms
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/backend/student">
                <i class="fas fa-user-graduate"></i>
                Students
            </a>
        </li>
    </ul>
</nav>

<!-- Top Navigation -->
<div class="topbar">
    <div class="dropdown user-dropdown">
        <button class="btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-user-circle me-2"></i>
            @if($profileName != null || $profileName != '')
                <span id="AdminName">{{$profileName}}</span>
            @else
                <span id="AdminName">Admin Name</span>
            @endif
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a class="dropdown-item text-danger" href="#" onclick="logout()"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
        </ul>
    </div>
</div>


<!-- Main Content -->
<div class="main-content">
    @yield('content')
</div>



<!-- Core JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>

<!-- Core JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/toastr.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>

<script>
    // Logout function
    function logout() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out of the system",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, logout'
        }).then((result) => {
            if (result.isConfirmed) {
                // Send logout request to server
                $.ajax({
                    url: '/logout',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        window.location.href = '/backend/auth';
                    },
                    error: function() {
                        Swal.fire(
                            'Error',
                            'There was an error logging out',
                            'error'
                        );
                    }
                });
            }
        });
    }

    // Toggle sidebar on mobile
    $(document).ready(function() {
        $('.navbar-toggler').on('click', function() {
            $('.sidebar').toggleClass('active');
        });

        // Highlight active nav item
        const currentPath = window.location.pathname;
        $(`.sidebar-nav .nav-link[href="${currentPath}"]`).addClass('active');
    });
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
</script>

<script>
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    $(document).ready(function() {
        $('.select2').select2({
            theme: "bootstrap-5", // Gunakan tema bootstrap
            width: '100%' // Pastikan dropdown menyesuaikan lebar container
        });
    });
    // $(document).ready(function() {
    //     $('.select2').select2();
    // });
</script>

</body>
</html>
