<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title>ADMIN :: <?php echo $__env->yieldContent('title', 'Royal Canin'); ?></title>
  <link rel="icon" type="image/x-icon" href="<?php echo e(asset('assets/img/favicon.ico')); ?>" />
  <!-------------- Fonts ------------------>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

  <!--------------- Stylesheets --------------->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo e(asset('assets/css/admin.css')); ?>" rel="stylesheet" />
  <link href="<?php echo e(asset('assets/css/admin-custom.css')); ?>" rel="stylesheet" />

  <!-- SweetAlert2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

  <!----------------- Fontawesome ------------>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body class="sb-nav-fixed">
  <?php echo $__env->make('admin.partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <div id="layoutSidenav">
    <?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div id="layoutSidenav_content">
      <main>
        <div class="container-fluid p-4">
          <?php echo $__env->yieldContent('content'); ?>
        </div>
      </main>
    </div>
  </div>

  <!-- CORE JS FILES (CDN) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <!-- Alpine.js -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="<?php echo e(asset('assets/js/main.js')); ?>"></script>
  <!-- Toastr configuration -->
<script>
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
</script>
  <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html><?php /**PATH /Users/sahilrihadh/Development/royalcanin/webinar-solution/resources/views/admin/layouts/master.blade.php ENDPATH**/ ?>