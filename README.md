
# Zeus Google Api Connector

This project hosts the PHP Google Api Connector for the various Google APIs (Ads, YouTube, Metrics...)

## Install

### Using composer require

The library will be downloaded by Composer and stored under the  `vendor/`  directory.  **Examples are NOT downloaded by this download method.**

1.  Install the latest version using  [Composer](https://getcomposer.org/).
    
    ```
    $ composer require albafo/google-connector-php
    ```
    
2.  Follow  [Using OAuth 2.0 to Access Google APIs](https://developers.google.com/identity/protocols/oauth2)  if you haven't set up the credentials yet.
    
3.  You can now use the library.

## AdsApi

AdsApi allows us to access Google Ads reports through the [Google Ads Query Language.](https://developers.google.com/google-ads/api/docs/query/overview) 

### Setting up your credentials

1. Get a OAuth2 **Client ID** and **Secret** from Credentials section over your [Google Cloud Platform](https://console.cloud.google.com/apis/credentials)
2. Get a **Refresh Token** from the [AuthenticateInStandaloneApplication](https://github.com/googleads/google-ads-php/blob/master/examples/Authentication/AuthenticateInStandaloneApplication.php), which will prompt you for your OAuth2 client ID and secret.
3. Get **Development Token** from your [Google Ads API Center](https://ads.google.com/aw/apicenter)
4. Get your **Customer ID** and your **Login Customer ID** from your Google Ads Console. [Find your Customer ID](https://support.google.com/google-ads/answer/1704344?hl=en)

### Basic Usage

To get a report with Google Ads Query Language create an instance of AdsApi with your config and send the query from the **searchStream** method:

```php    
use Zeus\GoogleConnector\Api\AdsApi;

$adsApi = AdsApi::fromConfig([  
    'clientId' => $config['clientId'],  
    'clientSecret' => $config['clientSecret'],  
    'refreshToken' => $config['refreshToken'],  
    'developerToken' => $config['developerToken'],  
    'customerId' => $config['customerId'],  
    'loginCustomerId' => $config['loginCustomerId']  
]);

$reportObject = $adsApi->searchStream("
    SELECT  campaign.id, campaign.name,  campaign.status
    FROM campaign 
    ORDER BY campaign.id
");
```

Learn all reports query options in [Google Developers Ads Api Page](https://developers.google.com/google-ads/api/docs/reporting/example) 

## Next modules soon...