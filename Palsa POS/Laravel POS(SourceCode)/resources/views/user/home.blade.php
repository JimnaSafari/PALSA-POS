@extends('user.layouts.master')

@section('content')

    <!-- Hero Start -->
    <div class="container-fluid py-5 mb-5 hero-header">
      <div class="container py-2">
        <div class="row g-5 align-items-center">
          <div class="col-md-12 col-lg-7">
            <h4 class="mb-3 text-secondary">100% Reliable Services</h4>
            <h1 class="mb-5 display-3 text-primary">
              Lorem ipsum, dolor sit amet
            </h1>

          </div>
          <div class="col-md-12 col-lg-5">
            <div
              id="carouselId"
              class="carousel slide position-relative"
              data-bs-ride="carousel"
            >
              <div class="carousel-inner" role="listbox">
                <div class="carousel-item active rounded">
                  <img
                    src="{{ asset('customer/img/banner3.jpg') }}"
                    class="img-fluid w-100 h-100 rounded"
                    alt="First slide"
                  />
                  <a href="{{route('shopList')}}" class="btn-warning px-4 py-2 text-white rounded"
                    >Shop</a
                  >
                </div>
                <div class="carousel-item rounded">
                  <img
                    src="{{ asset('customer/img/banner2.jpg') }}"
                    class="img-fluid w-100 h-100 rounded"
                    alt="Second slide"
                  />
                  <a href="{{route('shopList')}}" class="btn-warning px-4 py-2 text-white rounded"
                    >Shop</a
                  >
                </div>
              </div>
              <button
                class="carousel-control-prev"
                type="button"
                data-bs-target="#carouselId"
                data-bs-slide="prev"
              >
                <span
                  class="carousel-control-prev-icon"
                  aria-hidden="true"
                ></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button
                class="carousel-control-next"
                type="button"
                data-bs-target="#carouselId"
                data-bs-slide="next"
              >
                <span
                  class="carousel-control-next-icon"
                  aria-hidden="true"
                ></span>
                <span class="visually-hidden">Next</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Hero End -->

    <!-- Our Products & Services Start-->
    <div class="container-fluid products">
        <div class="container">
            <div class="tab-class text-center text-white">
                <div class="row g-4  bg-gradient-succes">
                    <div class="col-lg-4 text-start">
                        <h1 class="text-white">Our Products & Services</h1>
                        @foreach ($category as $item)

                            <div class="d-flex justify-content-between products-name">
                                <a class=" text-white" href="{{ route('shopList', $item->id) }}"><i class="text-white fa-solid fa-arrow-right"></i> {{ $item->name }}</a>

                            </div>

                        @endforeach
                    </div>
                    <div class="col-lg-4 text-end">
                        <ul class="nav nav-pills d-inline-flex text-center mb-5">
                            <li class="nav-item">
                                <img src="{{ asset('customer/img/best-product-1.jpg') }}"
                                    class="img-fluid bg-secondary rounded"
                                    style="width: 100%; height: 400px; object-fit: cover;">
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-4 text-end">
                        <ul class="nav nav-pills d-inline-flex text-center mb-5">
                            <li class="nav-item">
                            <img src="{{ asset('customer/img/best-product-2.jpg') }}"
                                    class="img-fluid bg-secondary rounded"
                                    style="width: 100%; height: 400px; object-fit: cover;">
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content" >
                    <div id="tab-1" class="tab-pane fade show p-0 active">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="row g-4">
                                    @php
                                        $count = 1;
                                    @endphp
                                    @foreach ($products as $item)
                                        @if ($count <= 4)
                                            <div class="col-md-6 col-lg-4 col-xl-3">
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
                                                    <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                        <h4 class="text-white">{{ $item->name }}</h4>
                                                        <p>{{ Str::words($item->description, 10, '...') }}</p>
                                                        <div class="d-flex justify-content-center flex-lg-wrap">
                                                            <p class="text-white fs-5 fw-bold mb-2"><i
                                                                    class="fa-solid fa-money-bill"></i> {{ $item->price }}
                                                                MMK</p>
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
                                        @endif
                                        @php
                                            $count++;

                                        @endphp
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Our Products & Services End-->

    <!-- Bestsaler Product Start -->
    <div class="container-fluid">
        <div class="container">
            <div class="text-center mx-auto mb-5" style="max-width: 700px;">
                <h1 class="display-4 text-white">Bestseller Products</h1>
                <p class="text-white">Check out our top 3 best-selling products, loved by customers!</p>
            </div>
            @if(!empty($topProducts) && count($topProducts) > 0)
            <div class="row g-4">
                @foreach ($topProducts as $item)
                    <div class="col-lg-6 col-xl-4">
                        <div class="p-4 rounded bg-light">
                            <div class="row align-items-center">
                                <div class="products-img">
                                    <a href="{{ route('shopDetails', $item->id) }}">
                                        <img style="height:250px" src="{{ asset('productImages/' . $item->image) }}"
                                            class="img-fluid w-100 rounded-top" alt="">
                                    </a>
                                </div>
                                <div class="col-6">
                                    <h4>{{ $item->product_name }}</h4>
                                    <h4 class="mb-3">{{ $item->price }} MMK</h4>

                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @else
                <div class="alert alert-warning" role="alert">
                    <p class="text-center text-dark">Not enough Data to show!</p>
                </div>
            @endif
        </div>
    </div>
    <!-- Bestsaler Product End -->


    <!-- Fact Start -->
    <div class="container-fluid ">
        <div class="container">
            <div class="bg-light p-5 rounded">
                <div class="row g-4 justify-content-center">
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter rounded p-5" style="background: linear-gradient(135deg, #ffd700, #191970);">
                            <i class="fa fa-users text-white"></i>
                            <h4 class="text-white">Customers</h4>
                            <h1 class="text-white">{{ $customerCount }}</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter rounded p-5" style="background: linear-gradient(135deg, #ffd700, #191970);">
                            <i class="fa fa-users text-white"></i>
                            <h4 class="text-white">quality of service</h4>
                            <h1 class="text-white">99%</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter rounded p-5" style="background: linear-gradient(135deg, #ffd700, #191970);">
                            <i class="fa fa-users text-white"></i>
                            <h4 class="text-white">quality certificates</h4>
                            <h1 class="text-white">33</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter rounded p-5" style="background: linear-gradient(135deg, #ffd700, #191970);">
                            <i class="fa fa-users text-white"></i>
                            <h4 class="text-white">Available Products</h4>
                            <h1 class="text-white">{{ count($products) }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fact Start -->


    <!-- Tastimonial Start -->
    <div class="container-fluid testimonial ">
        <div class="container ">
            <div class="testimonial-header text-center">
                <h4 class="text-white">Our Testimonial</h4>
                <h1 class="display-5 mb-5 text-white">Our Client Saying!</h1>
            </div>
            <div class="owl-carousel testimonial-carousel">
                @foreach ($rating as $item)
                    <div class="testimonial-item img-border-radius bg-light rounded p-4">
                        <div class="position-relative">
                            <i class="fa fa-quote-right fa-2x text-secondary position-absolute"
                                style="bottom: 30px; right: 0;"></i>
                            <div class="mb-4 pb-4 border-bottom border-secondary">
                                <p class="mb-0">Lorem Ipsum is simply dummy text of the printing Ipsum has been the
                                    industry's standard dummy text ever since the 1500s,
                                </p>
                            </div>
                            <div class="d-flex align-items-center flex-nowrap">
                                <div class="bg-secondary rounded">
                                    @if ($item->profile != null)
                                        <img src="{{ asset('userProfile/' . $item->profile) }}"
                                            class="img-fluid rounded-circle p-3" style="width: 100px; height: 100px;"
                                            alt="">
                                    @else
                                            <img src="{{asset('customer/img/testimonial-1.jpg')}}" class="img-fluid rounded" style="width: 100px; height: 100px;" alt="">
                                    @endif

                                </div>
                                <div class="ms-4 d-block">
                                    <h4 class="text-dark">{{ $item->name }}</h4>
                                    <!-- <p class="m-0 pb-3">Customer</p> -->
                                    <div class="d-flex pe-5">
                                        @php
                                            $stars = number_format($item->count);
                                        @endphp
                                        @for ($i = 1; $i <= $stars; $i++)
                                            <i class="fa fa-star text-secondary"></i>
                                        @endfor

                                        @for ($j = $stars + 1; $j <= 5; $j++)
                                            <i class="fa fa-star"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
    <!-- Tastimonial End -->
@endsection
