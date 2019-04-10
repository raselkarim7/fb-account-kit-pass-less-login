<?php

namespace App\Http\Controllers;


use AccountKit;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;



class CustomLoginController extends Controller
{


    public function otpLogin(Request $request) {


// Initialize variables
        $app_id = env("FB_ACCOUNTKIT_APP_ID");
        $secret = env("FB_ACCOUNTKIT_APP_SECRET");

        $version = 'v1.1'; // 'v1.1' for example

        $code = $request->code; //MYSELF


        // Exchange authorization code for access token
        $token_exchange_url = 'https://graph.accountkit.com/' . $version . '/access_token?' .
            'grant_type=authorization_code' .
            '&code=' . $code .
            "&access_token=AA|$app_id|$secret";

        $firstResponse = $this->doGuzzle($token_exchange_url);
        $firstResponseBody = $firstResponse->getBody();
        $data =  \GuzzleHttp\json_decode( $firstResponseBody );


        $user_id = $data->id;
        $user_access_token = $data->access_token;
        $refresh_interval = $data->token_refresh_interval_sec;

        /*
         *  Cannot use object of type GuzzleHttp\\Psr7\\Response as array

            $user_id = $data['id'];
            $user_access_token = $data['access_token'];
            $refresh_interval = $data['token_refresh_interval_sec'];
            return $data;
        */


        // Get Account Kit information
        $me_endpoint_url = 'https://graph.accountkit.com/' . $version . '/me?' .
            'access_token=' . $user_access_token;
        $secondResponse = $this->doGuzzle($me_endpoint_url);
        $secondResponseBody = $secondResponse->getBody();
        $data2 = \GuzzleHttp\json_decode($secondResponseBody);


        $phone = property_exists($data2, 'phone')  ? $data2->phone->number : '';

        /*
         * After getting the phone number, You make sure the user is valid.
         * Now you may GENERATE token for token_based_auth
         * (Example: Using Laravel Passport, or jwt.).
         *
         */

        return $phone;

        /*
        Here "isset" will not work, because the object is json decoded,
        So we have to use "property_exists"
            $phone = isset($data2['phone']) ? $data2['phone']['number'] : '';
            $email = isset($data2['email']) ? $data2['email']['address'] : '';
        */
       // return $data2;

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
