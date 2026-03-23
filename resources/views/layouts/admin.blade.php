@extends('layouts.app')

@section('body')

    @include('partials.sidebar-admin')

    <content>
        <header>
            <a href="#" class="sidebar_toggle"><i class="fa fa-bars"></i></a>
            <div class="header_content">
                <h6>Welcome Back</h6>
                <h4>{{ auth()->user()->name }}</h4>
            </div>
            <a href="#" class="header_btn"><img src="{{ asset('build/assets/images/seal-question.svg') }}"></a>
            <a href="#" class="header_btn"><img src="{{ asset('build/assets/images/bell-notification-social-media.svg') }}"></a>
            <a href="{{ route('admin.logout') }}" class="header_btn"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <img src="{{ asset('build/assets/images/exit.svg') }}">
                </a>

                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>
        </header>

        <div class="content_area">
            @yield('content')
        </div>

        <footer>
            <p class="copyright">© 2025 {{ setting('company_name') }}. All rights reserved.</p>
            <ul>
                <li><a href="#">Privacy Policy</a></li>
                <li>|</li>
                <li><a href="#">Terms & Conditions</a></li>
            </ul>
        </footer>
    </content>
    

@endsection
