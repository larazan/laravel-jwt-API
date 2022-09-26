<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

use App\Models\Person;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $persons = Person::get();

        return response()->json([
            "success" => true,
            "message" => "Person berhasil ditampilkan!",
            "data" => $persons
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
            'gender_id' => 'required|numeric',
            'bio' => 'required',
            'birth_date' => 'required',
            'birth_location' => 'required|string',
            'nationality' => 'required|string',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'original' => '',
            'large' => '',
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
                $name = Str::slug($request->name) . '_' . time();
                $fileName = $name . '.' . $image->getClientOriginalExtension();
                $folder = Person::UPLOAD_DIR;
                $filePath = $image->storeAs($folder . '/original', $fileName, 'public');
                $resizedImage = $this->_resizeImage($image, $fileName, $folder);

                $original = $filePath;
                $large =  $resizedImage['large'];
                $medium = $resizedImage['medium'];
                $small = $resizedImage['small'];
            } else {
                $original = '';
                $large =  '';
                $medium = '';
                $small = '';
            }

            $response = Person::create([
                'name' => $request->name,           
                // 'rand_id' => $rand, 
                'gender_id' => $request->gender_id,
                'bio' => $request->bio,
                'birth_date' => $request->birth_date,
                'birth_location' => $request->birth_location,
                'nationality' => $request->nationality,
                'original' => $original,
                'large' => $large,
                'medium' => $medium,
                'small' => $small,
            ]);

            // unset();

            return response()->json([
                "success" => true,
                "message" => "Person successfully added!",
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
        $person = Person::find($id);

        return response()->json([
            "success" => true,
            "message" => "person berhasil ditampilkan!",
            "data" => $person 
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
            'gender_id' => 'required|numeric',
            'bio' => 'required',
            'birth_date' => 'required',
            'birth_location' => 'required|string',
            'nationality' => 'required|string',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
            'original' => '',
            'large' => '',
            'medium' => '',
            'small' => '',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        // $user = auth()->user();

        // $person = Person::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik person",
        //     ], 403);
        // }
        // $person->name = $request->name;
        // $person->slug = Str::slug($request->name);
        // $person->save();

        try {
            $image = $request->file('image');
            $rand = Str::random(18);

            if ($image) {
                // delete image
			    $this->_deleteImage($id);

                $name = Str::slug($request->name) . '_' . time();
                $fileName = $name . '.' . $image->getClientOriginalExtension();
                $folder = Person::UPLOAD_DIR;
                $filePath = $image->storeAs($folder . '/original', $fileName, 'public');
                $resizedImage = $this->_resizeImage($image, $fileName, $folder);

                $original = $filePath;
                $large =  $resizedImage['large'];
                $medium = $resizedImage['medium'];
                $small = $resizedImage['small'];
            } else {
                $original = '';
                $large =  '';
                $medium = '';
                $small = '';
            }

            $response = Person::where('id', $id)->update([
                'name' => $request->name,           
                // 'rand_id' => $rand, 
                'gender_id' => $request->gender_id,
                'bio' => $request->bio,
                'birth_date' => $request->birth_date,
                'birth_location' => $request->birth_location,
                'nationality' => $request->nationality,
                'original' => $original,
                'large' => $large,
                'medium' => $medium,
                'small' => $small,
            ]);

            // unset();

            return response()->json([
                "success" => true,
                "message" => "Person successfully added!",
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
         $person = Person::find($id);
         // if ($user->id != $article->user_id) {
         //     return response()->json([
         //         "success" => false,
         //         "message" => "Kamu bukan pemilik artikel",
         //     ], 403);
         // }
         $person->delete();
 
         return response()->json([
             "success" => true,
             "message" => "person successfully deleted!",
             "data" => $person
         ]);
    }

    private function _resizeImage($image, $fileName, $folder)
	{
		$resizedImage = [];

		$smallImageFilePath = $folder . '/small/' . $fileName;
		$size = explode('x', Person::SMALL);
		list($width, $height) = $size;

		$smallImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $smallImageFilePath, $smallImageFile)) {
			$resizedImage['small'] = $smallImageFilePath;
		}
		
		$mediumImageFilePath = $folder . '/medium/' . $fileName;
		$size = explode('x', Person::MEDIUM);
		list($width, $height) = $size;

		$mediumImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $mediumImageFilePath, $mediumImageFile)) {
			$resizedImage['medium'] = $mediumImageFilePath;
		}

		$largeImageFilePath = $folder . '/large/' . $fileName;
		$size = explode('x', Person::LARGE);
		list($width, $height) = $size;

		$largeImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $largeImageFilePath, $largeImageFile)) {
			$resizedImage['large'] = $largeImageFilePath;
		}

		// $extraLargeImageFilePath  = $folder . '/xlarge/' . $fileName;
		// $size = explode('x', Person::EXTRA_LARGE);
		// list($width, $height) = $size;

		// $extraLargeImageFile = Image::make($image)->fit($width, $height)->stream();
		// if (Storage::put('public/' . $extraLargeImageFilePath, $extraLargeImageFile)) {
		// 	$resizedImage['extra_large'] = $extraLargeImageFilePath;
		// }

		return $resizedImage;
	}

    private function _deleteImage($id = null) {
        $personImage = Person::where(['id' => $id])->first();
		$path = 'storage/';
		
        // if (file_exists($path.$personImage->extra_large)) {
        //     unlink($path.$personImage->extra_large);
		// }
		
		// if (file_exists($path.$personImage->large)) {
        //     unlink($path.$personImage->large);
        // }

        if (Storage::exists($path.$personImage->original)) {
            Storage::delete($path.$personImage->original);
		}
		
		if (Storage::exists($path.$personImage->medium)) {
            Storage::delete($path.$personImage->medium);
        }

        if (Storage::exists($path.$personImage->small)) {
            Storage::delete($path.$personImage->small);
        }        

        return true;
    }
}
