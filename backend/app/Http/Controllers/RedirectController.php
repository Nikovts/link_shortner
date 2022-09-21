<?php

namespace App\Http\Controllers;

use App\Models\Urls;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class RedirectController extends BaseController
{


    public function index()
    {

      $urls = Urls::all();

      $token = csrf_token();

      return response()->json(['urls'=>$urls,'token'=>$token]);
    }


    public function store(Request $request)
    {

      $first_remove = explode("//",$request->url)[1];

      $elements = explode(".",$first_remove);

      $params = '';

      foreach ($elements as $element) {

        $sub_element = explode("/",$element);

        foreach ($sub_element as $plain_string) {

          $params .= $plain_string;

          $params .= "-";
        }
      }

      $params = rtrim($params,"-");

      $urls = Urls::firstOrCreate(

        ['params' => $params],

        ['url' => $request->url]
      );

      return response()->json($urls);
    }
}
