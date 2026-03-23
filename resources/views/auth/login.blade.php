@extends('layouts.auth')

@section('title', 'Login')

@section('content')

    <section class="login_section">
        <div class="login_left">
            <div class="login_top">
                <img src="{{ asset('build/assets/images/chisel-logo.png') }}" class="logo">
                <!-- <span>Don't have an account? <a href="register.html">Register</a></span> -->
            </div>
            <div class="login_block">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <h2>Login to your account</h2>
                    <p>Please enter your credentials to sign in!</p>
                    <div class="form-floating mb-3">
                        <input type="email" name="email" class="form-control" id="floatingInput" placeholder="name@example.com" autofocus required>
                        <label for="floatingInput">Email address</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                        <label for="floatingPassword">Password</label>
                    </div>
                    <div class="row mb-3 align-items-center justify-content-between">
                        <div class="col-auto">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                                <label class="form-check-label" for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('password.request') }}">Forgot Password?</a>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-theme btn-block btn-lg">Login</button>
                    </div>
                </form>
            </div>

            <div class="login_bottom">
                <a href="#">Contact Us</a>
                <ul>
                    <li><a href="#">Privacy Policy</a></li>
                    <li>|</li>
                    <li><a href="#">Term & Conditions</a></li>
                </ul>
            </div>
        </div>

        <div class="login_right">
            <div class="swiper login_slider">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="login_banner">
                            <img src="{{ asset('build/assets/images/slide1.jpg') }}">
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="login_banner">
                            <img src="{{ asset('build/assets/images/slide2.png') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
