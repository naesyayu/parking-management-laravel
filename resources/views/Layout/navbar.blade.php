<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<ul class="nav justify-content-center bg-light shadow-sm" style="border-bottom: 1px solid #dee2e6;">
  <li class="nav-item">
    <a class="nav-link {{ request()->is('/') ? 'active text-primary fw-semibold' : 'text-dark' }}"
       href="{{ url('/') }}">
       Dashboard Laporan
    </a>
  </li>

  <li class="nav-item">
    <a class="nav-link text-dark "
       href="{{ url('/') }}">
       Riwayat Transaksi
    </a>
  </li>

  <li class="nav-item">
    <a class="nav-link text-dark "
       href="{{ url('/') }}">
       Trancking Kendaraan
    </a>
  </li>

  <li class="nav-item">
    <a class="nav-link text-dark "
       href="{{ url('/') }}">
       Parkir-Masuk
    </a>
  </li>

  <li class="nav-item">
    <a class="nav-link text-dark "
       href="{{ url('/') }}">
       Parkir-keluar
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
      <li><a class="dropdown-item" href="{{ route('roles.index') }}">Role User</a></li>
      <li><a class="dropdown-item" href="{{ route('user.index') }}">User</a></li>
      <li><a class="dropdown-item" href="{{ route('pemilik.index') }}">Pemilik</a></li>
      <li><a class="dropdown-item" href="{{ route('member.index') }}">Data Member</a></li>
      <li><a class="dropdown-item" href="{{ route('area-parkir.index') }}">Area Parkir</a></li>
      <li><a class="dropdown-item" href="{{ route('area-kapasitas.index') }}">Kapasitas Area Parkir</a></li>
      <li><a class="dropdown-item" href="{{ route('tipe-kendaraan.index') }}">Tipe Kendaraan</a></li>
      <li><a class="dropdown-item" href="{{ route('data-kendaraan.index') }}">Data Kendaraan</a></li>
      <li><a class="dropdown-item" href="{{ route('tarif-parkir.index') }}">Tarif Parkir</a></li>
      <li><a class="dropdown-item" href="{{ route('metode-pembayaran.index') }}">Metode Pembayaran</a></li>
    </ul>
  </li>
</ul>
