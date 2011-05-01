<?php
/**
 * NP-Gravatar
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is
 * bundled with this package in the file LICENSE.txt.
 */

require_once 'NP/Service/Gravatar/Utility.php';

/**
 * Client for consuming profile information, based on the primary
 * email address of some user.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
class NP_Service_Gravatar_Profiles
{
    const GRAVATAR_SERVER = 'http://www.gravatar.com';

    /**
     * HTTP Client used to query web services.
     * 
     * @var Zend_Http_Client 
     */
    protected $_httpClient = null;

    /**
     * Response format.
     * 
     * @var NP_Service_Gravatar_Profiles_ResponseFormat_Abstract|string
     */
    protected $_responseFormat = 'php';

    /**
     * Plugin loader for response format classes.
     *
     * @var Zend_Loader_PluginLoader
     */
    protected static $_pluginLoader;

    /**
     * Constructor.
     *
     * @return void
     */
    public function  __construct($responseFormat = null)
    {
        if (null !== $responseFormat) {
            $this->setResponseFormat($responseFormat);
        }
    }

    /**
     * Gets plugin loader instance.
     *
     * @return Zend_Loader_PluginLoader
     */
    protected static function _getPluginLoader()
    {
        if (!self::$_pluginLoader) {
            require_once 'Zend/Loader/PluginLoader.php';
            self::$_pluginLoader = new Zend_Loader_PluginLoader(array(
                'NP_Service_Gravatar_Profiles_ResponseFormat_'=>'NP/Service/Gravatar/Profiles/ResponseFormat/')
            );
        }

        return self::$_pluginLoader;
    }

    /**
     * Sets Http_Client which will be used for sending requests.
     * 
     * @param Zend_Http_Client $httpClient
     * @return NP_Service_Gravatar_Profiles
     */
    public function setHttpClient(Zend_Http_Client $httpClient)
    {
        $this->_httpClient = $httpClient;

        return $this;
    }
    
    /**
     * Gets HTTP client instance.
     *
     * @return Zend_Http_Client
     */
    public function getHttpClient()
    {
        if (!$this->_httpClient instanceof Zend_Http_Client) {
            require_once 'Zend/Http/Client.php';
            $this->_httpClient = new Zend_Http_Client();
        }

        return $this->_httpClient;
    }

    /**
     * Sets the response format.
     *
     * @param NP_Service_Gravatar_Profiles_ResponseFormat_Abstract|string $responseFormat
     * @return NP_Service_Gravatar
     */
    public function setResponseFormat($responseFormat)
    {
        $this->_responseFormat = $responseFormat;
        
        return $this;
    }

    /**
     * Gets the response format.
     * 
     * @return NP_Service_Gravatar_Profiles_ResponseFormat_Abstract
     */
    public function getResponseFormat()
    {
        if (!$this->_responseFormat instanceof NP_Service_Gravatar_Profiles_ResponseFormat_Abstract) {
            $class = self::_getPluginLoader()->load((string)$this->_responseFormat);
            $this->_responseFormat = new $class();
        }
        
        return $this->_responseFormat;
    }

    /**
     * Gets profile info of some Gravatar's user, based on his/her
     * email address. Return value is NP_Gravatar_Profile instance,
     * in case $_responseFormat implements
     * NP_Service_Gravatar_Profiles_ResponseFormat_ParserInterface
     * interface. Otherwise, or in case $rawResponse flag is set to
     * boolean true, Zend_Http_Response instance is returned.
     *
     * @param string $email
     * @param bool $rawResponse Whether raw response object should be returned.
     * @return NP_Gravatar_Profile|Zend_Http_Response
     */
    public function getProfileInfo($email, $rawResponse = false)
    {
        $email = strtolower(trim((string)$email));
        $hash = NP_Service_Gravatar_Utility::emailHash($email);

        $responseFormat = $this->getResponseFormat();

        $response = $this->getHttpClient()
            ->setMethod(Zend_Http_Client::GET)
            ->setUri(self::GRAVATAR_SERVER . '/' . $hash . '.' . $responseFormat->__toString())
            ->request();

        $reflected = new ReflectionObject($responseFormat);
        if (
            $reflected->implementsInterface('NP_Service_Gravatar_Profiles_ResponseFormat_ParserInterface')
            && !$rawResponse
         ) {
            return $responseFormat->profileFromHttpResponse($response);
        } else {
            return $response;
        }
    }
}