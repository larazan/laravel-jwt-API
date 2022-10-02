<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Person;
use App\Models\Comment;

class CommentController extends Controller
{
    //
    public function store(Request $request, $person_id)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required', 
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        $user = auth()->user();

        $comment = $user->comments()->create([
            'user_id' => Auth::user()->id,
            'person_id' => $person_id,
            'body' => $request->body,
        ]);

        return response()->json([
            "success" => true,
            "message" => "Comment successfully added!",
            "data" => $comment
        ]);
    }

    public function reply(Request $request, $person_id, $comment_id)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required', 
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        $user = auth()->user();
        $comment = Comment::find($comment_id);
        $comment = $user->comments()->create([
            'user_id' => Auth::user()->id,
            'person_id' => $person_id,
            'body' => $request->body,
            'comment_id' => $comment->comment_id ? $comment->comment_id : $comment->id
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

        // $comment = Comment::where('id', $comment_id)->update([
        //     'body' => $request->body,
        // ]);
        
        $user = auth()->user();
        $comment = Comment::find($comment_id);
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
        $comment = Comment::find($comment_id);
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
