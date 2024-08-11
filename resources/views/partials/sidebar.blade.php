<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-custom sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard.index') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Dashboard</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ Request::is('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.index') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Master
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item {{ Request::is('dashboard/group*') || Request::is('dashboard/major*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
            aria-expanded="true" aria-controls="collapseTwo">
            <i class="fas fa-fw fa-cog"></i>
            <span>Data Master</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('dashboard.group.index') }}">Kelas</a>
                {{-- <a class="collapse-item" href="{{ route('dashboard.major.index') }}">Jurusan</a> --}}
            </div>
        </div>
    </li>

    {{-- <li class="nav-item">
        <a class="nav-link" href="{{ route('dashboard.student.index') }}">
            <i class="fas fa-user-graduate"></i>
            <span>Data Siswa</span></a>
    </li> --}}
    <li class="nav-item {{ Request::is('dashboard/teacher*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.teacher.index') }}">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Data Guru</span></a>
    </li>
    <li class="nav-item {{ Request::is('dashboard/subject*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.subject.index') }}">
            <i class="fas fa-book-open"></i>
            <span>Data Mapel</span></a>
    </li>
    <li class="nav-item {{ Request::is('dashboard/working*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.working.index') }}">
            <i class="fas fa-book-open"></i>
            <span>Penugasan</span></a>
    </li>
    <li class="nav-item {{ Request::is('dashboard/schedule*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.schedule.index') }}">
            <i class="fas fa-book-open"></i>
            <span>Generate Schedule</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Option
    </div>

    <!-- Nav Item - Tables -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-fw fa-user"></i>
            <span>Logout</span></a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
