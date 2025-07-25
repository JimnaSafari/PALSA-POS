@extends('admin.layouts.master')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid">
        <div class="row">
        @if (auth()->user()->role == 'superadmin')
            <div class="col-3">
                <div class="card shadow mb-4 ">
                    <div class="card-header py-3">
                        <div class="">
                            <div class="">
                                <h6 class="m-0 font-weight-bold text-dark">Add Payment</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('paymentCreate') }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Card Type</label>
                                <select name="paymentType" id=""
                                    class="form-control @error('paymentType') is-invalid @enderror">
                                    <option value="">Choose your payment...</option>
                                    <option value="KBZPay">KBZ Pay</option>
                                    <option value="KBZ">KBZ Account</option>
                                    <option value="WPay">Wave Pay</option>
                                    <option value="YOMA">YOMA Account</option>
                                    <option value="AYA">AYA Account</option>
                                    <option value="AYAPay">AYA Pay</option>
                                    <option value="CB">CB Account</option>
                                    <option value="CBPay">CB Pay</option>
                                    <option value="APay">A Pay</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Card Number...</label>
                                <input type="text" name="card_number" value="{{ old('card_number') }}"
                                    class="form-control @error('card_number') is-invalid @enderror"
                                    id="exampleFormControlInput1" placeholder="number...">
                                @error('card_number')
                                    <small class="invalid-feedback">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Cardholder Name...</label>
                                <input type="text" name="cardholder_name" value="{{ old('cardholder_name') }}"
                                    class="form-control @error('cardholder_name') is-invalid @enderror"
                                    id="exampleFormControlInput1" placeholder="name...">
                                @error('cardholder_name')
                                    <small class="invalid-feedback">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <input type="submit" value="Create" class="btn btn-primary w-100">
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('adminDashboard') }}" class="btn btn-secondary w-100 text-center">Back</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
            <div class="col">
                <!-- Payment List -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between">
                        <h6 class="m-0 font-weight-bold text-dark">Payment List</h6>
                        <a href="{{ route('paymentList') }}"></a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr class="text-center text-white" >
                                        <th>Invoice ID</th>
                                        <th>Card Type</th>
                                        <th>Card Number</th>
                                        <th>Cardholder Name</th>
                                        @if (auth()->user()->role == 'superadmin')
                                            <th>Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data as $item)
                                    @csrf
                                        <tr class="text-center text-white" >
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->type }}</td>
                                            <td>{{ $item->account_number }}</td>
                                            <td>{{ $item->account_name }}</td>
                                            @if (auth()->user()->role == 'superadmin')
                                            <td class="col-2">
                                                <a href="{{ route('paymentEdit', $item->id) }}"><i
                                                        class="fa-solid fa-pen-to-square btn btn-secondary"></i></a>
                                                <a href="{{ route('paymentDelete', $item->id) }}"><i
                                                        class="fa-solid fa-trash-can btn btn-danger"></i></a>
                                            </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <span class="d-flex justify-content-end">{{$data ->links()}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
@endsection


