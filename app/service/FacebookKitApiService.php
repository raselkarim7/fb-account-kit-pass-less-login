<?php



namespace App\Services;
use http\Client\Curl;

class FacebookKitApiService
{
    // method to send Get Request to Url
    function doCurl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $data = json_decode(curl_exec($ch), true);
        curl_close($ch);
//dd($data,$ch);

        return $data;
    }

    // get access token by authorization code

    function getTokenByCode($code)
    {
        $app_id = env('FACEBOOK_APP_ID');
        $secret = env('FACEBOOK_ACCOUNT_KIT_APP_SECRET');
        $version = env('FACEBOOK_ACCOUNT_KIT_API_VERSION');

        $token_exchange_url = 'https://graph.accountkit.com/'.$version.'/access_token?'.
            'grant_type=authorization_code'.
            '&code='.$code.
            "&access_token=AA|$app_id|$secret";


        //dd($token_exchange_url);

        //echo $token_exchange_url;
        $response = $this->doCurl($token_exchange_url);

        if(isset($response['error'])){
            return [
                'success' => false,
                'message' => $response ['error']['message']
            ];
        }else{

            $user_access_token = $response['access_token'];

            return [
                'success' => true,
                'message' => 'User Authorized',
                'data'    =>[
                    'access_token' => $user_access_token
                ]

            ];
        }
    }

    public function getInfoByToken(string $token)
    {
        $me_endpoint_url = 'https://graph.accountkit.com/'.env('FACEBOOK_ACCOUNT_KIT_API_VERSION').'/me?'.
            'access_token='.$token;
        $response = $this->doCurl($me_endpoint_url);



        // dd($response);

        if(isset($response['error'])){
            return [
                'success' => false,
                'message' => $response ['error']['message']
            ];
        }else{

            $phone = $response['phone'];

            return [
                'success' => true,
                'message' => 'User Authorized',
                'data'    =>[
                    'id'     => $response['id'],
                    'number' => '0'.$phone['national_number'],
                ]
            ];
        }

    }

}