<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class AuthController extends Controller
{
    //change password page
    public function changePasswordPage(){
        return view('admin.password.changePassword');
    }

    public function changePassword(Request $request){
        $validator = $request->validate([
            'oldPassword' => 'required',
            'newPassword' => 'required',
            'confirmPassword' => 'required|same:newPassword',
           ]);

            $dbHashPassword = User::select('password')->where('id',Auth::user()->id)->first();
            $dbHashPassword =$dbHashPassword['password'];
            // dd($dbHashPassword);
            //    dd($dbHashPassword->toArray());
            $userOldPassword = $request->oldPassword;
            if(Hash::check($userOldPassword, $dbHashPassword)){
                $data = [
                'password' => Hash::make($request->newPassword)
                ];

                User::where('id',Auth::user()->id)->update($data);
                Alert::success('Update Success', 'Password Updated Successfully....');
                return back();
            }
    }

    public function resetPasswordPage(){
        return view('admin.password.resetPassword');
    }

    public function resetPassword(Request $request){
        // dd($request);

        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email',$request->email)->first();

        // dd($user);

        if($user){
            $user->password = Hash::make('Password123');
            $user->save();

            Alert::success('success', 'Password has been reset to default (Password123)');
        }

        return back()->with('error','user not found');
    }
}
