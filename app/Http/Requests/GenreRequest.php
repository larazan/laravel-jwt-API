<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
			'name' => 'required|unique:genres,name',
			'slug' => 'unique:genres,slug,',
		];

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'A nice title is required for the genre.'
        ];
           
    }
}
