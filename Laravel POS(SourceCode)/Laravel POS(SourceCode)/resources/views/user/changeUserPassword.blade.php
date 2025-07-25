@extends('user.layouts.master')

@section('content')

<!-- Single Page Header start -->
<div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Change Password</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{route('userDashboard')}}">Home</a></li>
            <li class="breadcrumb-item "><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Change Password</li>
        </ol>
    </div>
<!-- Single Page Header End -->

     <!-- Begin Page Content -->
     <div class="container-fluid py-5">
        <div class="card shadow col-6 offset-3 py-3">
                {{-- <div class="container"> --}}

                <div class="card-header">
                    <div class="">
                        <div class="">
                            <h5 class="font-weight-bold text-secondary">Change Password...</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{route ('changeUserPassword')}}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label ">Old Password</label>
                            <input type="password" name="oldPassword" value="{{old('oldPassword')}}" class="form-control @error('oldPassword') is-invalid @enderror" id="exampleFormControlInput1" >
                            @error('oldPassword')
                                <small class="invalid-feedback">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label ">New Password</label>
                            <input type="password" name="newPassword" value="{{old('newPassword')}}" class="form-control @error('newPassword') is-invalid @enderror" id="exampleFormControlInput1" >
                            @error('newPassword')
                                <small class="invalid-feedback">{{$message}}</small>
                            @enderror
                        </div><div class="mb-3">
                            <label for="exampleFormControlInput1" class="form-label ">Confirm Password</label>
                            <input type="password" name="confirmPassword" value="{{old('confirmPassword')}}" class="form-control @error('confirmPassword') is-invalid @enderror" id="exampleFormControlInput1">
                            @error('confirmPassword')
                                <small class="invalid-feedback">{{$message}}</small>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col">
                                <input type="submit" value="Change" class="btn btn-warning w-100">
                            </div>
                        </div>
                    </form>
                </div>
        </div>
    </div>
    <!-- /.container-fluid -->
@endsection
