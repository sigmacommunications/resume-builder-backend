<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('admin.dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Admin</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('admin.dashboard' ) }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Category -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#categoryCollapse" aria-expanded="true" aria-controls="categoryCollapse">
            <i class="fas fa-list"></i>
            <span>Category</span>
        </a>
        <div id="categoryCollapse" class="collapse" aria-labelledby="headingCategory" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Options:</h6>
                <a class="collapse-item" href="{{ route('category.index') }}">View</a>
                <a class="collapse-item" href="{{ route('category.create') }}">Add</a>
            </div>
        </div>
    </li>

    <!-- Template -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#templateCollapse" aria-expanded="true" aria-controls="templateCollapse">
            <i class="fas fa-file-alt"></i>
            <span>Template</span>
        </a>
        <div id="templateCollapse" class="collapse" aria-labelledby="headingTemplate" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Options:</h6>
                <a class="collapse-item" href="{{ route('template.index') }}">View</a>
                <a class="collapse-item" href="{{ route('template.create') }}">Add</a>
            </div>
        </div>
    </li>

    <!-- Companies -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.companies.index') }}">
            <i class="fas fa-building"></i>
            <span>Companies</span>
        </a>
    </li>

    <!-- Departments & Employees -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.departments') }}">
            <i class="fas fa-sitemap"></i>
            <span>Departments & Employees</span>
        </a>
    </li>

    <!-- Assigned Templates -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('admin.assignments.index') }}">
            <i class="fas fa-paper-plane"></i>
            <span>Assigned Templates</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Users -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('users.index') }}">
            <i class="fas fa-users"></i>
            <span>Users</span>
        </a>
    </li>

    <!-- Settings -->
    <li class="nav-item">
        <a class="nav-link" href="{{ route('settings') }}">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </li>

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
