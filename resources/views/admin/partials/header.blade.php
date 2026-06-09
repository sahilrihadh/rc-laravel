<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
  <!-- Navbar Brand-->
  <a class="navbar-brand ps-3" href="{{ route('admin.dashboard') }}">
    <img src="{{ asset('assets/img/rc-logo.png') }}" width="100px" class="img-fluid" alt="">
  </a>

  <!-- Sidebar Toggle-->
  <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!">
    <i class="fas fa-bars"></i>
  </button>

  <!-- Navbar-->
  <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
    <li class="nav-item">
      <a class="btn btn-logout" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
    </li>
  </ul>
</nav>