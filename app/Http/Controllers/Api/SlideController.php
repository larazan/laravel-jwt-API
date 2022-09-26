<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

use App\Models\Slide;

class SlideController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $slides = Slide::get();

        return response()->json([
            "success" => true,
            "message" => "Slide berhasil ditampilkan!",
            "data" => $slides
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
            'user_id' => 'required|numeric',            
            'title' => 'required|string',
            'url' => 'required',
            'position' => 'required',
            'status' => 'required|string',
            'body' => 'required|string',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'original' => '',
            'extra_large' => '',
            'small' => '',
        ]); 
        
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        try {
            $image = $request->file('image');

            if ($image) {
                $name = Str::slug($request->name) . '_' . time();
                $fileName = $name . '.' . $image->getClientOriginalExtension();
                $folder = Slide::UPLOAD_DIR;
                $filePath = $image->storeAs($folder . '/original', $fileName, 'public');
                $resizedImage = $this->_resizeImage($image, $fileName, $folder);

                $original = $filePath;
                $extralarge =  $resizedImage['extra_large'];
                $small = $resizedImage['small'];
            } else {
                $original = '';
                $extralarge =  '';
                $small = '';
            }

            $response = Slide::create([
                'user_id' => $request->user_id,           
                'title' => $request->title,
                'url' => $request->url,
                'position' => $request->position,
                'status' => $request->status,
                'body' => $request->body,
                'original' => $original,
                'extra_large' => $extralarge,
                'small' => $small,
            ]);
           
            // unset();

            return response()->json([
                "success" => true,
                "message" => "Slide successfully added!",
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
        $slide = Slide::find($id);

        return response()->json([
            "success" => true,
            "message" => "slide berhasil ditampilkan!",
            "data" => $slide 
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
            'user_id' => 'required|numeric',            
            'title' => 'required|string',
            'url' => 'required',
            'position' => 'required',
            'status' => 'required|string',
            'body' => 'required|string',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'original' => '',
            'extra_large' => '',
            'small' => '',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        // $user = auth()->user();

        // $slide = Slide::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik slide",
        //     ], 403);
        // }
        // $slide->name = $request->name;
        // $slide->slug = Str::slug($request->name);
        // $slide->save();

        try {
            $image = $request->file('image');
            $rand = Str::random(18);

            if ($image) {
                // delete image
			    $this->_deleteImage($id);

                $name = Str::slug($request->name) . '_' . time();
                $fileName = $name . '.' . $image->getClientOriginalExtension();
                $folder = Slide::UPLOAD_DIR;
                $filePath = $image->storeAs($folder . '/original', $fileName, 'public');
                $resizedImage = $this->_resizeImage($image, $fileName, $folder);

                $original = $filePath;
                $extralarge =  $resizedImage['extra_large'];
                $small = $resizedImage['small'];
            } else {
                $original = '';
                $extralarge =  '';
                $small = '';
            }

            $response = Slide::where('id', $id)->update([
                'user_id' => $request->user_id,           
                'title' => $request->title,
                'url' => $request->url,
                'position' => $request->position,
                'status' => $request->status,
                'body' => $request->body,
                'original' => $original,
                'extra_large' => $extralarge,
                'small' => $small,
            ]);

            // unset();

            return response()->json([
                "success" => true,
                "message" => "Slide successfully added!",
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
        $slide = Slide::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik artikel",
        //     ], 403);
        // }
        $slide->delete();

        return response()->json([
            "success" => true,
            "message" => "slide successfully deleted!",
            "data" => $slide
        ]);
    }

    private function _resizeImage($image, $fileName, $folder)
	{
		$resizedImage = [];

		$smallImageFilePath = $folder . '/small/' . $fileName;
		$size = explode('x', Slide::SMALL);
		list($width, $height) = $size;

		$smallImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $smallImageFilePath, $smallImageFile)) {
			$resizedImage['small'] = $smallImageFilePath;
		}
		
		// $mediumImageFilePath = $folder . '/medium/' . $fileName;
		// $size = explode('x', Slide::MEDIUM);
		// list($width, $height) = $size;

		// $mediumImageFile = Image::make($image)->fit($width, $height)->stream();
		// if (Storage::put('public/' . $mediumImageFilePath, $mediumImageFile)) {
		// 	$resizedImage['medium'] = $mediumImageFilePath;
		// }

		// $largeImageFilePath = $folder . '/large/' . $fileName;
		// $size = explode('x', Slide::LARGE);
		// list($width, $height) = $size;

		// $largeImageFile = Image::make($image)->fit($width, $height)->stream();
		// if (Storage::put('public/' . $largeImageFilePath, $largeImageFile)) {
		// 	$resizedImage['large'] = $largeImageFilePath;
		// }

		$extraLargeImageFilePath  = $folder . '/xlarge/' . $fileName;
		$size = explode('x', Slide::EXTRA_LARGE);
		list($width, $height) = $size;

		$extraLargeImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $extraLargeImageFilePath, $extraLargeImageFile)) {
			$resizedImage['extra_large'] = $extraLargeImageFilePath;
		}

		return $resizedImage;
	}

    private function _deleteImage($id = null) {
        $slideImage = Slide::where(['id' => $id])->first();
		$path = 'storage/';
		
        if (file_exists($path.$slideImage->extra_large)) {
            unlink($path.$slideImage->extra_large);
		}

        // if (file_exists($path.$slideImage->large)) {
        //     unlink($path.$slideImage->large);
        // }

        if (Storage::exists($path.$slideImage->original)) {
            Storage::delete($path.$slideImage->original);
		}
		
		// if (Storage::exists($path.$slideImage->medium)) {
        //     Storage::delete($path.$slideImage->medium);
        // }

        if (Storage::exists($path.$slideImage->small)) {
            Storage::delete($path.$slideImage->small);
        }        

        return true;
    }
}
