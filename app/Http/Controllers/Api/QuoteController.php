<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\Quote;

class QuoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $quotes = Quote::get();

        return response()->json([
            "success" => true,
            "message" => "Quote berhasil ditampilkan!",
            "data" => $quotes
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
            'quote' => 'required|string',
            'character' => 'required|string',
            'movie' => 'required|string',
            'year' => 'required|string',
            // 'status' => 'required'
        ]);
        
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        // $user = auth()->user();

        $quote = Quote::create([
            'user_id' => Auth::user()->id,
            'quote' => $request->quote,
            'character' => $request->character,
            'movie' => $request->movie,
            'year' => $request->year,
            'status' => 'active'
        ]);
        
        return response()->json([
            "success" => true,
            "message" => "Quote successfully added!",
            "data" => $quote
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
        $quote = Quote::find($id);

        return response()->json([
            "success" => true,
            "message" => "quote berhasil ditampilkan!",
            "data" => $quote 
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
            'quote' => 'required|string',
            'character' => 'required|string',
            'movie' => 'required|string',
            'year' => 'required|string',
            // 'status' => 'required'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 422);
        }

        $quote = Quote::where('id', $id)->update([
            'user_id' => Auth::user()->id,
            'quote' => $request->quote,
            'character' => $request->character,
            'movie' => $request->movie,
            'year' => $request->year,
            'status' => 'active'
        ]);

        // $user = auth()->user();

        // $quote = Quote::find($id);
        // if ($user->id != $article->user_id) {
        //     return response()->json([
        //         "success" => false,
        //         "message" => "Kamu bukan pemilik quote",
        //     ], 403);
        // }
        // $quote->name = $request->name;
        // $quote->slug = Str::slug($request->name);
        // $quote->save();

        return response()->json([
            "success" => true,
            "message" => "quote successfully updated!",
            "data" => $quote
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
         $quote = Quote::find($id);
         // if ($user->id != $article->user_id) {
         //     return response()->json([
         //         "success" => false,
         //         "message" => "Kamu bukan pemilik artikel",
         //     ], 403);
         // }
         $quote->delete();
 
         return response()->json([
             "success" => true,
             "message" => "quote successfully deleted!",
             "data" => $quote
         ]);
    }

    public function random()
    {
        $quote = DB::table('quotes')
                ->inRandomOrder()
                ->limit(1)
                ->get();

        return response()->json([
            "success" => true,
            "message" => "random quote berhasil ditampilkan!",
            "data" => $quote 
        ], 201);
    }
}
