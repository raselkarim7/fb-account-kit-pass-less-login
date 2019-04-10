<?php

namespace App\Http\Controllers;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

use Illuminate\Http\Request;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function getobje(Request $request) {
        $obj = new Object_();
        $obj->firstname = "Rasel";
        $obj->lastname = 'Karim';
        return $obj['firstname'];
    }

    public function getResult(Request $request) {


        $respone = $this->doGuzzle('http://httpbin.org/get');
        $responseBody = $respone->getBody();

        $user = array();
        $user['firstname'] = "A";
        $user['lastname'] = "B";

        $obj = array();
        $obj['body'] = \GuzzleHttp\json_decode($responseBody); // this works same as next line;
        $obj['body'] = \GuzzleHttp\json_decode($responseBody);
        $obj['user'] =  $user;

        $result = \GuzzleHttp\json_decode($responseBody);

        return \GuzzleHttp\json_encode(gettype($result));

    }

    function doGuzzle($url) {
        $client = new Client();

        try {
            return $client->request('GET', $url);
        } catch (RequestException $e) {
            echo Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                return Psr7\str($e->getResponse());
            }
        }
    }
}
