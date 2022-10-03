<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Rating;
use App\Models\Movie;

class RatingController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'rate' => 'required|numeric|min:0|max:5'  
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        $rate = Rating::create([
            'user_id' => Auth::user()->id,
            'rate' => $request->rate,
            'movie_id' => $request->movie,
        ]);

        return response()->json([
            "success" => true,
            "message" => "Rating successfully added!",
            "data" => $rate
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
        //
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
        //
        $validator = Validator::make($request->all(), [
            'rate' => 'required|numeric|min:0|max:5'  
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        $rate = Rating::where('id', $id)->update([
            'user_id' => Auth::user()->id,
            'rate' => $request->rate,
            'movie_id' => $request->movie,
        ]);

        // $user = auth()->user();

        // $rate = Rating::find($id);
        // if ($user->id != $rate->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik rate",
        //     ], 403);
        // }
        // $rate->rate = $request->rate;
        // $rate->save();

        return response()->json([
            "success" => true,
            "message" => "rating successfully updated!",
            "data" => $rate
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
         $rate = Rating::find($id);
         // if ($user->id != $article->user_id) {
         //     return response()->json([
         //         "success" => false,
         //         "message" => "Kamu bukan pemilik artikel",
         //     ], 403);
         // }
         $rate->delete();
 
         return response()->json([
             "success" => true,
             "message" => "rating successfully deleted!",
             "data" => $rate
         ]);
    }
}
