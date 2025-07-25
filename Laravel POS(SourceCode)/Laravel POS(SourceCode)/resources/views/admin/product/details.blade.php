@extends('admin.layouts.master')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- DataTales Example -->
        <div class="card shadow mb-4 col">
            <div class="card-header py-3">
                <div class="">
                    <div class="">
                        <h6 class="m-0 font-weight-bold text-dark">Product Details Page</h6>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-3 h-100">
                        <img class="img-thumbnail h-100" src="{{ asset('productImages/' . $data->image) }}" alt=""
                            id="output">
                     </div>
                    <div class="col">
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Name</label>
                                    <h4>{{ $data->name }}</h4>

                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Category Name</label>
                                    <h4>{{ $data->category_name }}</h4>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col">
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Instock</label>
                                    <h4>{{ $data->count }}</h4>

                                </div>
                            </div>

                            <div class="col">
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Sell Price</label>
                                    <h4>{{ $data->price }} mmk</h4>

                                </div>
                            </div>

                        </div>
                        <div class="row ">
                            <div class="col">
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Description</label>
                                    <h5 class="">{{ $data->description }}</h5>

                                </div>
                            </div>


                        </div>

                        <div class="row w-100">
                            <div class="col-6">
                                <a href="{{ route('productList') }}"
                                    class="btn btn-primary mt-2 text-center w-100">Back</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <!-- /.container-fluid -->
@endsection
