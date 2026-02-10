<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Parking Management - @yield('title', 'Dashboard')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome (PENTING!) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom Styles -->
    @stack('styles')
    
</head>

{{-- SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Global Notification Handler --}}
<script>
   @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        @endif

    
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '{{ session('error') }}',
            confirmButtonText: 'OK'
        });
    @endif

    document.addEventListener('DOMContentLoaded', () => {
        if (Swal.isVisible()) {
            Swal.close();
        }

        // paksa hapus backdrop kalau ada bug
        document.body.classList.remove('swal2-shown');
        document.body.style.removeProperty('padding-right');

        document.querySelectorAll('.swal2-container').forEach(el => el.remove());
    });
</script>

{{-- SweetAlert Helper Functions --}}
<script src="{{ asset('js/sweetalert-helpers.js') }}"></script>

<body>

    @include('Layout.header')

    @include('Layout.navbar')

    <div class="container-fluid">
        <div class="row">
            <!-- MAIN CONTENT -->
            <main class="col p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom Scripts (PENTING!) -->
    @stack('scripts')

</body>
</html>