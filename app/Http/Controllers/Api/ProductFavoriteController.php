<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\ProductFavorite;

class ProductFavoriteController extends Controller
{
    //
    public function store(Request $request, $product_id)
    {
        $data = [
            'product_id' => $product_id,
            'user_id' => Auth::user()->id
        ];

        $favorite = ProductFavorite::where($data);

        $favAction = '';

        if ($favorite->count() > 0) {
            $favAction = 'dislike';
            $favorite->delete();
        } else {
            $favAction = 'like';
            ProductFavorite::create($data);
        }

        return response()->json([
            "success" => true,
            "message" => $favAction,
            "data" => $data
        ]);
        
    }
}
