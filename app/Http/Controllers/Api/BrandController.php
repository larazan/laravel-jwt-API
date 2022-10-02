<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

use App\Models\Brand;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brands = Brand::get();

        return response()->json([
            "success" => true,
            "message" => "Brand berhasil ditampilkan!",
            "data" => $brands
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'status' => 'required|string',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'original' => '',
            'small' => '',
        ]); 
       
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        try {
            $image = $request->file('image');
            $rand = Str::random(18);

            if ($image) {
                $name = Str::slug($request->name) . '_' . time();
                $fileName = $name . '.' . $image->getClientOriginalExtension();
                $folder = Brand::UPLOAD_DIR;
                $filePath = $image->storeAs($folder . '/original', $fileName, 'public');
                $resizedImage = $this->_resizeImage($image, $fileName, $folder);

                $original = $filePath;
                $small = $resizedImage['small'];
            } else {
                $original = '';
                $small = '';
            }

            $response = Brand::create([       
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'status' => $request->status,
                'original' => $original,
                'small' => $small,
            ]);
            
            // unset();

            return response()->json([
                "success" => true,
                "message" => "Brand successfully added!",
                "data" => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Err",
                "errors" => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $brand = Brand::find($id);

        return response()->json([
            "success" => true,
            "message" => "brand berhasil ditampilkan!",
            "data" => $brand 
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'status' => 'required|string',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'original' => '',
            'small' => '',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        // $user = auth()->user();

        // $brand = Brand::find($id);
        // if ($user->id != $brand->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik brand",
        //     ], 403);
        // }
        // $brand->name = $request->name;
        // $brand->slug = Str::slug($request->name);
        // $brand->save();

        try {
            $image = $request->file('image');
            $rand = Str::random(18);

            if ($image) {
                // delete image
			    $this->_deleteImage($id);

                $name = Str::slug($request->name) . '_' . time();
                $fileName = $name . '.' . $image->getClientOriginalExtension();
                $folder = Brand::UPLOAD_DIR;
                $filePath = $image->storeAs($folder . '/original', $fileName, 'public');
                $resizedImage = $this->_resizeImage($image, $fileName, $folder);

                $original = $filePath;
                $small = $resizedImage['small'];
            } else {
                $original = '';
                $small = '';
            }

            $response = Brand::where('id', $id)->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'status' => $request->status,
                'original' => $original,
                'small' => $small,
            ]);

            // unset();

            return response()->json([
                "success" => true,
                "message" => "Brand successfully added!",
                "data" => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Err",
                "errors" => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $user = auth()->user();
        $brand = Brand::find($id);
        // if ($user->id != $brand->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik artikel",
        //     ], 403);
        // }
        $brand->delete();

        return response()->json([
            "success" => true,
            "message" => "brand successfully deleted!",
            "data" => $brand
        ]);
    }

    private function _resizeImage($image, $fileName, $folder)
	{
		$resizedImage = [];

		$smallImageFilePath = $folder . '/small/' . $fileName;
		$size = explode('x', Brand::SMALL);
		list($width, $height) = $size;

		$smallImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $smallImageFilePath, $smallImageFile)) {
			$resizedImage['small'] = $smallImageFilePath;
		}
		
		// $mediumImageFilePath = $folder . '/medium/' . $fileName;
		// $size = explode('x', Brand::MEDIUM);
		// list($width, $height) = $size;

		// $mediumImageFile = Image::make($image)->fit($width, $height)->stream();
		// if (Storage::put('public/' . $mediumImageFilePath, $mediumImageFile)) {
		// 	$resizedImage['medium'] = $mediumImageFilePath;
		// }

		// $largeImageFilePath = $folder . '/large/' . $fileName;
		// $size = explode('x', Brand::LARGE);
		// list($width, $height) = $size;

		// $largeImageFile = Image::make($image)->fit($width, $height)->stream();
		// if (Storage::put('public/' . $largeImageFilePath, $largeImageFile)) {
		// 	$resizedImage['large'] = $largeImageFilePath;
		// }

		// $extraLargeImageFilePath  = $folder . '/xlarge/' . $fileName;
		// $size = explode('x', Brand::EXTRA_LARGE);
		// list($width, $height) = $size;

		// $extraLargeImageFile = Image::make($image)->fit($width, $height)->stream();
		// if (Storage::put('public/' . $extraLargeImageFilePath, $extraLargeImageFile)) {
		// 	$resizedImage['extra_large'] = $extraLargeImageFilePath;
		// }

		return $resizedImage;
	}

    private function _deleteImage($id = null) {
        $brandImage = Brand::where(['id' => $id])->first();
		$path = 'storage/';
		
        // if (file_exists($path.$brandImage->extra_large)) {
        //     unlink($path.$brandImage->extra_large);
		// }

        // if (file_exists($path.$brandImage->large)) {
        //     unlink($path.$brandImage->large);
        // }

        if (Storage::exists($path.$brandImage->original)) {
            Storage::delete($path.$brandImage->original);
		}
		
		// if (Storage::exists($path.$brandImage->medium)) {
        //     Storage::delete($path.$brandImage->medium);
        // }

        if (Storage::exists($path.$brandImage->small)) {
            Storage::delete($path.$brandImage->small);
        }        

        return true;
    }
}
