<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Models\Character;

class CharacterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $characters = Character::get();

        return response()->json([
            "success" => true,
            "message" => "Character berhasil ditampilkan!",
            "data" => $characters
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
            'person_id' => 'required|numeric',
            'role_id' => 'required|numeric',
            'character_name' => 'required|string',
            // 'status' => 'required'
        ]);
       
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        // $user = auth()->user();

        $character = Character::create([
            'movie_id' => $request->movie_id,
            'person_id' => $request->person_id,
            'role_id' => $request->role_id,
            'character_name' => $request->name,
            // 'status' => 'active'
        ]);
       
        return response()->json([
            "success" => true,
            "message" => "Character successfully added!",
            "data" => $character
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
        $character = Character::find($id);

        return response()->json([
            "success" => true,
            "message" => "character berhasil ditampilkan!",
            "data" => $character 
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
            'person_id' => 'required|numeric',
            'role_id' => 'required|numeric',
            'character_name' => 'required|string',
            // 'status' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        $character = Character::where('id', $id)->update([
            'movie_id' => $request->movie_id,
            'person_id' => $request->person_id,
            'role_id' => $request->role_id,
            'character_name' => $request->name,
            // 'status' => 'active'
        ]);

        // $user = auth()->user();

        // $character = Character::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik character",
        //     ], 403);
        // }
        // $character->name = $request->name;
        // $character->slug = Str::slug($request->name);
        // $character->save();

        return response()->json([
            "success" => true,
            "message" => "character successfully updated!",
            "data" => $character
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
        $character = Character::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik artikel",
        //     ], 403);
        // }
        $character->delete();

        return response()->json([
            "success" => true,
            "message" => "character successfully deleted!",
            "data" => $character
        ]);
    }
}
