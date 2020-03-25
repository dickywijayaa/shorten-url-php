<?php

namespace App\Http\Controllers;

use App\Services\ShortenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helpers\HTTPStatus;

class ShortenController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->service = new ShortenService;
    }

    public function GetURLFromShortcode($shortcode) {
        $result = $this->service->FetchURLByCode($shortcode);
        
        if (!$result['status']) {
            return response()->json([
                'message'=> 'shortcode is not found.'
            ], HTTPStatus::HTTP_NOT_FOUND);
        }

        return redirect($result['data']->url);
    }

    public function PostShorten(Request $request) {
        $params = $request->all();
        
        $validator = Validator::make($params, [
            'url'       => 'required',
            'shortcode' => 'sometimes|required|regex:/^[A-Za-z0-9_]{6}$/',
        ], $this->getValidationMessage());

        if ($validator->fails()) {
            return response()->json([
                'message'=> $validator->errors()->first()
            ], HTTPStatus::HTTP_BAD_REQUEST);
        }

        $result = $this->service->StoreShortenURL($params);
        if ($result) {
            $httpStatus = HTTPStatus::HTTP_OK;
        } else {
            $httpStatus = HTTPStatus::HTTP_UNPROCESSABLE_ENTITY;
        }
        return response()->json($result, $httpStatus);
    }

    private function getValidationMessage() {
        return [
            'url.required' => 'missing required url parameter.',
            'shortcode.required' => 'missing required shortcode parameter.',
            'shortcode.regex' => 'invalid format shortcode.'
        ];
    }
}