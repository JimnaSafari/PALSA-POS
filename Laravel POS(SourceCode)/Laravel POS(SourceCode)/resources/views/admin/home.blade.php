@extends('admin.layouts.master')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <!-- Content Row -->
            <div class="row g-3">

                <div class="col-xl-3 col-md-6 col-sm-12 mb-2">
                    <div class="card border-left-warning shadow h-80 d-flex flex-column" data-toggle="modal" data-target="#outOfStockModal" style="cursor: pointer;">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Products (Out of Stock)
                                    </div>

                                    @if($outofstock->isNotEmpty())
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        Items: {{ $outofstock->count() }}
                                        </div>
                                    @else
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            0
                                        </div>
                                    @endif

                                </div>
                                <div class="col-auto">
                                    <i class="fa-solid fa-2x fa-layer-group"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- to show details -->
                <div class="modal fade" id="outOfStockModal" tabindex="-1" aria-labelledby="outOfStockModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="outOfStockModalLabel">Almost Out of Stock Items</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @if($outofstock->isNotEmpty())
                                <ul class="list-group">
                                    @foreach($outofstock as $item)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $item->name }}
                                            <span class="badge badge-warning">{{ $item->stock }} left</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-center text-muted">No products are running low.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-sm-12 mb-2">
                <a href="{{ route('categoryList') }}" class="text-decoration-none">
                <div class="card border-left-secondary shadow h-80 d-flex flex-column categoryList">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    Category (Count)</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="category-count" data-count="{{ $categoryCount }}">0</div>

                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-2x fa-list"></i>
                            </div>
                        </div>
                    </div>
                </div>
                </a>
            </div>

            <!-- Pending Requests Card Example -->
            <div class="col-xl-3 col-md-6 col-sm-12 mb-2">
                <div class="card border-left-success shadow h-80 d-flex flex-column">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Payment Type</div>

                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="payment-count" data-count="{{ $paymentType }}">0</div>

                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-2x fa-credit-card"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 col-sm-12 mb-2">
                <a href="{{ route('adminList') }}" class="text-decoration-none">
                    <div class="card border-left-primary shadow h-80 d-flex flex-column adminList">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Admin Account</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="admin-count" data-count="{{ $adminCount }}">0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fa-solid fa-2x fa-user-shield"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    </a>
            </div>

        </div>

        <!-- Content Row -->

        <div class="row g-3">
            <!-- Pending Requests Card -->
            <div class="col-xl-3 col-md-6 col-sm-12 mb-2">
                <a href="{{ route('orderListPage') }}" class="text-decoration-none">
                    <div class="card border-left-warning shadow h-80 d-flex flex-column pendingList">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Pending Requests
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="orderpending-count" data-count="{{ $orderPending }}">0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fa-solid fa-2x fa-comment-dots"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Total Sales Card -->
            <div class="col-xl-3 col-md-6 col-sm-12 mb-2">
                <div class="card border-left-secondary shadow h-80 d-flex flex-column">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    Total (Sale)
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="total_sale-count" data-count="{{ $total_sale_amt }}">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-2x fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-xl-3 col-md-6 col-sm-12 mb-2">
                <div class="card border-left-success shadow h-80 d-flex flex-column">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Request Success</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="ordersuccess-count" data-count="{{ $orderSuccess }}">0</div>
                            </div>
                            <div class="col-auto">

                                <i class="fa-solid fa-2x fa-circle-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 col-sm-12 mb-2">
                <a href="{{ route('userList') }}" class="text-decoration-none">
                <div class="card border-left-primary shadow h-80 d-flex flex-column customerList">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Customer Account</div>

                                <div class="h5 mb-0 font-weight-bold text-gray-800" id="user-count" data-count="{{ $userCount }}">0</div>
                            </div>
                            <div class="col-auto">
                                <i class="fa-solid fa-2x fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
                </a>
            </div>
        </div>

        <!-- Chart -->
        <div class="row g-3">
            <div class="col-xl-8 col-lg-7 col-sm-12">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-dark">Sales Overview</h6>
                        <div class="card-body">
                            <div class="chart-area">
                                <canvas id="myAreaChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pie Chart -->
            <div class="col-xl-4 col-lg-5 col-sm-12">
                <div class="bg-white mb-4" style="height: 395px;">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-dark">Top Selling Products</h6>

                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="chart-pie pt-2 pb-2">
                            <canvas id="productSalesChart" width="300" height="260"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-section')

    <script>
        //  <!-- Sales overview chart -->
        document.addEventListener("DOMContentLoaded", function() {
            let salesData = @json($salesOverview);

            let labels = salesData.map(item => item.date);
            let data = salesData.map(item => item.daily_sales);

            var ctx = document.getElementById("myAreaChart").getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: "Daily Sales",
                        data: data,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'day'
                            }
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });


// Product Sales Pie Chart
        document.addEventListener("DOMContentLoaded", function(){
            let productSalesData = @json($topProducts);

            let labels = productSalesData.map(item => item.product_name);
            let data = productSalesData.map(item => item.total_sold);

            var ctx = document.getElementById("productSalesChart").getContext('2d');
            var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                }]

            }
        });
    });

        // Card data count
        document.addEventListener("DOMContentLoaded", function() {
            let countElements = document.querySelectorAll("[data-count]");

            countElements.forEach((element) => {
                let count = 0;
                let target = parseInt(element.dataset.count);

                let counter = setInterval(() => {
                    if (count < target) {
                        count += Math.ceil(target / 100); // Adjust speed
                        element.innerText = count;
                    } else {
                        element.innerText = target;
                        clearInterval(counter);
                    }
                }, 100); // Speed of counting
            });
        });

        //Admin list card route to adminlist page
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelector(".adminList").addEventListener("click", function() {
                window.location.href = "{{ route('adminList') }}";
            });
        });

        //Route to customerList page
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelector(".customerList").addEventListener("click", function() {
                window.location.href = "{{ route('userList') }}";
            });
        });


        //Route to Category List page
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelector(".categoryList").addEventListener("click", function() {
                window.location.href = "{{ route('categoryList') }}";
            });
        });


        //Route to order list page
        document.addEventListener("DOMContentLoaded", function(){
            document.querySelector(".pendingList").addEventListener("click", function(){
                window.location.href = "{{ route('orderListPage') }}";
            });
        });

    </script>
@endsection
