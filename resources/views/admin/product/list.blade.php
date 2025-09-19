@extends('admin.layouts.master')

@section('content')
    <div class="container-fluid">
        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <div class="d-flex justify-content-between">
                    <div class="">
                        <h6 class="m-0 font-weight-bold text-dark">
                            <form action="{{route('productList')}}" method="get">
                                <div class="input-group mb-3">
                                    <input type="text" name="searchKey" class="form-control "
                                        placeholder="Products name..." value="{{request('searchKey')}}">
                                    <button class="btn btn-outline-secondary" type="submit" id="button-addon2">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </button>
                                </div>
                            </form>
                        </h6>
                    </div>
                    <div class="font-weight-bold">
                        <a href="{{ route('productCreate') }}"><i class="fa-solid fa-plus"></i> Add Product</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr class="text-center text-white">
                                <th>Product Name</th>
                                <th>Image</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $item)
                                <tr class="text-center text-white">
                                    <td>{{ $item->name }}</td>
                                    <td >
                                        <img class="rounded-circle" style="width: 64px; height: 64px;" src="{{ asset('productImages/' . $item->image) }}" alt="">
                                    </td>
                                    <td>{{ $item->price }} mmk</td>
                                    <td>{{ $item->count }}</td>
                                    <td style="width: 200px;">
                                        <a href="{{ route('productDetails', $item->id) }}"><i class="fa-solid fa-eye btn btn-primary"></i></a>
                                        <a href="{{ route('productEdit', $item->id) }}"><i class="fa-solid fa-pen-to-square btn btn-secondary"></i></a>
                                        <a href="{{ route('productDelete', $item->id) }}"><i class="fa-solid fa-trash-can btn btn-danger"></i></a>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                    <span class="d-flex justify-content-end">{{ $products->links() }}</span>
                </div>
            </div>
        </div>

    </div>
    <!-- /.container-fluid -->
@endsection
