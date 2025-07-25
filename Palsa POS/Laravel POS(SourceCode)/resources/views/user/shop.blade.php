@extends('user.layouts.master')
@section('content')
    <!-- Single Page Header start -->
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Shop</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{route('userDashboard')}}">Home</a></li>
            <li class="breadcrumb-item "><a href="#">Pages</a></li>
            <li class="breadcrumb-item active text-white">Shop</li>
        </ol>
    </div>
    <!-- Single Page Header end -->

    <!-- Shop Start-->
    <div class="container-fluid products">
        <div class="container py-3">
            <h1 class="mb-3 text-white">Choose your flowers</h1>
            <div class="row g-4">
                <div class="col-lg-12">
                    <div class="row g-4">
                        <div class="col-xl-3">
                            <form action="{{ route('shopList') }}" method="get">
                                @csrf
                                <div class="input-group w-100 mx-auto d-flex">
                                    <input type="search" class="form-control p-3" value="{{ request('searchKey') }}"
                                        name="searchKey" placeholder="keywords">
                                    <button type="submit" class="input-group-text p-3"><i
                                            class="fa fa-search"></i></button>
                                </div>
                            </form>
                        </div>

                        <div class="row g-4">
                            <div class="col-lg-3">
                                <div class="row g-4">
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <h4 class="text-white">Categories</h4>
                                            <ul class="text-white list-unstyled products-categorie">
                                                <li>
                                                    <div class="d-flex justify-content-between products-name ">
                                                        <a href="{{ route('shopList') }}"><i class="fa-solid fa-clover"></i>
                                                            All Categories</a>
                                                    </div>
                                                </li>
                                                @foreach ($categories as $item)
                                                    <li>
                                                        <div class="d-flex justify-content-between products-name">
                                                            <a href="{{ route('shopList', $item->id) }}"><i class="fa-solid fa-clover"></i> {{ $item->name }}</a>
                                                            {{-- <span>(3)</span> --}}
                                                        </div>
                                                    </li>
                                                @endforeach

                                            </ul>
                                        </div>
                                    </div>


                                    <div class="col-lg-12">
                                        <form action="{{ route('shopList') }}" method="get">
                                            @csrf
                                            <p class="text-white">Price</p>
                                            <input type="text" name="minPrice" value="{{ request('minPrice') }}"
                                                class="form-control my-2" placeholder="Minimum">
                                            <input type="text" name="maxPrice" value="{{ request('maxPrice') }}"
                                                class="form-control my-2" placeholder="Maximum">
                                            <input type="submit" class="btn-warning my-2" value="Filter">
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-9">
                                <div class="row g-4">
                                    @foreach ($products as $item)
                                        <div class="col-md-6 col-lg-4 col-xl-4">
                                            <div class="rounded position-relative products-item" style="background: linear-gradient(135deg, #ffd700, #191970);">
                                                <div class="products-img">
                                                    <a href="{{ route('shopDetails', $item->id) }}">
                                                        <img style="height:250px"
                                                            src="{{ asset('productImages/' . $item->image) }}"
                                                            class="img-fluid w-100 rounded-top" alt="">
                                                    </a>
                                                </div>
                                                <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                    style="top: 10px; left: 10px;">{{ $item->category_name }}</div>
                                                <div
                                                    class="p-4 text-white border border-secondary border-top-0 rounded-bottom">
                                                    <h4 class="text-white">{{ $item->name }}</h4>
                                                    <p>{{ Str::words($item->description, 10, '...') }}</p>

                                                    <div class="d-flex flex-lg-wrap">
                                                        <p class="text-white fs-5 fw-bold mb-2">{{ $item->price }} MMK</p>
                                                        <form action="{{ route('addToCart') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="productID" value="{{ $item->id }}">
                                                            <input type="hidden" name="qty" value="1"> <!-- Default quantity -->
                                                            <button type="submit" class="btn border border-secondary rounded-pill px-3 text-primary">
                                                                <i class="fa fa-shopping-bag me-2 text-primary"></i> Add to cart
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    @endforeach
                                    <div class="col-12">
                                        <div class="pagination d-flex justify-content-center mt-5">
                                            {{ $products->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Shop End-->

        <!-- Back to Top -->
        <a href="#" class="btn border-3 border-primary rounded-circle back-to-top"><i
                class="fa fa-arrow-up"></i></a>
    @endsection
