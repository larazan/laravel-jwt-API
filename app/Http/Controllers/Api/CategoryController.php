<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::get();

        return response()->json([
            "success" => true,
            "message" => "Category berhasil ditampilkan!",
            "data" => $categories
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

        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            // 'status' => 'active'
        ]);

        return response()->json([
            "success" => true,
            "message" => "Category successfully added!",
            "data" => $category
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
        $category = Category::find($id);

        return response()->json([
            "success" => true,
            "message" => "category berhasil ditampilkan!",
            "data" => $category 
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

        $category = Category::where('id', $id)->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            // 'status' => 'active'
        ]);

        // $user = auth()->user();

        // $category = Category::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik category",
        //     ], 403);
        // }
        // $category->name = $request->name;
        // $category->slug = Str::slug($request->name);
        // $category->save();

        return response()->json([
            "success" => true,
            "message" => "category successfully updated!",
            "data" => $category
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
        $category = Category::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik artikel",
        //     ], 403);
        // }
        $category->delete();

        return response()->json([
            "success" => true,
            "message" => "category successfully deleted!",
            "data" => $category
        ]);
    }
}
