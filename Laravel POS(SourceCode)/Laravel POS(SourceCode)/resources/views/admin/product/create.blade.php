@extends('admin.layouts.master')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- DataTales Example -->
        <div class="card shadow mb-4 col">
            <div class="card-header py-3">
                <div class="">
                    <div class="">
                        <h6 class="m-0 font-weight-bold text-dark">Add Product Page</h6>
                    </div>
                </div>
            </div>
            <form action="{{ route('product#Create') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Category Name</label>
                                    <select name="categoryName" id=""
                                                class="form-control @error('categoryName') is-invalid @enderror">

                                    <option value="">Choose Category Name...</option>
                                                @foreach ($categories as $item)
                                    <option value="{{ $item->id }}" @if (old('categoryName') == $item->id)selected @endif>{{ $item->name }}</option>
                                                @endforeach
                                    </select>
                                            @error('categoryName')
                                                <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                            </div>

                            <img class="img-thumbnail" src="{{ asset('defaultImg/default.jpg') }}" alt=""
                                id="output">
                            <input type="file" name="image"
                                class="form-control mt-1 @error('image') is-invalid @enderror"
                                onchange="loadFile(event)">


                            @error('image')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Name</label>
                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            id="exampleFormControlInput1" placeholder="Name..." value="{{old('name')}}">
                                        @error('name')
                                            <small class="invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Sell Price</label>
                                        <input type="text" name="price"
                                            class="form-control @error('price') is-invalid @enderror"
                                            id="exampleFormControlInput1" placeholder="Price..." value="{{old('price')}}">
                                        @error('price')
                                            <small class="invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">

                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Purchase Price</label>
                                        <input type="text" name="purchaseprice"
                                            class="form-control @error('purchaseprice') is-invalid @enderror"
                                            id="exampleFormControlInput1" placeholder="Price..." value="{{old('purchaseprice')}}">
                                        @error('purchaseprice')
                                            <small class="invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>

                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Count</label>
                                        <input type="text" name="count"
                                            class="form-control @error('count') is-invalid @enderror"
                                            id="exampleFormControlInput1" placeholder="Count..." value="{{old('count')}}">
                                        @error('count')
                                            <small class="invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">Description</label>
                                        <textarea name="description" cols="30" rows="5"
                                            class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                        @error('description')
                                            <small class="invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <input type="submit" value="Create" class="btn btn-primary w-100">
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('productList') }}" class="btn btn-secondary w-100"
                                            >Back</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
    <!-- /.container-fluid -->
@endsection
