<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use RealRashid\SweetAlert\Facades\Alert;

class ProductController extends Controller
{
    //product list page
    public function list(){

        $products = Product::when(request('searchKey'),function($query){
                            $query->whereAny(['name','price','count'],'like','%'.request('searchKey').'%');
                            })
                            ->paginate(3);

        return view('admin.product.list',compact('products'));
    }

    //create product page
    public function createPage(){
        $categories = Category::get();
        return view('admin.product.create',compact('categories'));
    }

    //product create
    public function productCreate(Request $request){
        // dd($request->all());
        $this->validationCheck($request,"create");

        // dd($this->requestProductData($request));
        $data = $this->requestProductData($request);

        // dd($data);

        //check image
        if($request->hasFile('image')){
            $fileName = uniqid() . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path(). '/productImages/' , $fileName);
            $data['image'] = $fileName;
        }
        Product::create($data);

        Alert::success('Insert Success', 'Category Inserted Successfully....');
        return to_route('productList');
    }

       //product delete
    public function delete($id){
        Product::where('id',$id)->delete();

        Alert::success('Delete Success', 'Product Deleted Successfully....');
        return back();
    }

    //details product
    public function details($id){
        $data = Product::select('products.id','products.name','products.price',
                                'products.description','products.count','products.category_id',
                                'products.image','categories.name as category_name')
                        ->leftJoin('categories','products.category_id','categories.id')
                        ->where('products.id',$id)->first();

            // dd($data->toArray());
            return view('admin.product.details',compact('data'));
        }

    //edit product
    public function edit($id){
    $products = Product::select('products.id','products.name','products.price',
                                'products.purchase_price','products.description',
                                'products.count','products.category_id',
                                'products.image','categories.name as category_name')
                    ->leftJoin('categories','products.category_id','categories.id')
                    ->where('products.id',$id)->first();
        $categories = Category::get();
        // dd($products->toArray());
        return view('admin.product.edit',compact('products','categories'));
    }

    //update products
    public function update(Request $request){
        // dd($request->all());
        $this->validationCheck($request,"update");

        $data = $this ->requestProductData($request);

        if ($request->hasFile('image')) {
            $oldImageName = $request->oldImage;
            if (file_exists(public_path('productImages/' . $request->oldImage))) {
                unlink(public_path('productImages/' . $request->oldImage));
            }

            $fileName = uniqid() . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path() . '/productImages/', $fileName);
            $data['image'] = $fileName;
        } else {
            $data['image'] = $request->oldImage;
        }

        Product::where('id', $request->productID)->update($data);
        Alert::success('Update Success', 'Product Updated Successfully....');
        return to_route('productList');

    }

    //create | update validation check
    private function validationCheck($request,$action){
//  dd($request->all());
        if (!$request->has('productID')) {
            dd('Error: productID not found in request'); // Debugging
        }


        $rules = [
            'name'  => ['required', 'unique:products,name,' . $request->productID],
            'price' => 'required|numeric',
            'purchaseprice' => 'required|numeric',
            'categoryName' => 'required|exists:categories,id',
            'count' => ['required', 'integer', 'max:100'],
            'description' => 'required',
        ];

        $rules['image'] = $action == "create"
        ? ['required', 'mimes:png,jpeg,svg,gif,bmp,webp']
        : ['mimes:png,jpeg,svg,gif,bmp,webp'];

        $request->validate($rules);


    }

    //request prodcut data
    private function requestProductData($request){
        return[
            'name' =>$request->name,
            'price' =>$request->price,
            'purchase_price' =>$request->purchaseprice,
            'description' =>$request->description,
            'category_id' =>$request->categoryName ,
            'count' =>$request->count,
            'image' => $request->image,
        ];


    }

}
