<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;

class CategoryController extends Controller
{
    //category list page
    public function list(){
        $data = Category::orderBy('id')->paginate(3);
        return view('admin.category.list', compact('data'));
    }

    //category create page
    public function createPage(){
        return view('admin.category.create');
    }

    //category data
    public function create(Request $request){
        // dd($request->all());
        // to check validate
        $validator = $request->validate([
           'category' => ['required', 'unique:categories,name']
        ],
        [
            'category.required' => 'Please enter a Category Name'

        ]);
        Category::create([
            'name' => $request->category
        ]);

        Alert::success('Insert Success', 'Category Inserted Successfully....');
        return back();
    }

        //delete category
        public function delete($id){
            Category::where('id',$id)->delete();

            Alert::success('Delete Success', 'Category Deleted Successfully....');
            return back();
        }

        //edit category
        public function edit($id){
            $data = Category::where('id',$id)->first();

            return view('admin.category.edit', compact('data'));

            // dd($data->toArray());
        }

        //update category
        public function update(Request $request){

            // dd($request->all());
            $validator = $request->validate([
                'category' => ['required','unique:categories,name,' . $request->categoryID]
            ]);
            Category::where('id',$request->categoryID)->update([
                'name' => $request->category
            ]);

            Alert::success('Update Success', 'Category Updated Successfully....');
            return to_route('categoryList');

            // dd($data->toArray());
        }

}
