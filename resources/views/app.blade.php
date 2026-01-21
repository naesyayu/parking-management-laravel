<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Parking Management</title>

    <!-- ✅ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <!-- NAVBAR -->
    @include('Layout.navbar')

    <!--@include('Layout.sidebar')-->

    <div class="container-fluid">
        <div class="row">

            <!-- MAIN CONTENT -->
            <main class="col p-4">
                @yield('content')
            </main>

        </div>
    </div>

    <!-- ✅ Bootstrap JS (WAJIB untuk offcanvas, dropdown, dll) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
