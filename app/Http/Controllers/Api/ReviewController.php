<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Models\Review;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reviews = Review::get();

        return response()->json([
            "success" => true,
            "message" => "Review berhasil ditampilkan!",
            "data" => $reviews
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
            'body' => 'required|string',
            'user_id' => 'required|numeric',
            'movie_id' => 'required|numeric',
            // 'status' => 'required'
        ]);
        
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        // $user = auth()->user();

        $review = Review::create([
            'body' => $request->body,
            'user_id' => $request->user_id,
            'movie_id' => $request->movie_id,
            // 'status' => 'active'
        ]);
        
        return response()->json([
            "success" => true,
            "message" => "Review successfully added!",
            "data" => $review
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $review = Review::find($id);

        return response()->json([
            "success" => true,
            "message" => "review berhasil ditampilkan!",
            "data" => $review 
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
            'body' => 'required|string',
            'user_id' => 'required|numeric',
            'movie_id' => 'required|numeric',
            // 'status' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        $review = Review::where('id', $id)->update([
            'body' => $request->body,
            'user_id' => $request->user_id,
            'movie_id' => $request->movie_id,
            // 'status' => 'active'
        ]);

        // $user = auth()->user();

        // $review = Review::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik review",
        //     ], 403);
        // }
        // $review->name = $request->name;
        // $review->slug = Str::slug($request->name);
        // $review->save();

        return response()->json([
            "success" => true,
            "message" => "review successfully updated!",
            "data" => $review
        ]);
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
        $review = Review::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik artikel",
        //     ], 403);
        // }
        $review->delete();

        return response()->json([
            "success" => true,
            "message" => "review successfully deleted!",
            "data" => $review
        ]);
    }
}
