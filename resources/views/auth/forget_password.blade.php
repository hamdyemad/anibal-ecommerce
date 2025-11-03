@extends('auth.layout')
@section('content')
    <div class="row justify-content-center">
        <div class="col-xxl-3 col-xl-4 col-md-6 col-sm-8">
            <div class="edit-profile mt-5">
                <div class="edit-profile__logos">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="">
                </div>
                <div class="card border-0">
                    <div class="card-header">
                        <div class="edit-profile__title">
                            <h6>Forgot Password?</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="edit-profile__body">
                            <p>Enter the email address you used when you joined and we’ll send you instructions to reset
                                your password.</p>
                            <form action="{{ route('forgetPassword.store') }}" method="POST">
                                @csrf
                                <div class="form-group mb-20">
                                    <label for="email">Email Adress</label>
                                    <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="name@example.com">
                                    @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-flex">
                                    <a class="btn btn-light me-2" href="{{ route('login') }}">
                                        Go Back
                                    </a>
                                    <button type="submit"
                                        class="btn btn-primary">
                                        Send Reset Instructions
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
