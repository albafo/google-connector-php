<?php

use Google\Auth\CredentialsLoader;
use Google\Auth\OAuth2;
use Zeus\GoogleConnector\Auth\GoogleOAuth2;
use PHPUnit\Framework\TestCase;

class GoogleOAuth2Test extends TestCase
{
    public function testAccessToken()
    {
        $googleOauth = new GoogleOAuth2(
            'clientId', 'clientSecret', ['scope1', 'scope2'], 'refreshToken'
        );

        $this->assertEquals(
            [
                'authorizationUri' => GoogleOAuth2::AUTHORIZATION_URI,
                'redirectUri' => GoogleOAuth2::REDIRECT_URI,
                'tokenCredentialUri' => CredentialsLoader::TOKEN_CREDENTIAL_URI,
                'clientId' => 'clientId',
                'clientSecret' => 'clientSecret',
                'scope' => 'scope1 scope2',
            ], $googleOauth->getOauthConfig());

        $oauthMock = $this->createMock(OAuth2::class);

        $oauthMock->expects($this->once())->method('setRefreshToken')->with($this->equalTo('refreshToken'));
        $oauthMock->expects($this->once())->method('setGrantType')->with($this->equalTo('refresh_token'));

        $oauthMock->method('fetchAuthToken')->willReturn(['access_token' => '123456']);
        $googleOauth->setOauth2($oauthMock);
        $accessToken = $googleOauth->getAccessToken();
        $this->assertEquals('123456', $accessToken);
    }
}