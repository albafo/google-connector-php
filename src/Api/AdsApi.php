<?php


namespace Zeus\GoogleConnector\Api;



use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Zeus\GoogleConnector\Auth\GoogleOAuth2;
use Zeus\GoogleConnector\Auth\Ads\AdsAuth;
use Zeus\GoogleConnector\Exceptions\AdsApiException;
use Zeus\GoogleConnector\Exceptions\GoogleOauthException;

class AdsApi
{

    const SEARCHSTREAM_URL = "https://googleads.googleapis.com/v5/customers/%s/googleAds:searchStream";

    private $auth;

    public function __construct(AdsAuth $auth)
    {
        $this->auth = $auth;
    }


    /**
     * @param $query
     * @return mixed
     * @throws AdsApiException
     * @throws GoogleOauthException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function searchStream($query) {

        $url = sprintf(self::SEARCHSTREAM_URL, $this->auth->getCustomerId());

        $rawBody = [
            "query" => $query
        ];

        $client = new Client();
        $request = new Request('POST', $url, [
            'Authorization' => 'Bearer '.$this->auth->getAccessToken(),
            'developer-token' => $this->auth->getDeveloperToken(),
            'login-customer-id' => $this->auth->getLoginCustomerId()
        ], json_encode($rawBody));

        $response = $client->send($request);

        if($response->getStatusCode() !== 200) {
            throw new AdsApiException("AdsAPI Error. Result: ". (string) $response->getBody());
        }

        return json_decode((string) $response->getBody(), true);
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