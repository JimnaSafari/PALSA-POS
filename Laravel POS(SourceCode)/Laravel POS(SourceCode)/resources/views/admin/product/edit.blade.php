@extends('admin.layouts.master')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="">
                    <div class="">
                        <h6 class="m-0 font-weight-bold text-dark">
                            Update Your Product
                        </h6>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form action="{{ route('productUpdate') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <input type="hidden" name="oldImage" value="{{$products->image}}">
                        <input type="hidden" name="productID" value="{{$products->id}}">
                            <div class="col-4">
                                <div class="mb-3">
                                    <label for="exampleFormControlInput1" class="form-label">Category Name</label>
                                        <select name="categoryName" id=""
                                                class="form-control @error('categoryName') is-invalid @enderror">

                                            <option value="">Choose Category Name...</option>
                                            @foreach ($categories as $item)
                                                <option value="{{ $item->id }}" @if (old('categoryName',$products->category_id) == $item->id)selected @endif>{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                            @error('categoryName')
                                                <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                </div>
                                <!-- image  -->
                                <img class="img-thumbnail w-100" src="{{ asset('productImages/'. $products->image) }}" alt="" id="output" style="max-width: 320px; height: 300px;">
                                <input type="file" name="image" style="max-width: 320px;"
                                            class="form-control mt-2 @error('image') is-invalid @enderror"
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
                                                id="exampleFormControlInput1" placeholder="Name..." value="{{old('name',$products->name)}}">
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
                                                id="exampleFormControlInput1" placeholder="Price..." value="{{old('price',$products->price)}}">
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
                                                class="form-control @error('purchase_price') is-invalid @enderror"
                                                id="exampleFormControlInput1" placeholder=Purchase price..." value="{{old('purchase_price',$products->purchase_price)}}">
                                            @error('purchase_price')
                                                <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-label">Count</label>
                                            <input type="text" name="count"
                                                class="form-control @error('count') is-invalid @enderror"
                                                id="exampleFormControlInput1" placeholder="Count..." value="{{old('count',$products->count)}}">
                                            @error('count')
                                                <small class="invalid-feedback">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <label for="exampleFormControlInput1" class="form-label">Description</label>
                                        <textarea name="description" id="" cols="30" rows="7"
                                            class="form-control  @error('description')is-invalid @enderror">{{old('description',$products->description)}}</textarea>
                                        @error('description')
                                            <small class="invalid-feedback">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <input type="submit" value="Update" class="btn btn-primary mt-2 w-100">
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('productList') }}" class="btn btn-secondary mt-2 w-100 text-center"
                                            >Back</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                </form>
            </div>

        </div>

    </div>
    <!-- /.container-fluid -->
@endsection
