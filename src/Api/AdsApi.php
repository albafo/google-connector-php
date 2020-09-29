<?php


namespace Zeus\GoogleConnector\Api;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AdsApi
{

    const SEARCHSTREAM_URL = "https://googleads.googleapis.com/v5/customers/?/googleAds:searchStream";

    private $auth;

    public function __construct(AdsAuth $auth)
    {
        $this->auth = $auth;
    }


    /**
     * @param $query
     * @return mixed
     * @throws Exceptions\GoogleOauthException
     */
    public function searchStream($query) {
        $url = Str::replaceArray("?", [$this->auth->getCustomerId()], self::SEARCHSTREAM_URL);
        $rawBody = [
            "query" => $query
        ];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->auth->getAccessToken(),
            'developer-token' => $this->auth->getDeveloperToken(),
            'login-customer-id' => $this->auth->getLoginCustomerId()
        ])->withBody(json_encode($rawBody), 'application/json')->post($url);

        if($response->status() !== 200) {
            throw new \Exception("AdsAPI Error. Result: ". $response->body());
        }

        return $response->json();
    }

    public static function fromConfig($config) {
        $auth = new AdsAuth(
            $config['clientId'],
            $config['clientSecret'],
            [GoogleOAuth2::ADWORDS_API_SCOPE],
            $config['refreshToken'] ?? null,
            $config['developerToken'] ?? null,
            $config['customerId'] ?? null,
            $config['loginCustomerId'] ?? null
        );

        return new AdsApi($auth);
    }
}