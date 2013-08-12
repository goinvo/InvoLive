<?php

/*
*   Author: Ivan David Di Lernia
*   Fork of FitbitPHP api adapted to work with Withing api
*   Requirements: OAuth extension (pecl install oauth)   
*/

class BodymediaPHP
{

    /**
     * API Constants
     *
     */
    private $authHost = 'api.bodymedia.com';
    private $apiHost = 'api.bodymedia.com';

    private $baseApiUrl;
    private $authUrl;
    private $requestTokenUrl;
    private $accessTokenUrl;

    /**
     * Class Variables
     *
     */
    protected $oauth;
    protected $oauthToken, $oauthSecret;

    protected $userId = '-';
    protected $debug;

    /**
     * @param string $consumer_key Application consumer key for withings API
     * @param string $consumer_secret Application secret
     * @param int $debug Debug mode (0/1) enables OAuth internal debug
     */
    public function __construct($consumer_key, $consumer_secret, $debug = 1)
    {
       

        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->oauth = new OAuth($consumer_key, $consumer_secret, OAUTH_SIG_METHOD_HMACSHA1);

        $this->initUrls();

        $this->debug = $debug;
        if ($debug)
            $this->oauth->enableDebug();

    }

    private function initUrls()
    {
        $this->baseApiUrl = 'http://' . $this->apiHost . '/v2/measure';
        $this->authUrl = 'https://' . $this->authHost . '/oauth/authorize';
        $this->requestTokenUrl = 'https://' . $this->authHost . '/oauth/request_token';
        $this->accessTokenUrl = 'https://' . $this->authHost . '/oauth/access_token';
    }

    /**
     * @return OAuth debugInfo object for previous call. Debug should be enabled in __construct
     */
    public function oauthDebug()
    {
        return $this->oauth->debugInfo;
    }

    /**
     * Returns withings session status for frontend (i.e. 'Sign in with withings' implementations)
     *
     * @return int (0 - no session, 1 - just after successful authorization, 2 - session exist)
     */
    public static function sessionStatus()
    {
        $session = session_id();
        if (empty($session)) {
            session_start();
        }
        if (empty($_SESSION['withings_Session']))
            $_SESSION['withings_Session'] = 0;

        return (int)$_SESSION['withings_Session'];
    }

    /**
     * Initialize session. Inits OAuth session, handles redirects to withings login/authorization if needed
     *
     * @param  $callbackUrl Callback for 'Sign in with withings'
     * @return int (1 - just after successful authorization, 2 - if session already exist)
     */
    public function initSession($callbackUrl)
    {

        $session = session_id();
        if (empty($session)) {
            session_start();
        }

        if (empty($_SESSION['withings_Session']))
            $_SESSION['withings_Session'] = 0;


        if (!isset($_GET['oauth_token']) && $_SESSION['withings_Session'] == 1)
            $_SESSION['withings_Session'] = 0;


        if ($_SESSION['withings_Session'] == 0) {

            // fetch is used instead of getRequestToken (this is for the api_key additional parameter)
            $this->oauth->fetch($this->requestTokenUrl, 
                array('api_key' => $this->consumer_key),OAUTH_HTTP_METHOD_GET);

            $response = $this->oauth->getLastResponse();
            $request_token_info = array();
            parse_str($response, $request_token_info);

            $_SESSION['withings_Secret'] = $request_token_info['oauth_token_secret'];
            $_SESSION['withings_Session'] = 1;


            header('Location: ' . $this->authUrl . '?api_key='. $this->consumer_key .'&oauth_token=' . $request_token_info['oauth_token']);

            exit;

        } else if ($_SESSION['withings_Session'] == 1) {


            $this->oauth->setToken($_GET['oauth_token'], $_SESSION['withings_Secret']);
            
            $access_token_info = $this->oauth->getAccessToken($this->accessTokenUrl);

            $_SESSION['withings_Session'] = 2;
            $_SESSION['withings_Token'] = $access_token_info['oauth_token'];
            $_SESSION['withings_Secret'] = $access_token_info['oauth_token_secret'];

            $this->setOAuthDetails($_SESSION['withings_Token'], $_SESSION['withings_Secret']);
            return 1;

        } else if ($_SESSION['withings_Session'] == 2) {
            $this->setOAuthDetails($_SESSION['withings_Token'], $_SESSION['withings_Secret']);
            return 2;
        }
    }

    /**
     * Sets OAuth token/secret. Use if library used in internal calls without session handling
     *
     * @param  $token
     * @param  $secret
     * @return void
     */
    public function setOAuthDetails($token, $secret)
    {
        $this->oauthToken = $token;
        $this->oauthSecret = $secret;

        $this->oauth->setToken($this->oauthToken, $this->oauthSecret);
    }

    /**
     * Get OAuth token
     *
     * @return string
     */
    public function getOAuthToken()
    {
        return $this->oauthToken;
    }

    /**
     * Get OAuth secret
     *
     * @return string
     */
    public function getOAuthSecret()
    {
        return $this->oauthSecret;
    }

    /**
     * Set withings userId for future API calls
     *
     * @param  $userId 'XXXXX'
     * @return void
     */
    public function setUser($userId)
    {
        $this->userId = $userId;
    }

    public function getUser()
    {
        return $this->userId;
    }

    /**
     * Get user profile
     *
     * @param {DateString} $date - Day for which data activities data is fetched
     * @return {Object} - Activities for specified day
     */
    public function getActivities($date)
    {

        // fetch data using OAuth
        try {
            $this->oauth->fetch('http://wbsapi.withings.net/v2/measure', 
                array('action'=>'getactivity', 'date' => $date, 'userid'=> $this->userId),OAUTH_HTTP_METHOD_GET);
        } catch (Exception $E) {
            echo 'Unable to fetch data';
        }

        $response = $this->oauth->getLastResponse();
        $responseInfo = $this->oauth->getLastResponseInfo();
        if (!strcmp($responseInfo['http_code'], '200')) {
            if ($response)
                return $response;
            else
                echo 'Unable to fetch data';
        } else {
            echo 'withings request failed. Code: ' . $responseInfo['http_code'];
        }
    }


}

