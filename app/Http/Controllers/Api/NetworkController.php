<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

use App\Models\Network;

class NetworkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $networks = Network::get();

        return response()->json([
            "success" => true,
            "message" => "Network berhasil ditampilkan!",
            "data" => $networks
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
            'country' => 'required',
            'site' => 'required',
            'status' => 'required|string',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'original' => '',
            'medium' => '',
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
                $folder = Network::UPLOAD_DIR;
                $filePath = $image->storeAs($folder . '/original', $fileName, 'public');
                $resizedImage = $this->_resizeImage($image, $fileName, $folder);

                $original = $filePath;
                $medium = $resizedImage['medium'];
                $small = $resizedImage['small'];
            } else {
                $original = '';
                $medium = '';
                $small = '';
            }

            $response = Network::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'country' => $request->country,
                'site' => $request->site,
                'status' => $request->status,
                'original' => $original,
                'medium' => $medium,
                'small' => $small,
            ]);
           
            // unset();

            return response()->json([
                "success" => true,
                "message" => "Network successfully added!",
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
        $network = Network::find($id);

        return response()->json([
            "success" => true,
            "message" => "network berhasil ditampilkan!",
            "data" => $network 
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
            'country' => 'required',
            'site' => 'required',
            'status' => 'required|string',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'original' => '',
            'medium' => '',
            'small' => '',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        // $user = auth()->user();

        // $network = Network::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik network",
        //     ], 403);
        // }
        // $network->name = $request->name;
        // $network->slug = Str::slug($request->name);
        // $network->save();

        try {
            $image = $request->file('image');
            $rand = Str::random(18);

            if ($image) {
                // delete image
			    $this->_deleteImage($id);

                $name = Str::slug($request->name) . '_' . time();
                $fileName = $name . '.' . $image->getClientOriginalExtension();
                $folder = Network::UPLOAD_DIR;
                $filePath = $image->storeAs($folder . '/original', $fileName, 'public');
                $resizedImage = $this->_resizeImage($image, $fileName, $folder);

                $original = $filePath;
                $medium = $resizedImage['medium'];
                $small = $resizedImage['small'];
            } else {
                $original = '';
                $medium = '';
                $small = '';
            }

            $response = Network::where('id', $id)->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'country' => $request->country,
                'site' => $request->site,
                'status' => $request->status,
                'original' => $original,
                'medium' => $medium,
                'small' => $small,
            ]);

            // unset();

            return response()->json([
                "success" => true,
                "message" => "Network successfully added!",
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
        $network = Network::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik artikel",
        //     ], 403);
        // }
        $network->delete();

        return response()->json([
            "success" => true,
            "message" => "network successfully deleted!",
            "data" => $network
        ]);
    }

    private function _resizeImage($image, $fileName, $folder)
	{
		$resizedImage = [];

		$smallImageFilePath = $folder . '/small/' . $fileName;
		$size = explode('x', Network::SMALL);
		list($width, $height) = $size;

		$smallImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $smallImageFilePath, $smallImageFile)) {
			$resizedImage['small'] = $smallImageFilePath;
		}
		
		$mediumImageFilePath = $folder . '/medium/' . $fileName;
		$size = explode('x', Network::MEDIUM);
		list($width, $height) = $size;

		$mediumImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $mediumImageFilePath, $mediumImageFile)) {
			$resizedImage['medium'] = $mediumImageFilePath;
		}

		// $largeImageFilePath = $folder . '/large/' . $fileName;
		// $size = explode('x', Network::LARGE);
		// list($width, $height) = $size;

		// $largeImageFile = Image::make($image)->fit($width, $height)->stream();
		// if (Storage::put('public/' . $largeImageFilePath, $largeImageFile)) {
		// 	$resizedImage['large'] = $largeImageFilePath;
		// }

		// $extraLargeImageFilePath  = $folder . '/xlarge/' . $fileName;
		// $size = explode('x', Network::EXTRA_LARGE);
		// list($width, $height) = $size;

		// $extraLargeImageFile = Image::make($image)->fit($width, $height)->stream();
		// if (Storage::put('public/' . $extraLargeImageFilePath, $extraLargeImageFile)) {
		// 	$resizedImage['extra_large'] = $extraLargeImageFilePath;
		// }

		return $resizedImage;
	}

    private function _deleteImage($id = null) {
        $networkImage = Network::where(['id' => $id])->first();
		$path = 'storage/';
		
        // if (file_exists($path.$networkImage->extra_large)) {
        //     unlink($path.$networkImage->extra_large);
		// }

        // if (file_exists($path.$networkImage->large)) {
        //     unlink($path.$networkImage->large);
        // }

        if (Storage::exists($path.$networkImage->original)) {
            Storage::delete($path.$networkImage->original);
		}
		
		if (Storage::exists($path.$networkImage->medium)) {
            Storage::delete($path.$networkImage->medium);
        }

        if (Storage::exists($path.$networkImage->small)) {
            Storage::delete($path.$networkImage->small);
        }        

        return true;
    }
}
