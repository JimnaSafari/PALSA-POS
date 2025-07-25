<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class ProfileController extends Controller
{
    //profile details
    public function profileDetails(){
           return view('admin.profile.details');
    }

    //create admin account
    public function createAdminAccount(){
        return view('admin.profile.createAdminAcct');
    }

    //create new admin account
    public function create(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'confirmpassword' => 'required|same:password'
        ]);
        $adminAccount = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'provider' => 'simple'
        ];

            User::create($adminAccount);
            Alert::success('Success', 'New Admin Account Created Successfully....');
            return back();
    }


    //profile update
    public function update(Request $request){
    //    dd($request->all()) ;
        $this->validationCheck($request);
        $adminData = $this -> requestAdminData($request);


        if($request->hasFile('image')){
            //delete old image
            $oldImageName = $request->oldImage;
            if($request->oldImage != null){
                if(file_exists(public_path('adminProfile/'.$request->oldImage))){
                    unlink(public_path('adminProfile/'.$request->oldImage));
                }
            }
            //upload new image
            $fileName = uniqid() . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path(). '/adminProfile/' , $fileName);
            $adminData['profile'] = $fileName;
        }
        else{
            $adminData['profile']= $request->oldImage;
        }


        // dd($adminData);
        User::where('id',Auth::user()->id)->update($adminData);
        Alert::success('Update Success', 'Profile Updated Successfully....');
        return back();

    }

    //direct account Profile
    public function accountProfile($id){
        $account = User::where('id',$id)->first();
        return view('admin.profile.accountProfile',compact('account'));
    }


    //request admin data
    private function requestAdminData($request){

        // dd($request->all()) ;
        $data =[];
        if(Auth::user()->name != null){
            $data['name'] =Auth::user()->provider == 'simple' ? $request->name : Auth::user()->name;
        }else{
            $data['nickname'] =Auth::user()->provider == 'simple' ? $request->name : Auth::user()->name;
        }

        $data['email'] = Auth::user()->provider == 'simple' ? $request->email : Auth::user()->email;
        $data['phone'] =$request->phone;
        $data['address'] =$request->address;

        return $data;
    }

      //create | update validation check
      private function validationCheck($request){
        $rules = [
            'phone' => ['required', 'unique:users,phone,' . Auth::user()->id],
            'address' => 'required',
        ];


        if (Auth::user()->provider == 'simple') {
            $rules['name'] = 'required';
            $rules['email'] = 'required|unique:users,email,' . Auth::user()->id;
        }

        $validator = $request->validate($rules);

    }
}
