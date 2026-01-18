<!-- Tambahkan Font Awesome di <head> jika belum ada -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<nav class="navbar bg-primary py-3">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold fs-4" href="#">
      <img src="/docs/5.3/assets/brand/bootstrap-logo.svg" alt="Logo" width="30" height="24" class="d-inline-block align-text-top">
      Bootstrap
    </a>
    <div class="d-flex align-items-center me-5">
      <a href="/profile" class="text-white text-decoration-none d-flex align-items-center" style="font-size: 21px;">
        <i class="fas fa-user-circle me-2" style="font-size: 37px;"></i>
        <span>Admin</span>
      </a>
    </div>
  </div>
</nav>
<ul class="nav justify-content-center bg-light shadow-sm" style="border-bottom: 1px solid #dee2e6;">
  <li class="nav-item">
    <a class="nav-link active text-primary fw-semibold" aria-current="page" href="#">Active</a>
  </li>
  <li class="nav-item">
    <a class="nav-link text-dark" href="#">Link</a>
  </li>
  <li class="nav-item">
    <a class="nav-link text-dark" href="#">Link</a>
  </li>
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle text-dark" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Dropdown</a>
    <ul class="dropdown-menu">
      <li><a class="dropdown-item" href="#">Action</a></li>
      <li><a class="dropdown-item" href="#">Another action</a></li>
      <li><a class="dropdown-item" href="#">Something else here</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item" href="#">Separated link</a></li>
    </ul>
  </li>
</ul>