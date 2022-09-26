<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

use App\Models\Episode;

class EpisodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $episodes = Episode::get();

        return response()->json([
            "success" => true,
            "message" => "Episode berhasil ditampilkan!",
            "data" => $episodes
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
            'movie_id' => 'required|numeric',
            'season_id' => 'required|numeric',
            'title' => 'required|string',
            'short_description' => 'required|string',
            'release_date' => 'required|numeric',
            'duration' => 'required|numeric',
             // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
             'original' => '',
             'large' => '',
             'small' => '',
            'status' => 'required'
        ]);
       
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        // $user = auth()->user();

        try {
            $image = $request->file('image');

            if ($image) {
                $name = Str::slug($request->name) . '_' . time();
                $fileName = $name . '.' . $image->getClientOriginalExtension();
                $folder = Episode::UPLOAD_DIR;
                $filePath = $image->storeAs($folder . '/original', $fileName, 'public');
                $resizedImage = $this->_resizeImage($image, $fileName, $folder);

                $original = $filePath;
                $large =  $resizedImage['large'];
                $small = $resizedImage['small'];
            } else {
                $original = '';
                $large =  '';
                $small = '';
            }

            $response = Episode::create([
                'movie_id' => $request->movie_id,           
                'season_id' => $request->season_id,
                'title' => $request->title,
                'short_description' => $request->short_description,
                'release_date' => $request->release_date,
                'duration' => $request->duration,
                'status' => $request->status,
                'original' => $original,
                'large' => $large,
                'small' => $small,
            ]);
           
            // unset();

            return response()->json([
                "success" => true,
                "message" => "Episode successfully added!",
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
        $episode = Episode::find($id);

        return response()->json([
            "success" => true,
            "message" => "episode berhasil ditampilkan!",
            "data" => $episode 
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
            'movie_id' => 'required|numeric',
            'season_id' => 'required|numeric',
            'title' => 'required|string',
            'short_description' => 'required|string',
            'release_date' => 'required|numeric',
            'duration' => 'required|numeric',
             // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
             'original' => '',
             'large' => '',
             'small' => '',
            'status' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        $episode = Episode::where('id', $id)->update([
            'movie_id' => $request->movie_id,
            'title' => $request->title,
            'year' => $request->year,
            // 'status' => true
        ]);

        // $user = auth()->user();

        // $episode = Episode::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik episode",
        //     ], 403);
        // }
        // $episode->name = $request->name;
        // $episode->slug = Str::slug($request->name);
        // $episode->save();

        try {
            $image = $request->file('image');
           
            if ($image) {
                 // delete image
			    $this->_deleteImage($id);

                $name = Str::slug($request->name) . '_' . time();
                $fileName = $name . '.' . $image->getClientOriginalExtension();
                $folder = Episode::UPLOAD_DIR;
                $filePath = $image->storeAs($folder . '/original', $fileName, 'public');
                $resizedImage = $this->_resizeImage($image, $fileName, $folder);

                $original = $filePath;
                $large =  $resizedImage['large'];
                $small = $resizedImage['small'];
            } else {
                $original = '';
                $large =  '';
                $small = '';
            }

            $response = Episode::where('id', $id)->update([
                'movie_id' => $request->movie_id,           
                'season_id' => $request->season_id,
                'title' => $request->title,
                'short_description' => $request->short_description,
                'release_date' => $request->release_date,
                'duration' => $request->duration,
                'status' => $request->status,
                'original' => $original,
                'large' => $large,
                'small' => $small,
            ]);
           
            // unset();

            return response()->json([
                "success" => true,
                "message" => "Episode successfully updated!",
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
        $episode = Episode::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik artikel",
        //     ], 403);
        // }
        $episode->delete();

        return response()->json([
            "success" => true,
            "message" => "episode successfully deleted!",
            "data" => $episode
        ]);
    }

    private function _resizeImage($image, $fileName, $folder)
	{
		$resizedImage = [];

		$smallImageFilePath = $folder . '/small/' . $fileName;
		$size = explode('x', Episode::SMALL);
		list($width, $height) = $size;

		$smallImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $smallImageFilePath, $smallImageFile)) {
			$resizedImage['small'] = $smallImageFilePath;
		}
		
		$mediumImageFilePath = $folder . '/medium/' . $fileName;
		$size = explode('x', Episode::MEDIUM);
		list($width, $height) = $size;

		$mediumImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $mediumImageFilePath, $mediumImageFile)) {
			$resizedImage['medium'] = $mediumImageFilePath;
		}

		$largeImageFilePath = $folder . '/large/' . $fileName;
		$size = explode('x', Episode::LARGE);
		list($width, $height) = $size;

		$largeImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $largeImageFilePath, $largeImageFile)) {
			$resizedImage['large'] = $largeImageFilePath;
		}

		// $extraLargeImageFilePath  = $folder . '/xlarge/' . $fileName;
		// $size = explode('x', Episode::EXTRA_LARGE);
		// list($width, $height) = $size;

		// $extraLargeImageFile = Image::make($image)->fit($width, $height)->stream();
		// if (Storage::put('public/' . $extraLargeImageFilePath, $extraLargeImageFile)) {
		// 	$resizedImage['extra_large'] = $extraLargeImageFilePath;
		// }

		return $resizedImage;
	}

    private function _deleteImage($id = null) {
        $episodeImage = Episode::where(['id' => $id])->first();
		$path = 'storage/';
		
       // if (file_exists($path.$episodeImage->extra_large)) {
        //     unlink($path.$episodeImage->extra_large);
		// }
		
		// if (file_exists($path.$episodeImage->large)) {
        //     unlink($path.$episodeImage->large);
        // }

        if (Storage::exists($path.$episodeImage->original)) {
            Storage::delete($path.$episodeImage->original);
		}
		
		if (Storage::exists($path.$episodeImage->medium)) {
            Storage::delete($path.$episodeImage->medium);
        }

        if (Storage::exists($path.$episodeImage->small)) {
            Storage::delete($path.$episodeImage->small);
        }  

        return true;
    }
}
