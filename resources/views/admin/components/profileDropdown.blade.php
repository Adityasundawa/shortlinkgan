<div class="ms-auto">
    <ul class="list-unstyled">
        <li class="dropdown pc-h-item header-user-profile">
            <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
                <img src="{{ asset('assets') }}/images/user/avatar-2.jpg" alt="user-image" class="user-avtar" />
            </a>
            <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                <div class="dropdown-header d-flex align-items-center justify-content-between">
                    <h5 class="m-0">Profile</h5>
                </div>
                <div class="dropdown-body">
                    <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 225px)">
                        <div class="d-flex mb-1">
                            <div class="flex-shrink-0">
                                <img src="{{ asset('assets') }}/images/user/avatar-2.jpg" alt="user-image" class="user-avtar wid-35" />
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ Auth::user()->name }}</h6>
                                <span>{{ ucwords(Auth::user()->role) }}</span>
                            </div>
                        </div>
                        <hr class="border-secondary border-opacity-50" />
                        <div class="mb-3">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="btn btn-primary btn-block">
                                    <svg class="pc-icon me-2">
                                        <use xlink:href="#custom-logout-1-outline"></use>
                                    </svg>Logout
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </li>
    </ul>
</div>
