<?php

use PHPUnit\Framework\TestCase;
use Zeus\GoogleConnector\Auth\Ads\AdsAuth;

class AdsApiTest extends TestCase
{
    use \DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

    /* @var \Zeus\GoogleConnector\Api\AdsApi*/
    private $adsApi;


    /**
     * @var \GuzzleHttp\Handler\MockHandler
     */
    private $mock;

    private function setAdsApi()
    {
        $this->mock = new \GuzzleHttp\Handler\MockHandler([
            new \GuzzleHttp\Psr7\Response(200, ['X-Foo' => 'Bar'], json_encode([ 'test' => '123456' ]))
        ]);
        $handlerStack = \GuzzleHttp\HandlerStack::create($this->mock);
        $client = new \GuzzleHttp\Client(['handler' => $handlerStack]);

        $adsAuth = $this->createMock(AdsAuth::class);
        $adsAuth->method('getAccessToken')->willReturn('123456');
        $adsAuth->method('getDeveloperToken')->willReturn('654321');
        $adsAuth->method('getLoginCustomerId')->willReturn('123');
        $adsAuth->method('getCustomerId')->willReturn('123654');


        $this->adsApi = new \Zeus\GoogleConnector\Api\AdsApi($adsAuth, $client);

    }

    public function testSearchStreamExceptionWhenClientStatusNotOk()
    {
        $mock = new \GuzzleHttp\Handler\MockHandler([
            new \GuzzleHttp\Psr7\Response(500, ['X-Foo' => 'Bar'], json_encode([ 'error' => 'error' ]))
        ]);

        $handlerStack = \GuzzleHttp\HandlerStack::create($mock);

        $client = new \GuzzleHttp\Client(['handler' => $handlerStack]);

        $adsAuth = $this->createMock(AdsAuth::class);
        $adsAuth->method('getAccessToken')->willReturn('123456');
        $adsAuth->method('getDeveloperToken')->willReturn('654321');
        $adsAuth->method('getLoginCustomerId')->willReturn('123');
        $adsAuth->method('getCustomerId')->willReturn('123654');


        $adsApi = new \Zeus\GoogleConnector\Api\AdsApi($adsAuth, $client);
        $this->expectException(\Zeus\GoogleConnector\Exceptions\AdsApiException::class);
        $adsApi->searchStream("query");

    }

    public function testClientconfigSearchStream()
    {
        $this->setAdsApi();
        $this->adsApi->searchStream('test');
        $this->assertArraySubset([
            'Authorization' => ['Bearer 123456'],
            'developer-token' => ['654321'],
            'login-customer-id' => ['123']
        ], $this->mock->getLastRequest()->getHeaders(), true);

        $this->assertEquals('https://googleads.googleapis.com/v5/customers/123654/googleAds:searchStream',
            $this->mock->getLastRequest()->getUri());

        $this->assertEquals("POST", $this->mock->getLastRequest()->getMethod());

        $this->assertEquals(json_encode([ 'query' => 'test' ]), $this->mock->getLastRequest()->getBody());
    }

    public function testSearchStream()
    {
        $this->setAdsApi();
        $response = $this->adsApi->searchStream('test');
        $this->assertEquals(['test' => '123456'], $response);
    }

    public function testSearchStreamFromConfig()
    {
        $adsApi = \Zeus\GoogleConnector\Api\AdsApi::fromConfig([
            'clientId' => 'clientId',
            'clientSecret' => 'clientSecret',
            'refreshToken' => 'refreshToken',
            'developerToken' => 'developerToken',
            'customerId' => 'customerId',
            'loginCustomerId' => 'loginCustomerId'
        ]);

        $this->assertInstanceOf(\Zeus\GoogleConnector\Api\AdsApi::class, $adsApi);
    }

    public function testSearchStreamThrowExceptionIfClientIdNotSet()
    {
        $this->expectException(\Zeus\GoogleConnector\Exceptions\AdsApiException::class);
        \Zeus\GoogleConnector\Api\AdsApi::fromConfig([
            'clientSecret' => 'clientSecret',
            'refreshToken' => 'refreshToken',
            'developerToken' => 'developerToken',
            'customerId' => 'customerId',
            'loginCustomerId' => 'loginCustomerId'
        ]);
    }

    public function testSearchStreamThrowExceptionIfClientSecretNotSet()
    {
        $this->expectException(\Zeus\GoogleConnector\Exceptions\AdsApiException::class);
        \Zeus\GoogleConnector\Api\AdsApi::fromConfig([
            'clientId' => 'clientId',
            'refreshToken' => 'refreshToken',
            'developerToken' => 'developerToken',
            'customerId' => 'customerId',
            'loginCustomerId' => 'loginCustomerId'
        ]);
    }

    public function testSearchStreamThrowExceptionIfRefreshTokenNotSet()
    {
        $this->expectException(\Zeus\GoogleConnector\Exceptions\AdsApiException::class);
        \Zeus\GoogleConnector\Api\AdsApi::fromConfig([
            'clientId' => 'clientId',
            'clientSecret' => 'clientSecret',
            'developerToken' => 'developerToken',
            'customerId' => 'customerId',
            'loginCustomerId' => 'loginCustomerId'
        ]);
    }

    public function testSearchStreamThrowExceptionIfDeveloperTokenNotSet()
    {
        $this->expectException(\Zeus\GoogleConnector\Exceptions\AdsApiException::class);
        \Zeus\GoogleConnector\Api\AdsApi::fromConfig([
            'clientId' => 'clientId',
            'clientSecret' => 'clientSecret',
            'refreshToken' => 'refreshToken',
            'customerId' => 'customerId',
            'loginCustomerId' => 'loginCustomerId'
        ]);
    }

    public function testSearchStreamThrowExceptionIfCustomerIdNotSet()
    {
        $this->expectException(\Zeus\GoogleConnector\Exceptions\AdsApiException::class);
        \Zeus\GoogleConnector\Api\AdsApi::fromConfig([
            'clientId' => 'clientId',
            'clientSecret' => 'clientSecret',
            'refreshToken' => 'refreshToken',
            'developerToken' => 'developerToken',
            'loginCustomerId' => 'loginCustomerId'
        ]);
    }

    public function testSearchStreamThrowExceptionIfLoginCustomerIdNotSet()
    {
        $this->expectException(\Zeus\GoogleConnector\Exceptions\AdsApiException::class);
        \Zeus\GoogleConnector\Api\AdsApi::fromConfig([
            'clientId' => 'clientId',
            'clientSecret' => 'clientSecret',
            'refreshToken' => 'refreshToken',
            'developerToken' => 'developerToken',
            'customerId' => 'customerId',
        ]);
    }

    public function testSearchStremaConfigIsNotAnArray()
    {
        $this->expectException(\Zeus\GoogleConnector\Exceptions\AdsApiException::class);
        \Zeus\GoogleConnector\Api\AdsApi::fromConfig('test');
    }
}