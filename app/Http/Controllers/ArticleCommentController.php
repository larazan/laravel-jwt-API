<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Article;
use App\Models\ArticleComment;

class ArticleCommentController extends Controller
{
    //
    public function store(Request $request, $article_id)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required', 
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        $user = auth()->user();

        $comment = $user->articleComments()->create([
            'user_id' => Auth::user()->id,
            'article_id' => $article_id,
            'body' => $request->body,
        ]);

        return response()->json([
            "success" => true,
            "message" => "Comment successfully added!",
            "data" => $comment
        ]);
    }

    public function reply(Request $request, $article_id, $comment_id)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required', 
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        $user = auth()->user();
        $comment = ArticleComment::find($comment_id);
        $comment = $user->articleComments()->create([
            'user_id' => Auth::user()->id,
            'article_id' => $article_id,
            'body' => $request->body,
            'article_comment_id' => $comment->article_comment_id ? $comment->article_comment_id : $comment->id
        ]);

        return response()->json([
            "success" => true,
            "message" => "Comment successfully added!",
            "data" => $comment
        ]);
    }

    public function update(Request $request, $comment_id)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required', 
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        // $comment = ArticleComment::where('id', $comment_id)->update([
        //     'body' => $request->body,
        // ]);
        
        $user = auth()->user();
        $comment = ArticleComment::find($comment_id);
        if ($user->id != $comment->user_id) {
            return response()->json([
                "success" => false,
                "message" => "Kamu bukan pemilik komentar",
            ], 403);
        }
        $comment->body = $request->body;
        $comment->save();

        return response()->json([
            "success" => true,
            "message" => "Comment successfully updated!",
            "data" => $comment
        ]);
    }

    public function destroy($comment_id)
    {
        $user = auth()->user();
        $comment = ArticleComment::find($comment_id);
        if ($user->id != $comment->user_id) {
            return response()->json([
                "success" => false,
                "message" => "Kamu bukan pemilik komentar",
            ], 403);
        }
        $comment->delete();

        return response()->json([
            "success" => true,
            "message" => "Comment successfully deleted!",
            "data" => $comment
        ]);
    }
}
