<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::get();

        return response()->json([
            "success" => true,
            "message" => "Role berhasil ditampilkan!",
            "data" => $roles
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

        $role = Role::create([
            'name' => $request->name,
            // 'slug' => Str::slug($request->name),
            // 'status' => 'active'
        ]);

        return response()->json([
            "success" => true,
            "message" => "Role successfully added!",
            "data" => $role
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
        $role = Role::find($id);

        return response()->json([
            "success" => true,
            "message" => "role berhasil ditampilkan!",
            "data" => $role 
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

        $role = Role::where('id', $id)->update([
            'name' => $request->name,
            // 'slug' => Str::slug($request->name),
            // 'status' => true
        ]);

        // $user = auth()->user();

        // $role = Role::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik role",
        //     ], 403);
        // }
        // $role->name = $request->name;
        // $role->slug = Str::slug($request->name);
        // $role->save();

        return response()->json([
            "success" => true,
            "message" => "role successfully updated!",
            "data" => $role
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
         $role = Role::find($id);
         // if ($user->id != $article->user_id) {
         //     return response()->json([
         //         "success" => false,
         //         "message" => "Kamu bukan pemilik artikel",
         //     ], 403);
         // }
         $role->delete();
 
         return response()->json([
             "success" => true,
             "message" => "role successfully deleted!",
             "data" => $role
         ]);
    }
}
