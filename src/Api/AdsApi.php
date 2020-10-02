<?php


namespace Zeus\GoogleConnector\Api;



use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Zeus\GoogleConnector\Auth\GoogleOAuth2;
use Zeus\GoogleConnector\Auth\Ads\AdsAuth;
use Zeus\GoogleConnector\Exceptions\AdsApiException;
use Zeus\GoogleConnector\Exceptions\GoogleOauthException;

class AdsApi
{

    const SEARCHSTREAM_URL = "https://googleads.googleapis.com/v5/customers/%s/googleAds:searchStream";

    private $auth;
    private $client;

    public function __construct(AdsAuth $auth, Client $client)
    {
        $this->auth = $auth;
        $this->client = $client;
    }


    /**
     * @param $query
     * @return mixed
     * @throws AdsApiException
     * @throws GoogleOauthException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function searchStream($query) {

        $url = $this->getSearchStreamUrl();
        $rawBody = $this->getRawBodyStreamUrl($query);

        try {
            $response = $this->client->request('POST', $url, [
                'headers' => $this->getSearchStreamHeaders(),
                'body' => json_encode($rawBody)
            ]);

        } catch (GuzzleException $exception) {
                throw new AdsApiException("AdsAPI Error. Result: ". (string) $exception->getMessage());
        }

        return json_decode((string) $response->getBody(), true);
    }


    /**
     * @param $config
     * @return AdsApi
     * @throws AdsApiException
     */
    public static function fromConfig($config) {
        self::checkConfig($config);
        $auth = new AdsAuth(
            $config['clientId'],
            $config['clientSecret'],
            [GoogleOAuth2::ADWORDS_API_SCOPE],
            $config['refreshToken'] ?? null,
            $config['developerToken'] ?? null,
            $config['customerId'] ?? null,
            $config['loginCustomerId'] ?? null
        );

        return new AdsApi($auth, new Client());
    }

    private static function checkConfig($config) {
        if(!is_array($config)) {
            throw new AdsApiException("AdsApi config is not an array");
        }

        $configKeys = ['clientId', 'clientSecret', 'refreshToken', 'customerId', 'loginCustomerId', 'developerToken'];

        foreach($configKeys as $key) {
            if(!key_exists($key, $config) || !$config[$key]) {
                throw new AdsApiException("{$key} not set in AdsApi config");
            }
        }
    }

    private function getSearchStreamUrl()
    {
        return sprintf(self::SEARCHSTREAM_URL, $this->auth->getCustomerId());
    }

    private function getRawBodyStreamUrl($query)
    {
        return  [
            "query" => $query
        ];
    }

    /**
     * @return array
     * @throws GoogleOauthException
     */
    private function getSearchStreamHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->auth->getAccessToken(),
            'developer-token' => $this->auth->getDeveloperToken(),
            'login-customer-id' => $this->auth->getLoginCustomerId()
        ];
    }
}