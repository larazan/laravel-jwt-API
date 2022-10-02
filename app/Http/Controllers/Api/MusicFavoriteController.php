<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\MusicFavorite;

class MusicFavoriteController extends Controller
{
    //
    public function store(Request $request, $music_id)
    {
        $data = [
            'music_id' => $music_id,
            'user_id' => Auth::user()->id
        ];

        $favorite = MusicFavorite::where($data);

        $favAction = '';

        if ($favorite->count() > 0) {
            $favAction = 'dislike';
            $favorite->delete();
        } else {
            $favAction = 'like';
            MusicFavorite::create($data);
        }

        return response()->json([
            "success" => true,
            "message" => $favAction,
            "data" => $data
        ]);
        
    }
}
