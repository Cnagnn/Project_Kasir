@php
    use Carbon\Carbon;
    $time = "";
    if(Carbon::now()->format('H') >= '04' && Carbon::now()->format('H') < '11'){
      $time = "Morning";
    }
    else if (Carbon::now()->format('H') >= '11' && Carbon::now()->format('H') < '15') {
      $time = "Afternoon";
    }
    else if (Carbon::now()->format('H') >= '15' && Carbon::now()->format('H') < '18') {
      $time = "Evening";
    }
    else {
      $time = "Night";
    }
    // dd(Carbon::now()->format('H'));
@endphp

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>@yield('page-title', 'Kasir') - Toko Kasir</title>
    <!--  plugins:css -->
    <link rel="stylesheet" href="{{ asset('admin/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/typicons/typicons.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/simple-line-icons/css/simple-line-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/vendors/css/vendor.bundle.base.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('admin/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}"> --}}
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Flatpickr Date Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('admin/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/js/select.dataTables.min.css') }}">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- endinject -->
    <style>
        /* Alignment antara navbar dan content */
        .content-wrapper {
            padding-left: 2.187rem !important;
            padding-right: 2.187rem !important;
        }
        
        /* Hilangkan padding dari col-lg-12 agar card sejajar dengan navbar */
        .content-wrapper .row > .col-sm-12 > .col-lg-12 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        
        .navbar {
            align-items: center !important;
            padding-top: 0 !important;
        }
        .navbar-menu-wrapper {
            align-items: center !important;
            padding-top: 0 !important;
            padding-left: 2.187rem !important;
            padding-right: 2.187rem !important;
        }

        /* ===== UNIFORM MODAL STYLES ===== */
        .modal-content {
            border-radius: 0.75rem;
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            background: #1F3BB3;
            color: #fff;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            border-bottom: none;
            padding: 1.25rem 1.5rem;
        }

        .modal-header .modal-title {
            font-weight: 600;
            font-size: 1.125rem;
            margin: 0;
        }

        .modal-header .close,
        .modal-header .modal-close-btn {
            color: #fff;
            opacity: 0.9;
            text-shadow: none;
            font-size: 1.5rem;
            font-weight: 300;
            padding: 0;
            margin: 0;
            background: transparent;
            border: none;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .modal-header .close:hover,
        .modal-header .modal-close-btn:hover {
            opacity: 1;
            background: rgba(255, 255, 255, 0.15);
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-body .card-description {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e9ecef;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #e9ecef;
            background-color: #f8f9fa;
            border-bottom-left-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .modal-footer .btn,
        .modal-footer .button {
            min-width: 100px;
            font-weight: 500;
            padding: 0.5rem 1.25rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            margin: 0;
        }

        .modal-footer .btn-light {
            background-color: #dc3545;
            border: 1px solid #dc3545;
            color: #ffffff;
            font-weight: 500;
        }

        .modal-footer .btn-light:hover {
            background-color: #c82333;
            border-color: #bd2130;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .modal-footer .btn-secondary {
            background-color: #dc3545;
            border: 1px solid #dc3545;
            color: #ffffff;
            font-weight: 500;
        }

        .modal-footer .btn-secondary:hover {
            background-color: #c82333;
            border-color: #bd2130;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }

        .modal-footer .btn-primary,
        .modal-footer .button.btn-primary {
            background: #1F3BB3;
            border: none;
            box-shadow: 0 4px 12px rgba(31, 59, 179, 0.3);
            color: #fff;
        }

        .modal-footer .btn-primary:hover,
        .modal-footer .button.btn-primary:hover {
            background: #172d88;
            box-shadow: 0 6px 16px rgba(31, 59, 179, 0.4);
            transform: translateY(-1px);
        }

        .modal-footer .button.btn-primary span {
            color: #fff;
        }

        /* Form Controls in Modal */
        .modal-body .form-group {
            margin-bottom: 1.25rem;
        }

        .modal-body .form-group label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .modal-body .form-control {
            border-radius: 0.5rem;
            border: 1px solid #dee2e6;
            padding: 0.625rem 0.875rem;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
        }

        .modal-body .form-control:focus {
            border-color: #1F3BB3;
            box-shadow: 0 0 0 0.2rem rgba(31, 59, 179, 0.15);
        }

        .modal-body .form-control.is-invalid {
            border-color: #dc3545;
        }

        .modal-body .form-control.is-invalid:focus {
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
        }

        .modal-body .invalid-feedback {
            font-size: 0.8125rem;
            margin-top: 0.375rem;
        }

        .modal-body .form-text {
            font-size: 0.8125rem;
            color: #6c757d;
            margin-top: 0.375rem;
        }

        /* Dropdown in Modal */
        .modal-body .btn-group .btn {
            border-radius: 0.5rem;
            padding: 0.625rem 0.875rem;
        }

        .modal-body .dropdown-menu {
            border-radius: 0.5rem;
            border: 1px solid #e9ecef;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .modal-body .dropdown-item {
            padding: 0.5rem 1rem;
            font-size: 0.9375rem;
            transition: all 0.2s ease;
        }

        .modal-body .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #1F3BB3;
        }

        /* Material Form Style (for existing forms) */
        .modal-body .material-form .form-group {
            position: relative;
            margin-bottom: 1.75rem;
        }

        .modal-body .material-form .form-control {
            border: none;
            border-bottom: 2px solid #dee2e6;
            border-radius: 0;
            padding: 0.5rem 0;
            background: transparent;
        }

        .modal-body .material-form .form-control:focus {
            box-shadow: none;
            border-bottom-color: #1F3BB3;
        }

        .modal-body .material-form .control-label {
            position: absolute;
            top: 0.5rem;
            left: 0;
            color: #999;
            transition: all 0.2s ease;
            pointer-events: none;
            font-size: 0.9375rem;
        }

        .modal-body .material-form .form-control:focus ~ .control-label,
        .modal-body .material-form .form-control:valid ~ .control-label {
            top: -1.25rem;
            font-size: 0.75rem;
            color: #1F3BB3;
        }

        .modal-body .material-form .bar {
            position: relative;
            display: block;
            width: 100%;
        }

        .modal-body .material-form .bar:before,
        .modal-body .material-form .bar:after {
            content: '';
            height: 2px;
            width: 0;
            bottom: 0;
            position: absolute;
            background: #1F3BB3;
            transition: all 0.2s ease;
        }

        .modal-body .material-form .bar:before {
            left: 50%;
        }

        .modal-body .material-form .bar:after {
            right: 50%;
        }

        .modal-body .material-form .form-control:focus ~ .bar:before,
        .modal-body .material-form .form-control:focus ~ .bar:after {
            width: 50%;
        }

        /* Modal Animation */
        .modal.fade .modal-dialog {
            transform: scale(0.9) translateY(-20px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .modal.show .modal-dialog {
            transform: scale(1) translateY(0);
            opacity: 1;
        }

        /* Modal Backdrop */
        .modal-backdrop.show {
            opacity: 0.6;
        }
    </style>
    <link rel="shortcut icon" href="{{ asset('admin/images/favicon.png') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
  </head>
  <body class="with-welcome-text">
    <div class="container-scroller">
      <!-- partial:partials/_navbar.html -->
      <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
          <div class="me-3">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
              <span class="icon-menu"></span>
            </button>
          </div>
          <div>
            <a class="navbar-brand brand-logo">
              <img src="{{ asset('admin/images/logo.svg') }}" alt="logo" />
            </a>
          </div>
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-stretch justify-content-between mt-3">
          <div>
            <h4 class="mb-1">@yield('page-title', 'Dashboard Kasir')</h4>
            <p class="text-muted mb-0 small">@yield('page-description', 'Ringkasan singkat operasional hari ini.')</p>
          </div>
          <div class="text-end d-flex align-items-center gap-3">
            <div>
              <h4 class="mb-1">Good {{ $time }}, <span class="text-black fw-bold">{{ Auth::user()->name }}</span></h4>
              <p class="text-muted mb-0 small">{{ Auth::user()->role->name }}</p>
            </div>
            <form action="{{ route('logout') }}" method="POST" id="logout-form" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-link text-danger p-0" title="Logout" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                <i class="mdi mdi-logout" style="font-size: 1.3rem;"></i>
              </button>
            </form>
          </div>
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
          </button>
        </div>
      </nav>
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        @include('layouts.partials.sidebar')
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            <!-- Page Header -->
            @hasSection('page-header')
            <div class="row">
              <div class="col-sm-12">
                <div class="home-tab">
                  <div class="d-sm-flex align-items-center justify-content-between border-bottom pb-2 mb-4">
                    <div>
                      <h4 class="card-title mb-1">@yield('page-title', 'Dashboard')</h4>
                      <p class="card-description mb-0 text-muted">@yield('page-description', 'Selamat datang di sistem kasir')</p>
                    </div>
                    <div>
                      <div class="btn-wrapper">
                        @yield('page-actions')
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            @endif
            <!-- End Page Header -->
            
            @yield('content')
          </div>
          <!-- content-wrapper ends -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="{{ asset('admin/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('admin/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ asset('admin/vendors/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('admin/vendors/progressbar.js/progressbar.min.js') }}"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('admin/js/off-canvas.js') }}"></script>
    <script src="{{ asset('admin/js/template.js') }}"></script>
    {{-- <script src="{{ asset('admin/js/settings.js') }}"></script> --}}
    <script src="{{ asset('admin/js/hoverable-collapse.js') }}"></script>
    {{-- <script src="{{ asset('admin/js/todolist.js') }}"></script> --}}
    <!-- Flatpickr Date Picker -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Initialize Flatpickr for all date inputs
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr('input[type="date"]', {
                dateFormat: 'Y-m-d', // Format untuk value yang dikirim ke server (yyyy-mm-dd)
                altInput: true, // Gunakan input alternatif untuk display
                altFormat: 'd/m/Y', // Format yang ditampilkan ke user (dd/mm/yyyy)
                allowInput: true,
                locale: {
                    firstDayOfWeek: 1,
                    weekdays: {
                        shorthand: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                        longhand: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
                    },
                    months: {
                        shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Oct', 'Nov', 'Des'],
                        longhand: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
                    }
                }
            });
        });
    </script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    @stack('scripts')
    <script src="{{ asset('admin/js/jquery.cookie.js') }}" type="text/javascript"></script>
    {{-- <script src="{{ asset('admin/js/dashboard.js') }}"></script> --}}
    
    <!-- <script src="assets/js/Chart.roundedBarCharts.js"></script> -->
    <!-- End custom js for this page-->
  
  </body>
</html>