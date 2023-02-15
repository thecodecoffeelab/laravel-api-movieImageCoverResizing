<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResizeImageMovieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * Regex is a pattern for making string inside string
     * @return array
     */
    public function rules()
    {
        $rules = [
            'cover' => ['required'],
            'width' => ['required', 'regex:/^\d+(\.\d+)?%?$/'], //Values of Width: 50, 50%, 50.123px, 50.123% 
            'heaight' => 'regex:/^\d+(\.\d+)?%?$/',
            'movie_id' => 'exists:\App\Models\Movie,id',
        ];

        //$cover = $this->post('cover'); -> post cover image

        $cover = $this->all()['cover'] ?? false;
        //Qucik test to check the movie cover image
     /*     echo '<pre>';
         var_dump($cover);
         echo '</pre>';
         exit;   */      
        if($cover && $cover instanceof UploadedFile) {
            $rules['cover'][] = 'cover';
        } else {
            $rules['cover'][] = 'url';
        }
 
        //Qucik test to dump var $rules
 /*        echo '<pre>';
        var_dump($rules);
        echo '</pre>';
        exit;  */

        return $rules;
    }
}
