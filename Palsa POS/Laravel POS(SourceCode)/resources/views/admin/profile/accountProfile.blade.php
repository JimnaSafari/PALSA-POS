@extends('admin.layouts.master')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- DataTales Example -->
        <div class="card shadow mb-4 col">
            <div class="card-header py-3">
                <div class="">
                    <div class="">
                        <h6 class="m-0 font-weight-bold text-dark">
                            Account Information
                        </h6>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row ">
                    <div class="col-4">
                    <input type="hidden" name="oldImage" value="{{ $account->image}}">

                        <img class="img-thumbnail h-100" src="{{ asset('adminProfile/' . $account->profile) }}" alt=""
                            id="output">
                        <input type="file" name="image" style="max-width: 300px;"
                                    class="form-control mt-1 @error('image') is-invalid @enderror"
                                    onchange="loadFile(event)">

                    </div>
                    <div class="col-8">
                        <div class="row mt-3 fs-3">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Name</label>
                                    <h4>{{ $account->name == null ? $account->nickname : $account->name }}</h4>

                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Email</label>
                                    <h4>{{ $account->email  }}</h4>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Phone</label>
                                    <h4>{{ $account->phone == null ? '..............' : $account->phone  }}</h4>

                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Address</label>
                                    <h4>{{ $account->address == null ? '..............' : $account->address  }}</h4>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-6 mt-2">
                            <a href="{{ route('adminDashboard') }}" class="btn btn-secondary w-100 text-center"
                                            >Back</a>
                        </div>
                        </div>


                    </div>
                </div>
            </div>

        </div>

    </div>
    <!-- /.container-fluid -->
@endsection
