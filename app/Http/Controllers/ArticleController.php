<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Resources\ArticleShow;

use App\Models\User;
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
        $articles = Article::latest()->paginate(2);
        // http://127.0.0.1:8000/api/articles/page=3

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
            'title' => 'required|string',
            'body' => 'required', 
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        $user = auth()->user();

       
        $article = $user->articles()->create([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return response()->json([
            "success" => true,
            "message" => "Article successfully added!",
            "data" => $article
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
        $article = Article::with('comments')->find($id);

        return response()->json([
            "success" => true,
            "message" => "Article berhasil ditampilkan!",
            "data" => new ArticleShow($article) 
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
            'title' => 'required|string',
            'body' => 'required', 
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        $article = Article::where('id', $id)->update([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        $user = auth()->user();

        $article = Article::find($id);
        if ($user->id != $article->user_id) {
            return response()->json([
                "success" => false,
                "message" => "Kamu bukan pemilik artikel",
            ], 403);
        }
        $article->title = $request->title;
        $article->body = $request->body;
        $article->save();

        return response()->json([
            "success" => true,
            "message" => "Article successfully updated!",
            "data" => $article
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
        $user = auth()->user();
        $article = Article::find($id);
        if ($user->id != $article->user_id) {
            return response()->json([
                "success" => false,
                "message" => "Kamu bukan pemilik artikel",
            ], 403);
        }
        $article->delete();

        return response()->json([
            "success" => true,
            "message" => "Article successfully deleted!",
            "data" => $article
        ]);
    }
}
