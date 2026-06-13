<header class="header">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">Hello, <?php echo e(Auth::user()->full_name ?? Auth::user()->name); ?></a>

            <ul class="navbar-nav m-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('webcast') ? 'active' : ''); ?>" href="<?php echo e(route('webcast')); ?>">Live Session</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('previous-sessions') ? 'active' : ''); ?>" href="<?php echo e(route('previous-sessions')); ?>">Previous Session</a>
                </li>
            </ul>

            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-logout">
                    <i class="bi bi-person-circle"></i> Logout
                </button>
            </form>
        </div>
    </nav>
</header><?php /**PATH /Users/sahilrihadh/Development/royalcanin/webinar-solution/resources/views/partials/header.blade.php ENDPATH**/ ?>