<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;

class RoleChangeController extends Controller
{
    //direct admin list page
    public function adminList()
{

    $searchKey = request('searchKey');

    $query = User::select('id', 'name', 'nickname', 'email','phone','address')
        ->whereIn('role', ['superadmin', 'admin']);

        // dd($query);

    if ($searchKey) {
        $query->where(function ($query) use ($searchKey) {
            $query->where('name', 'like', '%' . $searchKey . '%')
                  ->orWhere('email', 'like', '%' . $searchKey . '%');

        });
    }

    $data = $query->paginate(3);

    // dd($data);

    $userCount = User::where('role','user')->count();

    return view('admin.roleChange.adminList', compact('data','userCount'));
}

    //delete Admin Account
    public function deleteAdminAccount($id){
        User::where('id',$id)->delete();

        Alert::success('Delete Success', 'Admin Account Deleted Successfully....');
        return back();
    }

     //change user Account to Admin

     public function changeAdminRole($id){
        User::where('id',$id)->update(['role'=>'admin']);

        Alert::success('Change Admin Account Success', 'Admin Account Changed Successfully....');
        return back();
    }

      //change Admin Account to User

      public function changeUserRole($id){
        User::where('id',$id)->update(['role'=>'user']);

        Alert::success('Change User Account Success', 'User Account Changed Successfully....');
        return back();
    }


    //direct admin list page
    public function userList()
    {
        $searchKey = request('searchKey');

        $query = User::select('id', 'name', 'nickname', 'email','phone','address')
            ->whereIn('role', ['user']);

        if ($searchKey) {
            $query->where(function ($query) use ($searchKey) {
                $query->where('name', 'like', '%' . $searchKey . '%')
                    ->orWhere('email', 'like', '%' . $searchKey . '%');

            });
        }

        $data = $query->paginate(3);

        $adminCount = User::whereIn('role', ['superadmin', 'admin'])->count();

        return view('admin.roleChange.userList', compact('data','adminCount'));
    }


}
