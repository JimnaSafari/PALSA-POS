@extends('admin.layouts.master')

@section('content')
    <!-- Begin Page Content -->
    <div class="container-fluid ">
        <!-- DataTales Example -->
        <div class="card shadow mb-4 col-5 offset-3">
            <div class="card-header py-3">
                <div class="">
                    <div class="">
                        <h6 class="m-0 font-weight-bold text-dark">
                            Payment List Page
                        </h6>
                    </div>
                </div>
            </div>
            <form action="{{ route('paymentUpdate') }}" method="post">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Payment Type</label>
                        <input type="hidden" name="paymentID" value="{{ $payment->id }}">
                        <select name="paymentType" id=""
                            class="form-control @error('paymentType') is-invalid @enderror">
                            <option value="">Choose Payment Type...</option>
                            <option value="KBZPay" @if (old('paymentType', $payment->type) == 'KBZPay') selected @endif>KBZ Pay
                            </option>
                            <option value="KBZ" @if (old('paymentType', $payment->type) == 'KBZ') selected @endif>KBZ
                                Account</option>
                            <option value="WPay" @if (old('paymentType', $payment->type) == 'WPay') selected @endif>Wave
                                Pay</option>
                            <option value="YOMA" @if (old('paymentType', $payment->type) == 'YOMA') selected @endif>YOMA
                                Account</option>
                            <option value="AYA" @if (old('paymentType', $payment->type) == 'AYA') selected @endif>AYA
                                Account</option>
                            <option value="AYAPay" @if (old('paymentType', $payment->type) == 'AYAPay') selected @endif>AYA Pay
                            </option>
                            <option value="CB" @if (old('paymentType', $payment->type) == 'CB') selected @endif>CB
                                Account</option>
                            <option value="CBPay" @if (old('paymentType', $payment->type) == 'CBPay') selected @endif>CB Pay
                            </option>
                            <option value="APay" @if (old('paymentType', $payment->type) == 'APay') selected @endif>A Pay
                            </option>
                        </select>
                        @error('paymentType')
                            <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Card Number</label>
                        <input type="text" name="card_number"
                            class="form-control @error('card_number') is-invalid @enderror" id="exampleFormControlInput1"
                            placeholder="Number..." value="{{ old('card_number', $payment->account_number) }}">
                        @error('card_number')
                            <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Cardholder Name...</label>
                        <input type="text" name="cardholder_name"
                            class="form-control @error('cardholder_name') is-invalid @enderror"
                            id="exampleFormControlInput1" placeholder="Name..."
                            value="{{ old('cardholder_name', $payment->account_name) }}">
                        @error('cardholder_name')
                            <small class="invalid-feedback">{{ $message }}</small>
                        @enderror
                    </div>
                    {{-- <input type="submit" value="Update" class="btn btn-primary mt-2">
                    <a href="{{route('paymentList')}}"><input type="button" value="Cancel" class="btn bg-dark text-white mt-2"></a> --}}
                    <div class="row">
                        <div class="col-6">
                            <input type="submit" value="Update" class="btn btn-primary w-100">
                        </div>
                        <div class="col-6">
                            <a href="{{ route('paymentList') }}" class="btn btn-secondary w-100 text-center">Back</a>
                        </div>
                    </div>
                </div>
        </div>
        </form>
    </div>

    </div>
    <!-- /.container-fluid -->
@endsection
