<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

use App\Models\Music;

class MusicController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $musics = Music::get();

        return response()->json([
            "success" => true,
            "message" => "Music berhasil ditampilkan!",
            "data" => $musics
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
            $audio = $request->file('audio');
            $rand = Str::random(18);

            if ($audio) {
                $name = Str::slug($request->title) . '_' . time();
                $audioname = $name . '.' . $audio->getClientOriginalName();
                $folder = Music::UPLOAD_AUDIO;
                $audiopath = $audio->storeAs($folder, $audioname);
            }

            if ($image) {
                $name = Str::slug($request->title) . '_' . time();
                $fileName = $name . '.' . $image->getClientOriginalExtension();
                $folder = Music::UPLOAD_DIR;
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

            $response = Music::create([       
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'rand_id' => $rand,
                'person_id' => $request->person_id,           
                'user_id' => $request->user_id,  
                'album' => $request->album,
                'description' => $request->description,
                'audio' => $audiopath,
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
                "message" => "Music successfully added!",
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
        $music = Music::find($id);

        return response()->json([
            "success" => true,
            "message" => "music berhasil ditampilkan!",
            "data" => $music 
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

        // $music = Music::find($id);
        // if ($user->id != $music->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik music",
        //     ], 403);
        // }
        // $music->name = $request->name;
        // $music->slug = Str::slug($request->name);
        // $music->save();

        try {
            $image = $request->file('image');
            $audio = $request->file('audio');
            $rand = Str::random(18);

            if ($audio) {
                // delete audio
			    $this->_deleteAudio($id);

                $name = Str::slug($request->title) . '_' . time();
                $audioname = $name . '.' . $audio->getClientOriginalName();
                $folder = Music::UPLOAD_AUDIO;
                $audiopath = $audio->storeAs($folder, $audioname);
            }

            if ($image) {
                // delete image
			    $this->_deleteImage($id);

                $name = Str::slug($request->title) . '_' . time();
                $fileName = $name . '.' . $image->getClientOriginalExtension();
                $folder = Music::UPLOAD_DIR;
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

            $response = Music::where('id', $id)->update([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'rand_id' => $rand,
                'person_id' => $request->person_id,           
                'user_id' => $request->user_id,  
                'album' => $request->album,
                'description' => $request->description,
                'audio' => $audiopath,
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
                "message" => "Music successfully added!",
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
        $music = Music::find($id);
        // if ($user->id != $music->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik artikel",
        //     ], 403);
        // }
        $music->delete();

        return response()->json([
            "success" => true,
            "message" => "music successfully deleted!",
            "data" => $music
        ]);
    }

    private function _resizeImage($image, $fileName, $folder)
	{
		$resizedImage = [];

		$smallImageFilePath = $folder . '/small/' . $fileName;
		$size = explode('x', Music::SMALL);
		list($width, $height) = $size;

		$smallImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $smallImageFilePath, $smallImageFile)) {
			$resizedImage['small'] = $smallImageFilePath;
		}
		
		$mediumImageFilePath = $folder . '/medium/' . $fileName;
		$size = explode('x', Music::MEDIUM);
		list($width, $height) = $size;

		$mediumImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $mediumImageFilePath, $mediumImageFile)) {
			$resizedImage['medium'] = $mediumImageFilePath;
		}

		// $largeImageFilePath = $folder . '/large/' . $fileName;
		// $size = explode('x', Music::LARGE);
		// list($width, $height) = $size;

		// $largeImageFile = Image::make($image)->fit($width, $height)->stream();
		// if (Storage::put('public/' . $largeImageFilePath, $largeImageFile)) {
		// 	$resizedImage['large'] = $largeImageFilePath;
		// }

		// $extraLargeImageFilePath  = $folder . '/xlarge/' . $fileName;
		// $size = explode('x', Music::EXTRA_LARGE);
		// list($width, $height) = $size;

		// $extraLargeImageFile = Image::make($image)->fit($width, $height)->stream();
		// if (Storage::put('public/' . $extraLargeImageFilePath, $extraLargeImageFile)) {
		// 	$resizedImage['extra_large'] = $extraLargeImageFilePath;
		// }

		return $resizedImage;
	}

    private function _deleteImage($id = null) {
        $musicImage = Music::where(['id' => $id])->first();
		$path = 'storage/';
		
        // if (file_exists($path.$musicImage->extra_large)) {
        //     unlink($path.$musicImage->extra_large);
		// }

        // if (file_exists($path.$musicImage->large)) {
        //     unlink($path.$musicImage->large);
        // }

        if (Storage::exists($path.$musicImage->original)) {
            Storage::delete($path.$musicImage->original);
		}
		
		if (Storage::exists($path.$musicImage->medium)) {
            Storage::delete($path.$musicImage->medium);
        }

        if (Storage::exists($path.$musicImage->small)) {
            Storage::delete($path.$musicImage->small);
        }        

        return true;
    }

    private function _deleteAudio($id = null) {
        $musicFile = Music::where(['id' => $id])->first();
		$path = 'storage/';

        if (Storage::exists($path.$musicFile->audio)) {
            Storage::delete($path.$musicFile->audio);
        }        

        return true;
    }
}
