<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function brands()
    {
        $brands = Brand::orderBy('id','DESC')->paginate(10);
        return view('admin.brands',compact('brands'));
    }

    public function add_brand()
    {
        return view('admin.add-brand');
    }

    public function store(Request $request)
    {
        try
        {
            $request->validate([
                'name'=>'required',
                'slug'=>'required|unique:brands,slug',
                'image'=>'mimes:png,jpg,jpeg|max:2048'
            ]);

            $brand = new Brand();
            $brand->name = $request->name;
            $brand->slug = Str::slug($request->slug);
            $image = $request->file('image');
            $file_ex  = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_ex;
            $this->GenerateBrandImage($image,$file_name);
            $brand->image = $file_name;
            $brand->save();
            return redirect()->route('admin.brands')->with('status','Brand has been added successfully !!');
        }
        catch(\Exception $ex){
            return redirect()->route('admin.brands')->with('status','Something went wrong !!');
        }

    }

    public function edit_brand($id)
    {
        $brand = Brand::find($id);
        return view('admin.edit-brand',compact('brand'));
    }

    public function update(Request $request ,$id)
    {
        try
        {
            $request->validate([
                'name'=>'required',
                'slug'=>'required|unique:brands,slug,'.$request->id,
                'image'=>'mimes:png,jpg,jpeg|max:2048'
            ]);

            $brand = Brand::find($id);
            $brand->name = $request->name;
            $brand->slug = Str::slug($request->slug);
            if($request->hasFile('image'))
            {
                # Delete the old photo and replace it with the new one

                if(File::exists(public_path('assets/uploads').'/'.$brand->image))
                {
                    File::delete(public_path('assets/uploads').'/'.$brand->image);
                }
                $image = $request->file('image');
                $file_ex  = $request->file('image')->extension();
                $file_name = Carbon::now()->timestamp.'.'.$file_ex;
                $this->GenerateBrandImage($image,$file_name);
                $brand->image = $file_name;
            }
            $brand->save();
            return redirect()->route('admin.brands')->with('status','Brand has been updated successfully !!');
        }
        catch(\Exception $ex){
            return redirect()->route('admin.brands')->with('status','Something went wrong !!');
        }

    }

    public function delete($id)
    {
        $brand = Brand::find($id);
        if(File::exists(public_path('assets/uploads').'/'.$brand->image))
        {
            File::delete(public_path('assets/uploads').'/'.$brand->image);
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status','Brand has been deleted successfully !!');

    }

    public function categories()
    {
        $categories = Category::orderBy('id','DESC')->paginate(10);
        return view('admin.categories',compact('categories'));
    }

    public function add_categories()
    {
        return view('admin.add-categories');
    }


    public function store_categories(Request $request)
    {
        try
        {
            $request->validate([
                'name'=>'required',
                'slug'=>'required|unique:categories,slug',
                'image'=>'mimes:png,jpg,jpeg|max:2048'
            ]);

            $brand = new Category();
            $brand->name = $request->name;
            $brand->slug = Str::slug($request->slug);
            $image = $request->file('image');
            $file_ex  = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_ex;
            $this->GenerateBrandImage($image, $file_name);
            $brand->image = $file_name;
            $brand->save();
            return redirect()->route('admin.categories')->with('status','Category has been added successfully !!');
        }
        catch(\Exception $ex){
            return redirect()->route('admin.categories')->with('status','Something went wrong !!');
        }

    }

    public function edit_categories($id)
    {
        $category = Category::find($id);
        return view('admin.edit-categories',compact('category'));
    }

    public function update_categories(Request $request ,$id)
    {
        try
        {
            $request->validate([
                'name'=>'required',
                'slug'=>'required|unique:categories,slug,'.$request->id,
                'image'=>'mimes:png,jpg,jpeg|max:2048'
            ]);

            $category = Category::find($id);
            $category->name = $request->name;
            $category->slug = Str::slug($request->slug);
            if($request->hasFile('image'))
            {
                # Delete the old photo and replace it with the new one

                if(File::exists(public_path('assets/uploads').'/'.$category->image))
                {
                    File::delete(public_path('assets/uploads').'/'.$category->image);
                }
                $image = $request->file('image');
                $file_ex  = $request->file('image')->extension();
                $file_name = Carbon::now()->timestamp.'.'.$file_ex;
                $this->GenerateBrandImage($image,$file_name);
                $category->image = $file_name;
            }
            $category->save();
            return redirect()->route('admin.categories')->with('status','Category has been updated successfully !!');
        }
        catch(\Exception $ex){
            return redirect()->route('admin.categories')->with('status','Something went wrong !!');
        }

    }

    public function delete_categories($id)
    {
        $category = Category::find($id);
        if(File::exists(public_path('assets/uploads').'/'.$category->image))
        {
            File::delete(public_path('assets/uploads').'/'.$category->image);
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status','Category has been deleted successfully !!');

    }

    public function GenerateBrandImage($image ,$imageName)
    {
        $destinationsPath = public_path('assets/uploads');
        $img = Image::read($image->path());
        $img->cover(124,124,'top');
        $img->resize(124,124,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationsPath.'/'.$imageName);
    }

    function uploadImage($folder,$image)
    {
        $fileExtension = $image->getClientOriginalExtension();
        $fileName = time().rand(1,99).'.'.$fileExtension;
        $image->move($folder,$fileName);

        return $fileName;
    }//end of uploadImage

}
