<?php

use PHPUnit\Framework\TestCase;
use Zeus\GoogleConnector\Auth\Ads\AdsAuth;

class AdsAuthTest extends TestCase
{

    /**
     * @var AdsAuth
     */
    private $adsAuth;

    private function init()
    {
        $this->adsAuth = $this->getAdsAuth();
    }

    public function testClassOauth()
    {
        $this->init();
        $this->assertInstanceOf(\Zeus\GoogleConnector\Auth\GoogleOAuth2::class, $this->adsAuth);
    }

    public function testDeveloperToken()
    {
        $this->init();
        $this->assertEquals('developerToken', $this->adsAuth->getDeveloperToken());
    }

    public function testCustomerId()
    {
        $this->init();
        $this->assertEquals('customerId', $this->adsAuth->getCustomerId());
    }

    public function testLoginCustomerId()
    {
        $this->init();
        $this->assertEquals('loginCustomerId', $this->adsAuth->getLoginCustomerId());
    }

    /**
     * @return AdsAuth
     */
    private function getAdsAuth(): AdsAuth
    {
        $adsAuth = new AdsAuth('clientId', 'clientSecret', ['scope1', 'scope2'],
            'refreshToken', 'developerToken', 'customerId', 'loginCustomerId');
        return $adsAuth;
    }
}