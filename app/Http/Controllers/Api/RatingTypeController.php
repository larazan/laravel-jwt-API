<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Models\RateType;

class RatingTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rates = RateType::get();

        return response()->json([
            "success" => true,
            "message" => "RateType berhasil ditampilkan!",
            "data" => $rates
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

        $rate = RateType::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => 'active'
        ]);

        return response()->json([
            "success" => true,
            "message" => "RateType successfully added!",
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
        $rate = RateType::find($id);

        return response()->json([
            "success" => true,
            "message" => "rate berhasil ditampilkan!",
            "data" => $rate 
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

        $rate = RateType::where('id', $id)->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => 'active'
        ]);

        // $user = auth()->user();

        // $rate = RateType::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik rate",
        //     ], 403);
        // }
        // $rate->name = $request->name;
        // $rate->slug = Str::slug($request->name);
        // $rate->save();

        return response()->json([
            "success" => true,
            "message" => "rate successfully updated!",
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
        $rate = RateType::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik artikel",
        //     ], 403);
        // }
        $rate->delete();

        return response()->json([
            "success" => true,
            "message" => "rate successfully deleted!",
            "data" => $rate
        ]);
    }
}
