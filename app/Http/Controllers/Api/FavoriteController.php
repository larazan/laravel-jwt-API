<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Favorite;

class FavoriteController extends Controller
{
    //
    public function store(Request $request, $person_id)
    {
        $data = [
            'person_id' => $person_id,
            'user_id' => Auth::user()->id
        ];

        $favorite = Favorite::where($data);

        $favAction = '';

        if ($favorite->count() > 0) {
            $favAction = 'dislike';
            $favorite->delete();
        } else {
            $favAction = 'like';
            Favorite::create($data);
        }

        return response()->json([
            "success" => true,
            "message" => $favAction,
            "data" => $data
        ]);
        
    }
}
