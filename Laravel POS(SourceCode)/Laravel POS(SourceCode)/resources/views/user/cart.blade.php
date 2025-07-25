@extends('user.layouts.master')
@section('content')
 <!-- Single Page Header start -->
 <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Order List</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="{{route('userDashboard')}}">Home</a></li>
            <li class="breadcrumb-item "><a href="#">Shop</a></li>
            <li class="breadcrumb-item active text-white">Order</li>
        </ol>
    </div>

<!-- Single Page Header End -->

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

    <!-- Cart Page Start -->
    <div class="container-fluid py-1">
        <div class="container py-5">
            <div class="table-responsive">
                <table class="table" id="dataTable">
                    <thead>
                      <tr class="text-white fw-bold">
                        <th scope="col">Products</th>
                        <th scope="col">Name</th>
                        <th scope="col">Price(MMK)</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Total(MMK)</th>
                        <th scope="col">Handle</th>
                      </tr>
                    </thead>
                    <tbody class="text-dark fw-bold">
                        <input type="hidden" name="" value="{{ auth()->user()->id }}" class="userId">
                        @foreach ($cart as $item)
                        <tr>
                            <input type="hidden" name="" value="{{ $item->product_id }}" class="productId">

                            <th scope="row">
                                <div class="d-flex align-items-center">
                                    <img src="{{asset('productImages/'.$item->image)}}" class="img-fluid me-5 rounded-circle" style="width: 80px; height: 80px;" alt="">
                                </div>
                            </th>
                            <td>
                                <p class="text-white mb-0 mt-4">{{ $item->name }}</p>
                            </td>
                            <td>
                                <p class="text-white mb-0 mt-4" id="price">{{ $item->price }}</p>
                            </td>
                            <td>
                                <div class="input-group quantity mt-4" style="width: 100px;">
                                    <div class="input-group-btn">
                                        <button class="btn btn-sm btn-minus rounded-circle bg-light border" >
                                        <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                    <input type="text" class="form-control form-control-sm text-center border-0" id="qty" value="{{ $item->qty }}">
                                    <div class="input-group-btn">
                                        <button class="btn btn-sm btn-plus rounded-circle bg-light border">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="text-white mb-0 mt-4" id="eachTotal">{{ $item->price * $item ->qty }}</p>
                            </td>
                            <td>
                                <input type="hidden" id="cartId" value="{{ $item->id}}">
                                <button class="btn btn-md  rounded-circle bg-light border mt-4 btn-remove" >
                                    <i class="fa fa-times text-danger"></i>
                                </button>
                            </td>

                        </tr>
                        @endforeach

                    </tbody>
                </table>

            </div>

            <div class="row g-4 justify-content-end">
                <div class="col-8"></div>
                <div class="col-sm-8 col-md-7 col-lg-6 col-xl-4">
                    <div class="bg-light rounded">
                        <div class="p-4">
                            <h1 class="display-6 mb-4">Cart <span class="fw-normal">Total</span></h1>
                            <div class="d-flex justify-content-between mb-4">
                                <h5 class="mb-0 me-4" >Subtotal:</h5>
                                <p class="mb-0" id="subTotal">{{ $totalPrice }} MMK</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <h5 class="mb-0 me-4">Deli fees:</h5>
                                <div class="">
                                    <p class="mb-0">500 MMK</p>
                                </div>
                            </div>

                        </div>
                        <div class="py-4 mb-4 border-top border-bottom d-flex justify-content-between">
                            <h5 class="mb-0 ps-4 me-4" >Total:</h5>
                            <p class="mb-0 pe-4" id="finalFee">{{ $totalPrice + 500 }} MMK</p>
                        </div>

                    </div>

                        <button id="proceedCheckOut" @if (count($cart)== 0) disabled @endif
                        class="btn border-secondary rounded-pill px-4 py-3 text-white text-uppercase mb-4 ms-4"
                        type="button">Payment Confirm</button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Cart Page End -->
@endsection

@section('js-section')
    <script>
        $(document).ready(function(){
            //when plus button click
            $('.btn-plus').click(function(){
                // console.log('btn plus clicked');
                $parentNode = $(this).parents("tr");
                $price = $parentNode.find("#price").text().replace("MMK","");
                $qty = $parentNode.find('#qty').val();
                // console.log($price, $qty);
                $totalPrice = $qty * $price;
                $parentNode.find('#eachTotal').html( $totalPrice + 'MMK' );
                finalCalculation();

            })
            //when minus button click
            $('.btn-minus').click(function(){
                $parentNode = $(this).parents("tr");
                $price = $parentNode.find("#price").text().replace("MMK","");
                $qty = $parentNode.find('#qty').val();
                // console.log($price, $qty);
                $totalPrice = $qty * $price;
                $parentNode.find('#eachTotal').html( $totalPrice + 'MMK' );
                finalCalculation();

            })

            //when btn remove click
            $(".btn-remove").click(function(){
                $parentNode = $(this).parents("tr");
                $cartId = $parentNode.find("#cartId").val();

                // console.log($productId);
                $deleteData ={
                    'cartId' : $cartId
                };

                $.ajax({
                    type : 'get',
                    url  : 'remove/cart',
                    data : $deleteData,
                    dataType : 'json',
                    success : function(response){
                        if(response.message == 'success'){
                            location.reload();
                        }
                        // console.log(response);

                    }
                });

            })

        //order process
        $('#proceedCheckOut').click(function(){
            $orderList =[]
            $orderCode = Math.floor(Math.random() * 10000000)//random value
            $userId = $(".userId").val() * 1;
            // $totalPrice = $('#finalFee').text().replace("MMK","") * 1;


            $(" #dataTable tbody tr ").each(function( item, row){
                    $productId = $(row).find('.productId').val();
                    $qty = $(row).find('#qty').val() * 1;
                    $totalPrice = $(row).find('#eachTotal').text().replace("MMK", "") * 1;

                    $orderList.push({
                        'user_id' :$userId,
                        'product_id' : $productId,
                        'order_code' : 'POS' + $orderCode,
                        'total_price' : $totalPrice,
                        'qty' : $qty

                    })
            })
             $.ajax({
                type: 'get',
                url : 'order',
                data : Object.assign({},$orderList),
                dataType : 'json',
                success : function(response){
                    if(response.message == 'success'){
                        location.href = "userPayment"
                    }
                }
             })
            //  console.log('send success');
        })
        function finalCalculation(){
                $totalPrice = 0;
                $(" #dataTable tbody tr ").each(function( item, row){
                    $totalPrice += Number( $(row).find("#eachTotal").text().replace("MMK",""));
                })

            // console.log($totalPrice);
            $("#subTotal").html( `${$totalPrice} MMK `)
            $("#finalFee").html( `${$totalPrice+500} MMK`)

            }
        })
    </script>
@endsection
