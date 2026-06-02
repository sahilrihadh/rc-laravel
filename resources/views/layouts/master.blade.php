<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Webinar Solution')</title>
    <!----------------- fonts ------------------------>
   <link rel="stylesheet" type="text/css" href="fonts/font.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <!-- Custom CSS -->
    <style>
        [x-cloak] { display: none !important; }
        .loading { opacity: 0.5; pointer-events: none; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50">
    
    <!-- Header -->
    @include('partials.header')
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    @include('partials.footer')
    
    <script>
        // Set CSRF token for Axios
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
        axios.defaults.withCredentials = true;
    </script>
    
    @stack('scripts')
</body>
</html>