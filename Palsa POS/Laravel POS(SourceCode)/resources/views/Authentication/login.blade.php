@extends('Authentication.layouts.master')

@section('content')
<div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

        <div class="col-xl-6 col-lg-12 col-md-9 mt-5">

            <div class="card o-hidden border-0 my-5 loginform">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="row">
                        <div class="col-lg-10 offset-1">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="mb-4">Login</h1>
                                </div>
                                <form class="user" method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="form-group">
                                        <input type="email" class="form-control form-control-user @error('email') is-invalid @enderror" placeholder="Enter Email Address..." name="email" value="{{old ('email')}}">
                                        @error('email')
                                            <small class="invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <input type="password" class="form-control form-control-user @error('password') is-invalid @enderror" placeholder="Password" name="password" value="{{old ('password')}}">
                                        @error('password')
                                            <small class="invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <input type="submit" value="Login" class="btn btn-primary btn-user btn-block">

                                    <hr>
                                   <div class="row">
                                    <div class="col">
                                        <a href="{{url('/auth/google/redirect')}}" class="btn btn-google btn-user btn-block">
                                            <i class="fab fa-google fa-fw"></i> Login with Google
                                        </a>

                                    </div>
                                   </div>
                                </form>
                                <hr>
                                <div class="text-center ">
                                    <a class="small text-dark" href="{{route ('userRegister')}}">Don't have an Account? Sign Up</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>


@endsection
