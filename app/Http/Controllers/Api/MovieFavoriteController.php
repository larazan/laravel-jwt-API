<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\MovieFavorite;

class MovieFavoriteController extends Controller
{
    //
    public function store(Request $request, $movie_id)
    {
        $data = [
            'movie_id' => $movie_id,
            'user_id' => Auth::user()->id
        ];

        $favorite = MovieFavorite::where($data);

        $favAction = '';

        if ($favorite->count() > 0) {
            $favAction = 'dislike';
            $favorite->delete();
        } else {
            $favAction = 'like';
            MovieFavorite::create($data);
        }

        return response()->json([
            "success" => true,
            "message" => $favAction,
            "data" => $data
        ]);
        
    }
}
