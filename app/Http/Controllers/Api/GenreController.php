<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Models\Genre;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $genres = Genre::get();

        return response()->json([
            "success" => true,
            "message" => "Genre berhasil ditampilkan!",
            "data" => $genres
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
            // 'status' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        // $user = auth()->user();

        $genre = Genre::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            // 'status' => 'active'
        ]);

        return response()->json([
            "success" => true,
            "message" => "Genre successfully added!",
            "data" => $genre
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
        $genre = Genre::find($id);

        return response()->json([
            "success" => true,
            "message" => "genre berhasil ditampilkan!",
            "data" => $genre 
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
            // 'status' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        $genre = Genre::where('id', $id)->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            // 'status' => 'active'
        ]);

        // $user = auth()->user();

        // $genre = Genre::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik genre",
        //     ], 403);
        // }
        // $genre->name = $request->name;
        // $genre->slug = Str::slug($request->name);
        // $genre->save();

        return response()->json([
            "success" => true,
            "message" => "genre successfully updated!",
            "data" => $genre
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
        $genre = Genre::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik artikel",
        //     ], 403);
        // }
        $genre->delete();

        return response()->json([
            "success" => true,
            "message" => "genre successfully deleted!",
            "data" => $genre
        ]);
    }
}
