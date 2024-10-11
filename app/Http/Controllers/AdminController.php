<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
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
            $brand->slug = Str::slug($request->name);
            $image = $request->file('image');
            $file_ex  = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.'.'.$file_ex;
            $this->GenerateImage($image,$file_name);
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
                $this->GenerateImage($image,$file_name);
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
            $this->GenerateImage($image, $file_name);
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
                $this->GenerateImage($image,$file_name);
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

    public function products()
    {
        $products = Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.products',compact('products'));
    }

    public function add_products()
    {
        $categories = Category::select('id','name')->orderBy('name')->get();
        $brands = Brand::select('id','name')->orderBy('name')->get();
        return view('admin.add-products',compact('categories','brands'));
    }

    public function store_products(Request $request)
    {
        try
        {
            $request->validate([
                'name' => 'required',
                'slug' => 'required|unique:products,slug',
                'short_description' => 'required',
                'description' => 'required',
                'regular_price' => 'required',
                'sale_price' => 'required',
                'SKU' => 'required',
                'stock_status' => 'required',
                'featured' => 'required',
                'quantity' => 'required',
                'image'=>'mimes:png,jpg,jpeg|max:2048',
                'category_id' => 'required',
                'brand_id' => 'required'
            ]);

            $product = new Product();

            $product->name = $request->name;
            $product->slug = Str::slug($request->name);
            $product->short_description = $request->short_description;
            $product->description = $request->description;
            $product->regular_price = $request->regular_price;
            $product->sale_price = $request->sale_price;
            $product->SKU = $request->SKU;
            $product->stock_status = $request->stock_status;
            $product->featured = $request->featured;
            $product->quantity = $request->quantity;
            $product->category_id = $request->category_id;
            $product->brand_id = $request->brand_id;

            if($request->hasFile('image'))
            {
                $image = $request->file('image');
                $file_ex  = $request->file('image')->extension();
                $file_name = Carbon::now()->timestamp.'.'.$file_ex;
                $this->GenerateProductImage($image,$file_name);
                $product->image = $file_name;
            }

            $gallery_array = array();
            $gallery_images = '';
            $counter = 1;

            if($request->hasFile('images'))
            {
                $allowedFileExtension = ['jpg','png','jpeg'];
                $files = $request->file('images');
                foreach($files as $file){
                    $G_extension = $file->getClientOriginalExtension();
                    $G_check = in_array($G_extension,$allowedFileExtension);
                    if($G_check){
                        $G_fileName = Carbon::now()->timestamp.'-'.$counter.'.'.$G_extension;
                        $this->GenerateProductImage($file,$G_fileName);
                        array_push($gallery_array,$G_fileName);
                        $counter = $counter + 1;
                    } # End If
                } # End For
                $gallery_images = implode(',',$gallery_array);
                $product->images = $gallery_images;
            } # End If
            $product->save();
            return redirect()->route('admin.products')->with('status','Product has been added successfully !!');
        } # End Try
        catch(\Exception $ex){
            return redirect()->back()->with('status','Something went wrong !!')->withInput();
        }

    } # End of store_products

    public function edit_products(Request $request ,$id)
    {
        $product = Product::find($id);
        $categories = Category::select('id','name')->orderBy('name')->get();
        $brands = Brand::select('id','name')->orderBy('name')->get();
        return view('admin.edit-products',compact('categories','brands','product'));
    }

    public function update_products(Request $request, $id)
    {
    try{
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,'.$request->id,
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image'=>'mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required'
        ]);

        $product = Product::find($id);

        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        if($request->hasFile('image'))
            {
                # Replace Image
                if(File::exists(public_path('assets/uploads/product').'/'.$product->image))
                {
                    File::delete(public_path('assets/uploads/product').'/'.$product->image);
                }
                # Replace Image
                if(File::exists(public_path('assets/uploads/product/thumbnail').'/'.$product->image))
                {
                    File::delete(public_path('assets/uploads/product/thumbnail').'/'.$product->image);
                }
                $image = $request->file('image');
                $file_ex  = $request->file('image')->extension();
                $file_name = Carbon::now()->timestamp.'.'.$file_ex;
                $this->GenerateProductImage($image,$file_name);
                $product->image = $file_name;
            } # End If

        $gallery_array = array();
        $gallery_images = '';
        $counter = 1;

        if($request->hasFile('images'))
        {
            foreach(explode(',',$product->images) as $ofile)
            {
                # Replace Image
                if(File::exists(public_path('assets/uploads/product').'/'.$ofile))
                {
                    File::delete(public_path('assets/uploads/product').'/'.$ofile);
                }
                # Replace Image
                if(File::exists(public_path('assets/uploads/product/thumbnail').'/'.$ofile))
                {
                    File::delete(public_path('assets/uploads/product/thumbnail').'/'.$ofile);
                }
            } # End For

            $allowedFileExtension = ['jpg','png','jpeg'];
            $files = $request->file('images');
            foreach($files as $file){
                $G_extension = $file->getClientOriginalExtension();
                $G_check = in_array($G_extension,$allowedFileExtension);
                if($G_check){
                    $G_fileName = Carbon::now()->timestamp.'-'.$counter.'.'.$G_extension;
                    $this->GenerateProductImage($file,$G_fileName);
                    array_push($gallery_array,$G_fileName);
                    $counter = $counter + 1;
                } # End If
            } # End For
            $gallery_images = implode(',',$gallery_array);
            $product->images = $gallery_images;
        } # End If
        $product->update();
        return redirect()->route('admin.products')->with('status','Product has been updated successfully !!');
        } # End Try
        catch(\Exception $ex){
            return redirect()->back()->with('status','Something went wrong !!')->withInput();
        }
    }

    public function delete_products($id)
    {
        $product = Product::find($id);
        if(File::exists(public_path('assets/uploads/product').'/'.$product->image))
        {
            File::delete(public_path('assets/uploads/product').'/'.$product->image);
        }
        if(File::exists(public_path('assets/uploads/product/thumbnail').'/'.$product->image))
        {
            File::delete(public_path('assets/uploads/product/thumbnail').'/'.$product->image);
        }

        # Delete multiple image !
        foreach(explode(',',$product->images) as $ofile)
        {
                if(File::exists(public_path('assets/uploads/product').'/'.$ofile))
            {
                File::delete(public_path('assets/uploads/product').'/'.$ofile);
            }
                if(File::exists(public_path('assets/uploads/product/thumbnail').'/'.$ofile))
            {
                File::delete(public_path('assets/uploads/product/thumbnail').'/'.$ofile);
            }
        } # End For
        $product->delete();
        return redirect()->route('admin.products')->with('status','Product has been deleted successfully !!');

    }






    # Helper Function
    public function GenerateImage($image ,$imageName)
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
    public function GenerateProductImage($image ,$imageName)
    {
        $destinationsPathThumbnail = public_path('assets/uploads/product/thumbnail');
        $destinationsPath = public_path('assets/uploads/product');
        $img = Image::read($image->path());

        $img->cover(540,689,'top');
        $img->resize(540,689,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationsPath.'/'.$imageName);

        $img->resize(104,104,function($constraint){
            $constraint->aspectRatio();
        })->save($destinationsPathThumbnail.'/'.$imageName);
    }

}
