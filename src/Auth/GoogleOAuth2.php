<?php


namespace Zeus\GoogleConnector\Ads;


use Google\Auth\CredentialsLoader;
use Google\Auth\OAuth2;
use Zeus\GoogleConnector\Exceptions\GoogleOauthException;

class GoogleOAuth2
{
    /**
     * @var string the OAuth2 scope for the AdWords API
     * @see https://developers.google.com/adwords/api/docs/guides/authentication#scope
     */
    const ADWORDS_API_SCOPE = 'https://www.googleapis.com/auth/adwords';

    /**
     * @var string the OAuth2 scope for the Ad Manger API
     * @see https://developers.google.com/ad-manager/docs/authentication#scope
     */
    const AD_MANAGER_API_SCOPE = 'https://www.googleapis.com/auth/dfp';

    /**
     * @var string the Google OAuth2 authorization URI for OAuth2 requests
     * @see https://developers.google.com/identity/protocols/OAuth2InstalledApp#formingtheurl
     */
    const AUTHORIZATION_URI = 'https://accounts.google.com/o/oauth2/v2/auth';

    /**
     * @var string the redirect URI for OAuth2 installed application flows
     * @see https://developers.google.com/identity/protocols/OAuth2InstalledApp#formingtheurl
     */
    const REDIRECT_URI = 'urn:ietf:wg:oauth:2.0:oob';

    private $clientId, $clientSecret, $scopes;
    private $refreshToken = null;
    private $oauth2 = null;


    public function __construct($clientId, $clientSecret, array $scopes, $refreshToken = null)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->scopes = $scopes;
        $this->refreshToken = $refreshToken;

        $this->oauth2 = new OAuth2(
            [
                'authorizationUri' => self::AUTHORIZATION_URI,
                'redirectUri' => self::REDIRECT_URI,
                'tokenCredentialUri' => CredentialsLoader::TOKEN_CREDENTIAL_URI,
                'clientId' => $this->clientId,
                'clientSecret' => $this->clientSecret,
                'scope' => implode(" ", $this->scopes)
            ]
        );
    }

    /**
     * @throws GoogleOauthException
     */
    public function getAccessToken()
    {
        if(!$this->refreshToken) {
            $url = $this->oauth2->buildFullAuthorizationUri();
            throw new GoogleOauthException('Refresh Token is not setted. 
            Log into the Google account you use for your scope and visit the following URL: '.$url." Use the code generated
            in the method GoogleOAuth2::generateRefreshToken to obtain a valid refresh token");
        }
        $this->oauth2->setRefreshToken($this->refreshToken);
        $this->oauth2->setGrantType('refresh_token');
        return $this->oauth2->fetchAuthToken()['access_token'];
    }
}