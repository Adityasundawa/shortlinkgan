<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('admin.dashboard') }}" class="b-brand text-primary">
                <!-- ========   Change your logo from here   ============ -->
                <h4 class="fw-bold">HUFT <i class="fw-light">REMAKE</i></h4>
            </a>
        </div>

        <div class="navbar-content">
            <div class="card pc-user-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <img src="{{ asset('assets') }}/images/user/avatar-1.jpg" alt="user-image" class="user-avtar wid-45 rounded-circle" />
                        </div>
                        <div class="flex-grow-1 ms-3 me-2">
                            <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                            <small>{{ ucwords(Auth::user()->role) }}</small>
                        </div>
                        <a class="btn btn-icon btn-link-secondary avtar" data-bs-toggle="collapse" href="#pc_sidebar_userlink">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-sort-outline"></use>
                            </svg>
                        </a>
                    </div>
                    <div class="collapse pc-user-links" id="pc_sidebar_userlink">
                        <div class="pt-3">
                            <a href="javascript:void(0)">
                                <i class="ti ti-user"></i>
                                <span>My Account</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                    <i class="ti ti-power"></i>
                                    <span>Logout</span>
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <ul class="pc-navbar">
                <li class="pc-item">
                    <a href="{{ route('admin.dashboard') }}" class="pc-link">
                        <span class="pc-micon">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-status-up"></use>
                            </svg>
                        </span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>

                {{-- <li class="pc-item pc-hasmenu">
                    <a href="javascript:void(0)" class="pc-link">
                        <span class="pc-micon">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-fatrows"></use>
                            </svg>
                        </span>
                        <span class="pc-mtext">Project Analytics</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item"><a class="pc-link" href="{{ route('project_analytic.list') }}">List Projects</a></li>
                <li class="pc-item"><a class="pc-link" href="">Create new project</a></li>
                <li class="pc-item"><a class="pc-link" href="">Generate mass link</a></li>
            </ul>
            </li> --}}

            @if (Auth::user()->hasRole('admin'))
            <li class="pc-item pc-hasmenu">
                <a href="javascript:void(0)" class="pc-link">
                    <span class="pc-micon">
                        <svg class="pc-icon">
                            <use xlink:href="#custom-user-add"></use>
                        </svg>
                    </span>
                    <span class="pc-mtext">Users Management</span>
                    <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                </a>
                <ul class="pc-submenu">
                    <li class="pc-item"><a class="pc-link" href="{{ route('admin.list_team') }}">Team Management</a></li>
                    <li class="pc-item"><a class="pc-link" href="{{ route('admin.list_user') }}">List User</a></li>
                    <li class="pc-item"><a class="pc-link" href="{{ route('admin.create_new_user') }}">Create new user</a></li>
                </ul>
            </li>
            @endif

            <li class="pc-item">
                <a href="{{ route('project_analytic.list') }}" class="pc-link">
                    <span class="pc-micon">
                        <svg class="pc-icon">
                            <use xlink:href="#custom-link"></use>
                        </svg>
                    </span>
                    <span class="pc-mtext">Short Link</span>
                </a>
            </li>

             <li class="pc-item">
                <a href="{{ route('bulk.list') }}" class="pc-link">
                    <span class="pc-micon">
                        <svg class="pc-icon">
                            <use xlink:href="#custom-link"></use>
                        </svg>
                    </span>
                    <span class="pc-mtext">Bulk Short Link</span>
                </a>
            </li>


 <li class="pc-item">
                <a href="{{ route('campaign.index') }}" class="pc-link">
                    <span class="pc-micon">
                        <svg class="pc-icon">
                            <use xlink:href="#custom-link"></use>
                        </svg>
                    </span>
                    <span class="pc-mtext">Campaign</span>
                </a>
            </li>

            @if (Auth::user()->hasRole('admin'))
            <li class="pc-item">
                <a href="{{ route('user_project_analytic.list') }}" class="pc-link">
                    <span class="pc-micon">
                        <svg class="pc-icon">
                            <use xlink:href="#custom-link"></use>
                        </svg>
                    </span>
                    <span class="pc-mtext">Project Analytic - User</span>
                </a>
            </li>
            @endif


            {{-- <li class="pc-item">
                    <a href="{{ route('view.shortedLink') }}" class="pc-link">
            <span class="pc-micon">
                <svg class="pc-icon">
                    <use xlink:href="#custom-link"></use>
                </svg>
            </span>
            <span class="pc-mtext">Shorted Links</span>
            </a>
            </li> --}}

            <li class="pc-item">
                <a href="{{ route('admin.microsite') }}" class="pc-link">
                    <span class="pc-micon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="w-5 mr-2">
                            <path fill-rule="evenodd" d="M4.125 3C3.089 3 2.25 3.84 2.25 4.875V18a3 3 0 003 3h15a3 3 0 01-3-3V4.875C17.25 3.839 16.41 3 15.375 3H4.125zM12 9.75a.75.75 0 000 1.5h1.5a.75.75 0 000-1.5H12zm-.75-2.25a.75.75 0 01.75-.75h1.5a.75.75 0 010 1.5H12a.75.75 0 01-.75-.75zM6 12.75a.75.75 0 000 1.5h7.5a.75.75 0 000-1.5H6zm-.75 3.75a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5H6a.75.75 0 01-.75-.75zM6 6.75a.75.75 0 00-.75.75v3c0 .414.336.75.75.75h3a.75.75 0 00.75-.75v-3A.75.75 0 009 6.75H6z" clip-rule="evenodd"></path>
                            <path d="M18.75 6.75h1.875c.621 0 1.125.504 1.125 1.125V18a1.5 1.5 0 01-3 0V6.75z">
                            </path>
                        </svg>
                    </span>
                    <span class="pc-mtext">Microsite</span>
                </a>
            </li>

            @if (Auth::user()->hasRole('admin'))
            <li class="pc-item">
                <a href="{{ route('admin.domain_decentralized') }}" class="pc-link">
                    <span class="pc-micon">
                        <svg class="pc-icon">
                            <use xlink:href="#custom-data"></use>
                        </svg>
                    </span>
                    <span class="pc-mtext">Domain Decentralized</span>
                </a>
            </li>
            @endif
            </ul>
        </div>
    </div>
</nav>
