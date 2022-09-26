<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Models\Season;

class SeasonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $seasons = Season::get();

        return response()->json([
            "success" => true,
            "message" => "Season berhasil ditampilkan!",
            "data" => $seasons
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
            'title' => 'required|string',
            'year' => 'required|numeric',
            // 'status' => 'required'
        ]);
        
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        // $user = auth()->user();

        $season = Season::create([
            'movie_id' => $request->movie_id,
            'title' => $request->title,
            'year' => $request->year,
            // 'status' => 'active'
        ]);

        return response()->json([
            "success" => true,
            "message" => "Season successfully added!",
            "data" => $season
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
        $season = Season::find($id);

        return response()->json([
            "success" => true,
            "message" => "season berhasil ditampilkan!",
            "data" => $season 
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
            'title' => 'required|string',
            'year' => 'required|numeric',
            // 'status' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        $season = Season::where('id', $id)->update([
            'movie_id' => $request->movie_id,
            'title' => $request->title,
            'year' => $request->year,
            // 'status' => true
        ]);

        // $user = auth()->user();

        // $season = Season::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik season",
        //     ], 403);
        // }
        // $season->name = $request->name;
        // $season->slug = Str::slug($request->name);
        // $season->save();

        return response()->json([
            "success" => true,
            "message" => "season successfully updated!",
            "data" => $season
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
        $season = Season::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik artikel",
        //     ], 403);
        // }
        $season->delete();

        return response()->json([
            "success" => true,
            "message" => "season successfully deleted!",
            "data" => $season
        ]);
    }
}
