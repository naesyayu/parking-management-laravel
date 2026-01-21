<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<nav class="navbar bg-primary py-3">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold fs-4 text-white ms-3" href="{{ url('/') }}">
      <i class="fa-solid fa-square-parking me-2"></i>
      Parking Management
    </a>

    <div class="d-flex align-items-center me-5">
      <a href="#" class="text-white text-decoration-none d-flex align-items-center" style="font-size: 21px;">
        <i class="fas fa-user-circle me-2" style="font-size: 37px;"></i>
        <span>Admin</span>
      </a>
    </div>
  </div>
</nav>

<ul class="nav justify-content-center bg-light shadow-sm" style="border-bottom: 1px solid #dee2e6;">
  <li class="nav-item">
    <a class="nav-link {{ request()->is('/') ? 'active text-primary fw-semibold' : 'text-dark' }}"
       href="{{ url('/') }}">
       Dashboard
    </a>
  </li>

  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle text-dark"
       data-bs-toggle="dropdown"
       href="#"
       role="button">
       CRUD
    </a>

    <ul class="dropdown-menu">
      <li><a class="dropdown-item" href="{{ route('user.index') }}">User</a></li>
      <li><a class="dropdown-item" href="{{ route('pemilik.index') }}">Pemilik</a></li>
      <li><a class="dropdown-item" href="#">Data Member</a></li>
      <li><a class="dropdown-item" href="#">Area Parkir</a></li>
      <li><a class="dropdown-item" href="{{ route('tipe-kendaraan.index') }}">Tipe Kendaraan</a></li>
      <li><a class="dropdown-item" href="#">Data Kendaraan</a></li>
      <li><a class="dropdown-item" href="#">Tarif Parkir</a></li>
    </ul>
  </li>
</ul>
