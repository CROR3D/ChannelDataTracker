<div class="row top-header my-3">
    <div class="col data-user text-left">
        @if (Sentinel::check())
            Hello, {{ Sentinel::getUser()->email }}!</h1>
        @else
            Welcome, Guest!
        @endif
    </div>
    <div class="col data-connection text-right">
        Data connection:
        <span class="{{ ($connectionStatus == 'ACTIVE') ? 'text-success' : 'text-danger' }} mr-3">
            {{ $connectionStatus }}
        </span>
        @if (Sentinel::check())
            <a class="btn-custom btn-custom-secondary" href="{{ route('auth.logout') }}">Log Out</a>
        @endif
    </div>
</div>
