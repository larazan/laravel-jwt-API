<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Like;

class LikeController extends Controller
{
    //
    public function store(Request $request, $comment_id)
    {
        $data = [
            'comment_id' => $comment_id,
            'user_id' => Auth::user()->id
        ];

        $like = Like::where($data);

        $postAction = '';

        if ($like->count() > 0) {
            $postAction = 'dislike';
            $like->delete();
        } else {
            $postAction = 'like';
            Like::create($data);
        }

        return response()->json([
            "success" => true,
            "message" => $postAction,
            "data" => $data
        ]);
        
    }
}
