<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

use App\Models\Podcast;

class PodcastController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $podcasts = Podcast::get();

        return response()->json([
            "success" => true,
            "message" => "Podcast berhasil ditampilkan!",
            "data" => $podcasts
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
            'description' => 'required|string',
            'status' => 'required|string',
            'audio' => 'required|string',
            'duration' => 'required|numeric',
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
            $audio = $request->file('audio');
            $rand = Str::random(18);

            if ($audio) {
                $name = Str::slug($request->title) . '_' . time();
                $audioname = $name . '.' . $audio->getClientOriginalName();
                $folder = Podcast::UPLOAD_AUDIO;
                $audiopath = $audio->storeAs($folder, $audioname);
            }

            if ($image) {
                $name = Str::slug($request->title) . '_' . time();
                $fileName = $name . '.' . $image->getClientOriginalExtension();
                $folder = Podcast::UPLOAD_DIR;
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

            $response = Podcast::create([
                'user_id' => $request->user_id,           
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'rand_id' => $rand,
                'description' => $request->description,
                'status' => $request->status,
                'audio' => $audiopath,
                'duration' => $request->duration,
                'original' => $original,
                'medium' => $medium,
                'small' => $small,
            ]);
           
            // unset();

            return response()->json([
                "success" => true,
                "message" => "Podcast successfully added!",
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
        $podcast = Podcast::find($id);

        return response()->json([
            "success" => true,
            "message" => "podcast berhasil ditampilkan!",
            "data" => $podcast 
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
            'description' => 'required|string',
            'status' => 'required|string',
            'audio' => 'required|string',
            'duration' => 'required|numeric',
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

        // $podcast = Podcast::find($id);
        // if ($user->id != $podcast->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik podcast",
        //     ], 403);
        // }
        // $podcast->name = $request->name;
        // $podcast->slug = Str::slug($request->name);
        // $podcast->save();

        try {
            $image = $request->file('image');
            $audio = $request->file('audio');
            $rand = Str::random(18);

            if ($audio) {
                // delete audio
			    $this->_deleteAudio($id);
                
                $name = Str::slug($request->title) . '_' . time();
                $audioname = $name . '.' . $audio->getClientOriginalName();
                $folder = Podcast::UPLOAD_AUDIO;
                $audiopath = $audio->storeAs($folder, $audioname);
            }

            if ($image) {
                // delete image
			    $this->_deleteImage($id);

                $name = Str::slug($request->title) . '_' . time();
                $fileName = $name . '.' . $image->getClientOriginalExtension();
                $folder = Podcast::UPLOAD_DIR;
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

            $response = Podcast::where('id', $id)->update([
                'user_id' => $request->user_id,           
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'rand_id' => $rand,
                'description' => $request->description,
                'status' => $request->status,
                'audio' => $audiopath,
                'duration' => $request->duration,
                'original' => $original,
                'medium' => $medium,
                'small' => $small,
            ]);

            // unset();

            return response()->json([
                "success" => true,
                "message" => "Podcast successfully added!",
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
         $podcast = Podcast::find($id);
         // if ($user->id != $podcast->user_id) {
         //     return response()->json([
         //         "success" => false,
         //         "message" => "Kamu bukan pemilik artikel",
         //     ], 403);
         // }
         $podcast->delete();
 
         return response()->json([
             "success" => true,
             "message" => "podcast successfully deleted!",
             "data" => $podcast
         ]);
    }

    private function _resizeImage($image, $fileName, $folder)
	{
		$resizedImage = [];

		$smallImageFilePath = $folder . '/small/' . $fileName;
		$size = explode('x', Podcast::SMALL);
		list($width, $height) = $size;

		$smallImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $smallImageFilePath, $smallImageFile)) {
			$resizedImage['small'] = $smallImageFilePath;
		}
		
		$mediumImageFilePath = $folder . '/medium/' . $fileName;
		$size = explode('x', Podcast::MEDIUM);
		list($width, $height) = $size;

		$mediumImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $mediumImageFilePath, $mediumImageFile)) {
			$resizedImage['medium'] = $mediumImageFilePath;
		}

		// $largeImageFilePath = $folder . '/large/' . $fileName;
		// $size = explode('x', Podcast::LARGE);
		// list($width, $height) = $size;

		// $largeImageFile = Image::make($image)->fit($width, $height)->stream();
		// if (Storage::put('public/' . $largeImageFilePath, $largeImageFile)) {
		// 	$resizedImage['large'] = $largeImageFilePath;
		// }

		// $extraLargeImageFilePath  = $folder . '/xlarge/' . $fileName;
		// $size = explode('x', Podcast::EXTRA_LARGE);
		// list($width, $height) = $size;

		// $extraLargeImageFile = Image::make($image)->fit($width, $height)->stream();
		// if (Storage::put('public/' . $extraLargeImageFilePath, $extraLargeImageFile)) {
		// 	$resizedImage['extra_large'] = $extraLargeImageFilePath;
		// }

		return $resizedImage;
	}

    private function _deleteImage($id = null) {
        $podcastImage = Podcast::where(['id' => $id])->first();
		$path = 'storage/';
		
        // if (file_exists($path.$podcastImage->extra_large)) {
        //     unlink($path.$podcastImage->extra_large);
		// }

        // if (file_exists($path.$podcastImage->large)) {
        //     unlink($path.$podcastImage->large);
        // }

        if (Storage::exists($path.$podcastImage->original)) {
            Storage::delete($path.$podcastImage->original);
		}
		
		if (Storage::exists($path.$podcastImage->medium)) {
            Storage::delete($path.$podcastImage->medium);
        }

        if (Storage::exists($path.$podcastImage->small)) {
            Storage::delete($path.$podcastImage->small);
        }        

        return true;
    }

    private function _deleteAudio($id = null) {
        $podcastFile = Podcast::where(['id' => $id])->first();
		$path = 'storage/';

        if (Storage::exists($path.$podcastFile->audio)) {
            Storage::delete($path.$podcastFile->audio);
        }        

        return true;
    }
}
