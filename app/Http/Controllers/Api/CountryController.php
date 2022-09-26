<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Models\Country;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $countries = Country::get();

        return response()->json([
            "success" => true,
            "message" => "Country berhasil ditampilkan!",
            "data" => $countries
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
            'status' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        // $user = auth()->user();

        $country = Country::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => 'active'
        ]);

        return response()->json([
            "success" => true,
            "message" => "Country successfully added!",
            "data" => $country
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
        $country = Country::find($id);

        return response()->json([
            "success" => true,
            "message" => "country berhasil ditampilkan!",
            "data" => $country 
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
            'status' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        $country = Country::where('id', $id)->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => 'active'
        ]);

        // $user = auth()->user();

        // $country = Country::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik country",
        //     ], 403);
        // }
        // $country->name = $request->name;
        // $country->slug = Str::slug($request->name);
        // $country->save();

        return response()->json([
            "success" => true,
            "message" => "country successfully updated!",
            "data" => $country
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
         $country = Country::find($id);
         // if ($user->id != $article->user_id) {
         //     return response()->json([
         //         "success" => false,
         //         "message" => "Kamu bukan pemilik artikel",
         //     ], 403);
         // }
         $country->delete();
 
         return response()->json([
             "success" => true,
             "message" => "country successfully deleted!",
             "data" => $country
         ]);
    }
}
