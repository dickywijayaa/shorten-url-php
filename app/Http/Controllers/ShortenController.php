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

    /**
     * @OA\Get(
     *     path="/shorten/{shortcode}",
     *     operationId="GetURLFromShortcode",
     *     description="Endpoint to redirect to url from shortcode",
     *     tags={"Shorten"},
     *     @OA\Parameter(
     *         in="path",
     *         name="shortcode",
     *         description="Shortcode to be search.",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="awXy12"
     *         )
     *     ),
     *     @OA\Response(
     *          response=302,
     *          description="Will redirect to url if exists"
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad Request.",
     *          @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="shortcode is not found"
     *             )
     *          )
     *     ),
     * )
     */
    public function GetURLFromShortcode($shortcode) {
        $result = $this->service->FetchURLByCode($shortcode);
        
        if (!$result['status']) {
            $result['message'] = 'shortcode is not found.';
            return response()->json($result, HTTPStatus::HTTP_NOT_FOUND);
        }

        return redirect($result['data']->url);
    }

    /**
     * @OA\Post(
     *     path="/shorten/",
     *     operationId="PostShortcode",
     *     description="Endpoint to store url and shortcode",
     *     tags={"Shorten"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="url",
     *                 type="string",
     *                 example="https://dickywijayaa.com"
     *             ),
     *             @OA\Property(
     *                 property="shortcode",
     *                 type="string",
     *                 example="AWX123"
     *             )
     *          ),
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="success, return url and shortcode",
     *          @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="to-do"
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="url",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="shortcode",
     *                     type="string"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             )
     *          )
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Bad Request.",
     *          @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="missing required url parameter."
     *             )
     *          )
     *     ),
     *     @OA\Response(
     *          response="422",
     *          description="Unprocessable Entity.",
     *          @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="failed insert url."
     *             )
     *          )
     *     ),
     *
     * )
     */
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