
# Zeus Google Api Connector

[![PHP version](https://badge.fury.io/ph/albafo%2Fgoogle-connector-php.svg)](https://badge.fury.io/ph/albafo%2Fgoogle-connector-php) [![Build Status](https://travis-ci.org/albafo/google-connector-php.svg?branch=master)](https://travis-ci.org/albafo/google-connector-php)

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
2. Get a **Refresh Token** calling our refresh-token.php script from your root project directory, which will prompt you for your OAuth2 client ID and secret.
```console    
php vendor/bin/refresh-token.php
```
3. Get **Development Token** from your [Google Ads API Center](https://ads.google.com/aw/apicenter)
4. Get your **Customer ID** (account id which you are consulting) and your **Login Customer ID** (parent account id) from your Google Ads Console. [Find your Customer ID](https://support.google.com/google-ads/answer/1704344?hl=en)
> **Remove hyphens from your customerId and loginCustomerId:** xxx-XXX-xxx  to xxxXXXxxx

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
