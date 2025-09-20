<?php

namespace App\Http\Controllers\Admin;


use view;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;

class PaymentController extends Controller
{
    //Payment Create Page
    public function createPage(){
        return view('admin.payment.create');
    }

    //create payment data
    public function create(Request $request){
        // dd($request->all());
        $validator = $request->validate([
            'paymentType' => 'required',
            'card_number' => 'required',
            'cardholder_name' => 'required',
        ]);
        Payment::create([
            'type' => $request->paymentType,
            'account_number' => $request->card_number,
            'account_name' => $request->cardholder_name,

        ]);
        Alert::success('Payment Success', 'Payment Added Successfully....');
        return redirect()->route('paymentList');
    }

    //list payment
    public function list(){
        $data = Payment::paginate(3);
        // dd($payments);
        return view('admin.payment.create', compact('data'));
    }

    //delete payment
    public function  delete($id){
        Payment::where('id',$id)->delete();
        Alert::success('Delete Success', 'Payment Deleted Successfully....');
        return back();
    }

    //edit payment
    public function edit($id){
        // dd($id);

        $payment = Payment::where('id',$id)->first();
        return view('admin.payment.edit', compact('payment'));

        // dd($payments->toArray());
    }

    //update payment
    public function update(Request $request){
        $validator = $request->validate([
            'paymentType' => ['required'],
            'card_number' => 'required',
            'cardholder_name' => 'required',
        ]);
        $payment = Payment::find($request->paymentID);
        $payment->update([
            'type' => $request->paymentType,
            'account_number' => $request->card_number,
            'account_name' => $request->cardholder_name,
        ]);

        Alert::success('Update Success', 'Payment Updated Successfully....');
        return redirect()->route('paymentList');
    }
}
