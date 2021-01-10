<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Auth;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;



// If use query builder
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function AllCat(){
        $categories = Category::latest()->paginate(5);
        
        // If use query builder
        // $categories = DB::table('category')->latest()->paginate(5);
        // $categories = DB::table('categories')
        //     ->join('users','category.user_id','users.id')
        //     ->select('category.*','users.name')
        //     ->latest()->paginate(5);

        $trashCat = Category::onlyTrashed()->latest()->paginate(3);

        return view('admin.category.index', compact('categories','trashCat'));
    }

    public function AddCat(Request $request){
        $validatedData = $request->validate([
            'category_name' => 'required|unique:category|max:255',            
        ],
        [
            'category_name.required' => 'Please input Category Name',
            'category_name.max' => 'Category less than 255 chars',
        ]);

        Category::insert([
            'category_name' => $request->category_name,
            'user_id' => Auth::user()->id,
            'created_at' => Carbon::now()
        ]);

        // Contoh insert data yang lain 

        // $category = new Category;
        // $category->category_name = $request->category_name;
        // $category->user_id = Auth::user()->id;
        // $category->save();

        // Contoh insert data menggunakan query
        // $data = array();
        // $data['category_name'] = $request->category_name;
        // $data['user_id'] = Auth::user()->id;
        // DB::table('category')->insert($data);

        return Redirect()->back()->with('success','Category Inserted Successfully');
    }

    public function Edit($id)
    {
        $categories = Category::find($id);
        return view('admin.category.edit', compact('categories'));
    }

    public function Update(Request $request, $id)
    {
        $update = Category::find($id)->update([
            'category_name' => $request->category_name,
            'user_id' => Auth::user()->id
        ]);

        return Redirect()->route('all.category')->with('success','Category Update Successfully');
    }

    public function SoftDelete($id)
    {
        $delete = Category::find($id)->delete();
        return Redirect()->route('all.category')->with('success','Category Delete Successfully');
    }

    public function Restore($id)
    {
        $delete = Category::withTrashed()->find($id)->restore();        
        return Redirect()->back()->with('success','Category Restore Successfully');
    }

    public function Pdelete($id)
    {
        $delete = Category::onlyTrashed()->find($id)->forceDelete();        
        return Redirect()->back()->with('success','Category Delete Permanently Successfully');
    }

}

    
    


