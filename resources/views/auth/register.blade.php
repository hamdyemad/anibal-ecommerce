@extends('auth.layout')
@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-3 col-xl-4 col-md-6 col-sm-8">
            <div class="edit-profile">
                <div class="edit-profile__logos">
                    <img class="dark" src="{{ asset('assets/img/logo-dark.png') }}" alt="">
                    <img class="light" src="{{ asset('assets/img/logo-white.png') }}" alt="">
                </div>
                <div class="card border-0">
                    <div class="card-header">
                        <div class="edit-profile__title">
                            <h6>Sign Up HexaDash</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('signup') }}" method="POST">
                            @csrf
                            <div class="edit-profile__body">
                                <div class="form-group mb-20">
                                    <label for="name">name</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        placeholder="Full Name">
                                    @if ($errors->has('name'))
                                        <p class="text-danger">{{ $errors->first('name') }}</p>
                                    @endif
                                </div>
                                <div class="form-group mb-20">
                                    <label for="email">Email Adress</label>
                                    <input type="text" class="form-control" id="email" name="email"
                                        placeholder="Email address">
                                    @if ($errors->has('email'))
                                        <p class="text-danger">{{ $errors->first('email') }}</p>
                                    @endif
                                </div>
                                <div class="form-group mb-15">
                                    <label for="password-field">password</label>
                                    <div class="position-relative">
                                        <input id="password-field" type="password" class="form-control" name="password"
                                            placeholder="Password">
                                        <span toggle="#password-field"
                                            class="uil uil-eye-slash text-lighten fs-15 field-icon toggle-password2"></span>
                                    </div>
                                    @if ($errors->has('password'))
                                        <p class="text-danger">{{ $errors->first('password') }}</p>
                                    @endif
                                </div>
                                <div class="admin-condition">
                                    <div class="checkbox-theme-default custom-checkbox ">
                                        <input class="checkbox" type="checkbox" id="check-1">
                                        <label for="check-1">
                                            <span class="checkbox-text">Creating an account means you’re okay
                                                with our <a href="#" class="color-primary">Terms of
                                                    Service</a> and <a href="#" class="color-primary">Privacy
                                                    Policy</a>
                                                my preference</span>
                                        </label>
                                    </div>
                                </div>
                                <div
                                    class="admin__button-group button-group d-flex pt-1 justify-content-md-start justify-content-center">
                                    <button
                                        class="btn btn-primary btn-default w-100 btn-squared text-capitalize lh-normal px-50 signIn-createBtn ">
                                        Create Account
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="px-20">
                        <p class="social-connector social-connector__admin text-center">
                            <span>Or</span>
                        </p>
                        <div class="button-group d-flex align-items-center justify-content-center">
                            <ul class="admin-socialBtn">
                                <li>
                                    <button class="btn text-dark google">
                                        <img class="svg" src="{{ asset('assets/img/google-Icon.svg') }}"
                                            alt="img" />
                                    </button>
                                </li>
                                <li>
                                    <button class=" radius-md wh-48 content-center facebook">
                                        <i class="uil uil-facebook-f"></i>
                                    </button>
                                </li>
                                <li>
                                    <button class="radius-md wh-48 content-center twitter">
                                        <i class="uil uil-twitter"></i>
                                    </button>
                                </li>
                                <li>
                                    <button class="radius-md wh-48 content-center github">
                                        <i class="uil uil-github"></i>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="admin-topbar">
                        <p class="mb-0">
                            Don't have an account?
                            <a href="{{ route('login') }}" class="color-primary">
                                Sign In
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
