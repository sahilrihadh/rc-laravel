<div id="layoutSidenav_nav">
  <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
      <div class="nav">
        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users') }}">
          <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
          Users
        </a>

        <a class="nav-link {{ request()->routeIs('admin.questions.*') ? 'active' : '' }}" href="{{ route('admin.questions') }}">
          <div class="sb-nav-link-icon"><i class="fas fa-question"></i></div>
          Questions
        </a>

        <a class="nav-link {{ request()->routeIs('admin.polls.*') ? 'active' : '' }}" href="{{ route('admin.polls.index') }}">
          <div class="sb-nav-link-icon"><i class="fas fa-poll"></i></div>
          Polls
        </a>

        @if(Auth::user()->is_admin)
        <a class="nav-link {{ request()->routeIs('admin.visitors.*') ? 'active' : '' }}" href="{{ route('admin.visitors.index') }}">
          <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
          Unique Visitors
        </a>

        <a class="nav-link {{ request()->routeIs('admin.certificates.*') ? 'active' : '' }}" href="{{ route('admin.certificates.index') }}">
          <div class="sb-nav-link-icon"><i class="fas fa-award"></i></div>
          Participation Certificate
        </a>

        <a class="nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}" href="{{ route('admin.admins.index') }}">
          <div class="sb-nav-link-icon"><i class="fas fa-user-shield"></i></div>
          Admin User
        </a>
        @endif
      </div>
    </div>
    <div class="sb-sidenav-footer">
      <div class="small">Logged in as:</div>
      {{ Auth::guard('admin')->user()->username ?? Auth::guard('admin')->user()->full_name }}
      <form method="POST" action="{{ route('admin.logout') }}" class="mt-2">
        @csrf
        <button type="submit" class="btn btn-sm btn-danger w-100">Logout</button>
      </form>
    </div>
  </nav>
</div>