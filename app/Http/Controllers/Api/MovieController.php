<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

use App\Models\Movie;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $movies = Movie::get();

        return response()->json([
            "success" => true,
            "message" => "Movie berhasil ditampilkan!",
            "data" => $movies
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
            'title' => 'required|string',
            'person_id' => 'required|numeric',            
            'user_id' => 'required|numeric',            
            'album' => 'required|string',
            'description' => 'required|string',
            'audio' => 'required|string',
            'duration' => 'required|numeric',
            'country' => 'required|string',
            'status' => 'required|string',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            // 'audio' => 'nullable|file|mimes:audio/mpeg,mpga,mp3,wav,aac',
            'original' => '',
            'medium' => '',
            'small' => '',
        ]); 
       
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        try {
            $image = $request->file('image');
            $rand = Str::random(18);

            if ($image) {
                $name = Str::slug($request->title) . '_' . time();
                $fileName = $name . '.' . $image->getClientOriginalExtension();
                $folder = Movie::UPLOAD_DIR;
                $filePath = $image->storeAs($folder . '/original', $fileName, 'public');
                $resizedImage = $this->_resizeImage($image, $fileName, $folder);

                $original = $filePath;
                $medium =  $resizedImage['medium'];
                $small = $resizedImage['small'];
            } else {
                $original = '';
                $medium =  '';
                $small = '';
            }

            $response = Movie::create([       
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'rand_id' => $rand,
                'person_id' => $request->person_id,           
                'user_id' => $request->user_id,  
                'album' => $request->album,
                'description' => $request->description,
                'duration' => $request->duration,
                'country' => $request->country,
                'status' => $request->status,
                'original' => $original,
                'medium' => $medium,
                'small' => $small,
            ]);
            
            // unset();

            return response()->json([
                "success" => true,
                "message" => "Movie successfully added!",
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
        $movie = Movie::find($id);

        return response()->json([
            "success" => true,
            "message" => "movie berhasil ditampilkan!",
            "data" => $movie 
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
            'title' => 'required|string',
            'person_id' => 'required|numeric',            
            'user_id' => 'required|numeric',            
            'album' => 'required|string',
            'description' => 'required|string',
            'audio' => 'required|string',
            'duration' => 'required|numeric',
            'country' => 'required|string',
            'status' => 'required|string',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            // 'audio' => 'nullable|file|mimes:audio/mpeg,mpga,mp3,wav,aac',
            'original' => '',
            'medium' => '',
            'small' => '',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        // $user = auth()->user();

        // $movie = Movie::find($id);
        // if ($user->id != $movie->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik movie",
        //     ], 403);
        // }
        // $movie->name = $request->name;
        // $movie->slug = Str::slug($request->name);
        // $movie->save();

        try {
            $image = $request->file('image');
            $rand = Str::random(18);

            if ($image) {
                // delete image
			    $this->_deleteImage($id);

                $name = Str::slug($request->title) . '_' . time();
                $fileName = $name . '.' . $image->getClientOriginalExtension();
                $folder = Movie::UPLOAD_DIR;
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

            $response = Movie::where('id', $id)->update([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'rand_id' => $rand,
                'person_id' => $request->person_id,           
                'user_id' => $request->user_id,  
                'album' => $request->album,
                'description' => $request->description,
                'duration' => $request->duration,
                'country' => $request->country,
                'status' => $request->status,
                'original' => $original,
                'medium' => $medium,
                'small' => $small,
            ]);

            // unset();

            return response()->json([
                "success" => true,
                "message" => "Movie successfully added!",
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
        $movie = Movie::find($id);
        // if ($user->id != $movie->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik artikel",
        //     ], 403);
        // }
        $movie->delete();

        return response()->json([
            "success" => true,
            "message" => "movie successfully deleted!",
            "data" => $movie
        ]);
    }

    private function _resizeImage($image, $fileName, $folder)
	{
		$resizedImage = [];

		$smallImageFilePath = $folder . '/small/' . $fileName;
		$size = explode('x', Movie::SMALL);
		list($width, $height) = $size;

		$smallImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $smallImageFilePath, $smallImageFile)) {
			$resizedImage['small'] = $smallImageFilePath;
		}
		
		$mediumImageFilePath = $folder . '/medium/' . $fileName;
		$size = explode('x', Movie::MEDIUM);
		list($width, $height) = $size;

		$mediumImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $mediumImageFilePath, $mediumImageFile)) {
			$resizedImage['medium'] = $mediumImageFilePath;
		}

		$largeImageFilePath = $folder . '/large/' . $fileName;
		$size = explode('x', Movie::LARGE);
		list($width, $height) = $size;

		$largeImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $largeImageFilePath, $largeImageFile)) {
			$resizedImage['large'] = $largeImageFilePath;
		}

		// $extraLargeImageFilePath  = $folder . '/xlarge/' . $fileName;
		// $size = explode('x', Movie::EXTRA_LARGE);
		// list($width, $height) = $size;

		// $extraLargeImageFile = Image::make($image)->fit($width, $height)->stream();
		// if (Storage::put('public/' . $extraLargeImageFilePath, $extraLargeImageFile)) {
		// 	$resizedImage['extra_large'] = $extraLargeImageFilePath;
		// }

		return $resizedImage;
	}

    private function _deleteImage($id = null) {
        $movieImage = Movie::where(['id' => $id])->first();
		$path = 'storage/';
		
        // if (file_exists($path.$movieImage->extra_large)) {
        //     unlink($path.$movieImage->extra_large);
		// }

        if (file_exists($path.$movieImage->large)) {
            unlink($path.$movieImage->large);
        }

        if (Storage::exists($path.$movieImage->original)) {
            Storage::delete($path.$movieImage->original);
		}
		
		if (Storage::exists($path.$movieImage->medium)) {
            Storage::delete($path.$movieImage->medium);
        }

        if (Storage::exists($path.$movieImage->small)) {
            Storage::delete($path.$movieImage->small);
        }        

        return true;
    }
}
