<?php

class WithingsPHP
{

    /**
     * API Constants
     *
     */
    private $authHost = 'oauth.withings.com';
    private $apiHost = 'wbsapi.withings.net';

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

    protected $metric = 0;
    protected $debug;

    protected $clientDebug;


    /**
     * @param string $consumer_key Application consumer key for Fitbit API
     * @param string $consumer_secret Application secret
     * @param int $debug Debug mode (0/1) enables OAuth internal debug
     * @param string $user_agent User-agent to use in API calls
     */
    public function __construct($consumer_key, $consumer_secret, $debug = 1, $user_agent = null)
    {
        $this->initUrls();

        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->oauth = new OAuth($consumer_key, $consumer_secret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);

        $this->debug = $debug;
        if ($debug)
            $this->oauth->enableDebug();

    }


    /**
     * @param string $consumer_key Application consumer key for Fitbit API
     * @param string $consumer_secret Application secret
     */
    public function reinit($consumer_key, $consumer_secret)
    {

        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;

        $this->oauth = new OAuth($consumer_key, $consumer_secret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_AUTHORIZATION);

        if ($this->debug)
            $this->oauth->enableDebug();
    }


    private function initUrls()
    {
        $this->baseApiUrl = 'http://' . $this->apiHost . '/v2/measure';
        $this->authUrl = 'https://' . $this->authHost . '/account/authorize';
        $this->requestTokenUrl = 'https://' . $this->authHost . '/account/request_token';
        $this->accessTokenUrl = 'https://' . $this->authHost . '/account/access_token';
    }

    /**
     * @return OAuth debugInfo object for previous call. Debug should be enabled in __construct
     */
    public function oauthDebug()
    {
        return $this->oauth->debugInfo;
    }

    /**
     * @return OAuth debugInfo object for previous client_customCall. Debug should be enabled in __construct
     */
    public function client_oauthDebug()
    {
        return $this->clientDebug;
    }


    /**
     * Returns Fitbit session status for frontend (i.e. 'Sign in with Fitbit' implementations)
     *
     * @return int (0 - no session, 1 - just after successful authorization, 2 - session exist)
     */
    public static function sessionStatus()
    {
        $session = session_id();
        if (empty($session)) {
            session_start();
        }
        if (empty($_SESSION['fitbit_Session']))
            $_SESSION['fitbit_Session'] = 0;

        return (int)$_SESSION['fitbit_Session'];
    }

    /**
     * Initialize session. Inits OAuth session, handles redirects to Fitbit login/authorization if needed
     *
     * @param  $callbackUrl Callback for 'Sign in with Fitbit'
     * @param  $cookie Use persistent cookie for authorization, or session cookie only
     * @return int (1 - just after successful authorization, 2 - if session already exist)
     */
    public function initSession($callbackUrl, $cookie = true)
    {

        $session = session_id();
        if (empty($session)) {
            session_start();
        }

        if (empty($_SESSION['fitbit_Session']))
            $_SESSION['fitbit_Session'] = 0;


        if (!isset($_GET['oauth_token']) && $_SESSION['fitbit_Session'] == 1)
            $_SESSION['fitbit_Session'] = 0;


        if ($_SESSION['fitbit_Session'] == 0) {

            $request_token_info = $this->oauth->getRequestToken($this->requestTokenUrl, $callbackUrl);

            $_SESSION['fitbit_Secret'] = $request_token_info['oauth_token_secret'];
            $_SESSION['fitbit_Session'] = 1;

            header('Location: ' . $this->authUrl . '?oauth_token=' . $request_token_info['oauth_token']);
            exit;

        } else if ($_SESSION['fitbit_Session'] == 1) {

            $this->oauth->setToken($_GET['oauth_token'], $_SESSION['fitbit_Secret']);
            $access_token_info = $this->oauth->getAccessToken($this->accessTokenUrl);

            $_SESSION['fitbit_Session'] = 2;
            $_SESSION['fitbit_Token'] = $access_token_info['oauth_token'];
            $_SESSION['fitbit_Secret'] = $access_token_info['oauth_token_secret'];

            $this->setOAuthDetails($_SESSION['fitbit_Token'], $_SESSION['fitbit_Secret']);
            return 1;

        } else if ($_SESSION['fitbit_Session'] == 2) {
            $this->setOAuthDetails($_SESSION['fitbit_Token'], $_SESSION['fitbit_Secret']);
            return 2;
        }
    }

    /**
     * Reset session
     *
     * @return void
     */
    public function resetSession()
    {
        $_SESSION['fitbit_Session'] = 0;
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
     * Set Fitbit userId for future API calls
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
     * API wrappers
     *
     */

    /**
     * Get user profile
     *
     * @throws FitBitException
     * @param string $userId UserId of public profile, if none using set with setUser or '-' by default
     * @return mixed SimpleXMLElement or the value encoded in json as an object
     */
    public function getActivities($date)
    {

        // $this->oauth->setNonce('HmorW');
        // $this->oauth->setTimestamp(1375278604);
        
        // echo $this->oauth->getRequestHeader(OAUTH_HTTP_METHOD_GET,'http://wbsapi.withings.net/v2/measure?action=getactivity&date=2013-07-29&userid=2160884');


        // echo '******'.$this->oauth->generateSignature (OAUTH_HTTP_METHOD_GET,
        //     'http://wbsapi.withings.net/v2/measure', 
        //         array('action'=>'getactivity', 'date' => '2013-07-29', 'userid'=> 2160884)
        //     ).'*****';


        try {
            $this->oauth->fetch('http://wbsapi.withings.net/v2/measure', 
                array('action'=>'getactivity', 'date' => $date, 'userid'=> $this->userId),OAUTH_HTTP_METHOD_GET);
        } catch (Exception $E) {
            echo 'ex';
        }

        $response = $this->oauth->getLastResponse();
        $responseInfo = $this->oauth->getLastResponseInfo();
        if (!strcmp($responseInfo['http_code'], '200')) {
            $response = $this->parseResponse($response);
            if ($response)
                return $response;
            else
                echo 'fail';
        } else {
                echo 'Fitbit request failed. Code: ' . $responseInfo['http_code'];
        }
    }

     /**
     * @return array
     */
    private function getHeaders()
    {
        $headers = array();

        if ($this->metric == 1) {
            $headers['Accept-Language'] = 'en_US';
        } else if ($this->metric == 2) {
            $headers['Accept-Language'] = 'en_GB';
        }

        return $headers;
    }

        /**
     * @return mixed SimpleXMLElement or the value encoded in json as an object
     */
    private function parseResponse($response)
    {
        return $response;
    }


}

