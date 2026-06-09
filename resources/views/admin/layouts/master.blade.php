<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ADMIN :: @yield('title', 'Royal Canin')</title>
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.ico') }}" />
  <!-------------- Fonts ------------------>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

  <!--------------- Stylesheets --------------->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="{{ asset('assets/css/admin.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/admin-custom.css') }}" rel="stylesheet" />

  <!-- Sweet Alert -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">

  <!----------------- Fontawesome ------------>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  @stack('styles')
</head>

<body class="sb-nav-fixed">
  @include('admin.partials.header')

  <div id="layoutSidenav">
    @include('admin.partials.sidebar')

    <div id="layoutSidenav_content">
      <main>
        <div class="container-fluid px-4">
          @yield('content')
        </div>
      </main>
    </div>
  </div>

  <!-- CORE JS FILES (CDN) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
  <script src="{{ asset('assets/js/main.js') }}"></script>

  @stack('scripts')
</body>

</html>