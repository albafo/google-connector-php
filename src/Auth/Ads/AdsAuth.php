<?php


namespace Zeus\GoogleConnector;


use Zeus\GoogleConnector\Ads\GoogleOAuth2;

class AdsAuth extends GoogleOAuth2
{

    private $developerToken = null;
    private $customerId = null;
    private $loginCustomerId = null;

    public function __construct($clientId, $clientSecret, array $scopes, $refreshToken = null, $developerToken = null, $customerId = null, $loginCustomerId = null)
    {
        parent::__construct($clientId, $clientSecret, $scopes, $refreshToken);
        $this->developerToken = $developerToken;
        $this->customerId = $customerId;
        $this->loginCustomerId = $loginCustomerId;
    }

    public function getDeveloperToken()
    {
        return $this->developerToken;
    }

    /**
     * @return null
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param null $customerId
     */
    public function setCustomerId($customerId): void
    {
        $this->customerId = $customerId;
    }

    /**
     * @return null
     */
    public function getLoginCustomerId()
    {
        return $this->loginCustomerId;
    }

    /**
     * @param null $loginCustomerId
     */
    public function setLoginCustomerId($loginCustomerId): void
    {
        $this->loginCustomerId = $loginCustomerId;
    }





}