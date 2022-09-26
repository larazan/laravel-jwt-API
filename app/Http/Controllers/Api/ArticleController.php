<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

use App\Models\Article;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = Article::get();

        return response()->json([
            "success" => true,
            "message" => "Article berhasil ditampilkan!",
            "data" => $articles
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
            'category_id' => 'required|numeric',            
            'user_id' => 'required|numeric',            
            'title' => 'required|string',
            'published_at' => 'required',
            'status' => 'required|string',
            'body' => 'required|string',
            'author' => 'required|string',
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
                $folder = Article::UPLOAD_DIR;
                $filePath = $image->storeAs($folder . '/original', $fileName, 'public');
                $resizedImage = $this->_resizeImage($image, $fileName, $folder);

                $original = $filePath;
                $large =  $resizedImage['large'];
                $medium =  $resizedImage['medium'];
                $small = $resizedImage['small'];
            } else {
                $original = '';
                $large =  '';
                $medium =  '';
                $small = '';
            }

            $response = Article::create([
                'category_id' => $request->category_id,           
                'user_id' => $request->user_id,           
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'rand_id' => $rand,
                'published_at' => $request->published_at,
                'status' => $request->status,
                'body' => $request->body,
                'author' => $request->author,
                'original' => $original,
                'large' => $large,
                'medium' => $medium,
                'small' => $small,
            ]);
            
            // unset();

            return response()->json([
                "success" => true,
                "message" => "Article successfully added!",
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
        $article = Article::find($id);

        return response()->json([
            "success" => true,
            "message" => "article berhasil ditampilkan!",
            "data" => $article 
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
            'category_id' => 'required|numeric',            
            'user_id' => 'required|numeric',            
            'title' => 'required|string',
            'published_at' => 'required',
            'status' => 'required|string',
            'body' => 'required|string',
            'author' => 'required|string',
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

        // $article = Article::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik article",
        //     ], 403);
        // }
        // $article->name = $request->name;
        // $article->slug = Str::slug($request->name);
        // $article->save();

        try {
            $image = $request->file('image');
            $rand = Str::random(18);

            if ($image) {
                // delete image
			    $this->_deleteImage($id);

                $name = Str::slug($request->name) . '_' . time();
                $fileName = $name . '.' . $image->getClientOriginalExtension();
                $folder = Article::UPLOAD_DIR;
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

            $response = Article::where('id', $id)->update([
                'category_id' => $request->category_id,           
                'user_id' => $request->user_id,           
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'rand_id' => $rand,
                'published_at' => $request->published_at,
                'status' => $request->status,
                'body' => $request->body,
                'author' => $request->author,
                'original' => $original,
                'large' => $large,
                'medium' => $medium,
                'small' => $small,
            ]);

            // unset();

            return response()->json([
                "success" => true,
                "message" => "Article successfully added!",
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
        $article = Article::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik artikel",
        //     ], 403);
        // }
        $article->delete();

        return response()->json([
            "success" => true,
            "message" => "article successfully deleted!",
            "data" => $article
        ]);
    }

    private function _resizeImage($image, $fileName, $folder)
	{
		$resizedImage = [];

		$smallImageFilePath = $folder . '/small/' . $fileName;
		$size = explode('x', Article::SMALL);
		list($width, $height) = $size;

		$smallImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $smallImageFilePath, $smallImageFile)) {
			$resizedImage['small'] = $smallImageFilePath;
		}
		
		$mediumImageFilePath = $folder . '/medium/' . $fileName;
		$size = explode('x', Article::MEDIUM);
		list($width, $height) = $size;

		$mediumImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $mediumImageFilePath, $mediumImageFile)) {
			$resizedImage['medium'] = $mediumImageFilePath;
		}

		$largeImageFilePath = $folder . '/large/' . $fileName;
		$size = explode('x', Article::LARGE);
		list($width, $height) = $size;

		$largeImageFile = Image::make($image)->fit($width, $height)->stream();
		if (Storage::put('public/' . $largeImageFilePath, $largeImageFile)) {
			$resizedImage['large'] = $largeImageFilePath;
		}

		// $extraLargeImageFilePath  = $folder . '/xlarge/' . $fileName;
		// $size = explode('x', Article::EXTRA_LARGE);
		// list($width, $height) = $size;

		// $extraLargeImageFile = Image::make($image)->fit($width, $height)->stream();
		// if (Storage::put('public/' . $extraLargeImageFilePath, $extraLargeImageFile)) {
		// 	$resizedImage['extra_large'] = $extraLargeImageFilePath;
		// }

		return $resizedImage;
	}

    private function _deleteImage($id = null) {
        $articleImage = Article::where(['id' => $id])->first();
		$path = 'storage/';
		
        // if (file_exists($path.$articleImage->extra_large)) {
        //     unlink($path.$articleImage->extra_large);
		// }

        if (file_exists($path.$articleImage->large)) {
            unlink($path.$articleImage->large);
        }

        if (Storage::exists($path.$articleImage->original)) {
            Storage::delete($path.$articleImage->original);
		}
		
		if (Storage::exists($path.$articleImage->medium)) {
            Storage::delete($path.$articleImage->medium);
        }

        if (Storage::exists($path.$articleImage->small)) {
            Storage::delete($path.$articleImage->small);
        }        

        return true;
    }
}
