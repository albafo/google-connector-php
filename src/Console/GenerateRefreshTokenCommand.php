<?php


namespace Zeus\GoogleConnector\Console;


use Google\Auth\CredentialsLoader;
use Google\Auth\OAuth2;

class GenerateRefreshTokenCommand
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

    const ANALYTICS_READONLY_API_SCOPE = 'https://www.googleapis.com/auth/analytics.readonly';

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


    /**
     * Execute the console command.
     *
     * @return int
     */
    public static function run()
    {
        $PRODUCTS = [
            ['AdWords API', self::ADWORDS_API_SCOPE],
            ['Ad Manager API', self::AD_MANAGER_API_SCOPE],
            ['Analytics ReadOnly API', self::ANALYTICS_READONLY_API_SCOPE],

        ];

        $stdin = fopen('php://stdin', 'r');

        print 'Enter your OAuth2 client ID here: ';
        $clientId = trim(fgets($stdin));

        print 'Enter your OAuth2 client secret here: ';
        $clientSecret = trim(fgets($stdin));

        print "Select the API you're using: [0] AdWords API [1] Ad Manager API [2] Analytics Read Only"
            . PHP_EOL;
        $api = trim(fgets($stdin));

        while (!is_numeric($api)
            || !(strval(intval($api)) === $api)
            || !(intval($api) >= 0 && intval($api) <= 2))
        {
            print "Please enter a valid number for the API you're using: " .
                "[0] AdWords API [1] Ad Manager API [2] Analytics Read Only " . PHP_EOL;
            $api = trim(fgets($stdin));
        }
        $api = intval($api);


        printf(
            '[OPTIONAL] enter any additional OAuth2 scopes as a space '
            . 'delimited string here (the %s scope is already included): ',
            $PRODUCTS[$api][0]
        );

        $scopes = $PRODUCTS[$api][1] . ' ' . trim(fgets($stdin));

        $oauth2 = new OAuth2(
            [
                'authorizationUri' => self::AUTHORIZATION_URI,
                'redirectUri' => self::REDIRECT_URI,
                'tokenCredentialUri' => CredentialsLoader::TOKEN_CREDENTIAL_URI,
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'scope' => $scopes
            ]
        );


        printf(
            "Log into the Google account you use for %s and visit the following"
            . " URL:\n%s\n\n",
            $PRODUCTS[$api][0],
            $oauth2->buildFullAuthorizationUri()
        );
        print 'After approving the application, enter the authorization code '
            . 'here: ';
        $code = trim(fgets($stdin));
        fclose($stdin);
        print "\n";

        $oauth2->setCode($code);
        $authToken = $oauth2->fetchAuthToken();

        printf("Your refresh token is: %s\n\n", $authToken['refresh_token']);

        return 0;
    }
}