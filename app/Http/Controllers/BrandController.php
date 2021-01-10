<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Multipicture;
use Illuminate\Support\Carbon;
use Image;

class BrandController extends Controller
{
    public function AllBrand(){
        $brands = Brand::latest()->paginate(5);

        return view('admin.brand.index', compact('brands'));
    }

    public function StoreBrand(Request $request){
        $validatedData = $request->validate([
            'brand_name' => 'required|unique:brands|min:4|max:255',
            'brand_image' => 'required|unique:brands|mimes:jpg,jpeg,png',
        ],
        [
            
            'brand_image.required' => 'Please input Brand image',            
            
            
            ]);
        $brand_image = $request->file('brand_image');

        // $name_gen = hexdec(uniqid());
        // $img_ext = strtolower($brand_image->getClientOriginalExtension());
        // $img_name = $name_gen.'.'.$img_ext;
        // $up_location = 'image/brand/';
        // $last_img = $up_location.$img_name;
        // $brand_image->move($up_location,$img_name);

        $name_gen = hexdec(uniqid()) .'.'.$brand_image->getClientOriginalExtension();
        Image::make($brand_image)->resize(300,200)->save('image/brand/'.$name_gen);

        $last_img = 'image/brand/'.$name_gen;

        Brand::insert([
            'brand_name' => $request->brand_name,
            'brand_image' => $last_img,
            'created_at' => Carbon::now(),
        ]);

        return Redirect()->route('all.brand')->with('success','Brand Added Successfully');

    }

    public function Edit($id)
    {
        $brand = Brand::find($id);
        return view('admin.brand.edit',compact('brand'));
    }

    public function Update(Request $request, $id)
    {  
        $validatedData = $request->validate([
            'brand_name' => 'required|min:4|max:255',
            'brand_image' => 'required|unique:brands|mimes:jpg,jpeg,png',
        ],
        [
            
            'brand_image.required' => 'Please input Brand image',            
            
        ]);
        
        $old_image = $request->old_image;
        $brand_image = $request->file('brand_image');

        if($brand_image){
        
            $name_gen = hexdec(uniqid());
            $img_ext = strtolower($brand_image->getClientOriginalExtension());
            $img_name = $name_gen.'.'.$img_ext;
            $up_location = 'image/brand/';
            $last_img = $up_location.$img_name;
            $brand_image->move($up_location,$img_name);

            unlink($old_image);
            Brand::find($id)->update([
                'brand_name' => $request->brand_name,
                'brand_image' => $last_img,
                'updated_at' => Carbon::now(),
            ]);

            return Redirect()->route('all.brand')->with('success','Brand Update Successfully');
            
        }else {
            Brand::find($id)->update([
                'brand_name' => $request->brand_name,
                'brand_image' => $last_img,
                'updated_at' => Carbon::now(),
            ]);

            return Redirect()->route('all.brand')->with('success','Brand Update Successfully');
        }        
        
    }

    public function Delete($id)
    {
        $image = Brand::find($id);
        $old_image = $image->brand_image;
        unlink($old_image);

        Brand::find($id)->delete();
        return Redirect()->route('all.brand')->with('success','Brand Delete Successfully');
    }

    public function Multipic()
    {
        $images = Multipicture::all();
        return view('admin.multipic.index', compact('images'));
    }

    public function StoreImg(Request $request)
    {
        $image = $request->file('image');

        foreach($image as $multi_img){

            $name_gen = hexdec(uniqid()).'.'.$multi_img->getClientOriginalExtension();
            
            Image::make($multi_img)->resize(300,300)->save('image/multi/'.$name_gen);
            
            $last_img = 'image/multi'.$name_gen;
            

            Multipicture::insert([
                'image' => $last_img,            
                'created_at' => Carbon::now(),
            ]);
        
        }

        return Redirect()->back()->with('success','Brand Added Successfully');   
    }


}
