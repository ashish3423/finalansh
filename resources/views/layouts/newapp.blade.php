
<!DOCTYPE html>
<html lang="en" data-coreui-theme="light" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Employee Management')</title>

    <link rel="stylesheet"
          href="{{ asset('vendors/simplebar/css/simplebar.css') }}">

    <!-- <link rel="stylesheet"
          href="{{ asset('css/vendors/simplebar.css') }}"> -->

    <link rel="stylesheet"
          href="{{ asset('css/style.css') }}">

<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  
<link rel="stylesheet"
      href="{{ asset('css/employee-management.css') }}">
<link rel="stylesheet" href="{{ asset('css/enterprise-portal.css') }}?v=20261117-1">


    <!-- <script src="{{ asset('js/config.js') }}"></script> -->
    <script src="{{ asset('js/color-modes.js') }}?v=20261117-1"></script>
</head>

<body>
    @include('layouts.sidebar')

    <div class="wrapper d-flex flex-column min-vh-100">

        @include('layouts.header')

        <div class="body flex-grow-1">
            <div class="container-lg px-4 mw-100">
                @yield('content')
            </div>
        </div>

        @include('layouts.footer')

    </div>

    @include('enterprise.partials.vehicle-expiry-reminders')

     <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('vendors/@coreui/coreui/js/coreui.bundle.min.js') }}"></script>
    <script src="{{ asset('vendors/simplebar/js/simplebar.min.js') }}"></script>

    @if ($vehicleExpiryReminders->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const reminderModal = document.getElementById('vehicleExpiryReminderModal');
                if (reminderModal) bootstrap.Modal.getOrCreateInstance(reminderModal).show();
            });
        </script>
    @endif

    @yield('scripts')

    <div id="listingLoadOverlay" class="listing-load-overlay" role="status" aria-live="polite" aria-label="Loading records" hidden>
        <div class="listing-load-card">
            <span class="spinner-border text-primary" aria-hidden="true"></span>
            <span>Loading records…</span>
        </div>
    </div>

    <script>
        (() => {
            const overlay = document.getElementById('listingLoadOverlay');
            if (!overlay) return;

            const showLoader = () => {
                overlay.hidden = false;
                document.body.classList.add('listing-is-loading');
            };

            document.addEventListener('submit', event => {
                const form = event.target;
                if (!form.matches('form.filter-bar')) return;
                if (event.submitter?.id === 'salaryExportButton') return;
                showLoader();
            });

            document.addEventListener('click', event => {
                if (event.target.closest('.filter-bar a')) showLoader();
            });

            window.addEventListener('pageshow', () => {
                overlay.hidden = true;
                document.body.classList.remove('listing-is-loading');
            });
        })();
    </script>

   
   
   
    




</body>
</html>
