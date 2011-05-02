<?php
/**
 * NP-Gravatar
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is
 * bundled with this package in the file LICENSE.txt.
 */

/**
 * NP_Service_Gravatar_Profiles tests.
 *
 * @group NP-Gravatar
 * @group NP-Gravatar_Service
 * @group NP-Gravatar_Service_Profiles
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles.php';

class NP_Service_Gravatar_Profiles_GetProfileInfoTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var NP_Service_Gravatar_Profiles
     */
    protected $_gravatarService;

    /**
     * @var Zend_Http_Client
     */
    protected $_httpClient;

    /**
     * @var Zend_Http_Client_Adapter_Abstract
     */
    protected $_httpAdapter;

    protected static $_responseFormatsContentTypes = array(
        'php'=>array('type'=>'text/plain', 'response'=>null),
        'json'=>array('type'=>'application/json', 'response'=>'json_response.json'),
        'xml'=>array('type'=>'application/xml', 'response'=>'xml_response.xml'),
        'QRCode'=>array('type'=>'image/png', 'response'=>null),
        'VCard'=>array('type'=>'text/directory', 'response'=>null)
    );

    protected static $_accountEmail = null;

    public static function setUpBeforeClass()
    {
        $path = dirname(__FILE__) . '/ResponseFormat/_files';
        foreach (self::$_responseFormatsContentTypes as $key=>$data) {
            if ($data['response'] !== null) {
                self::$_responseFormatsContentTypes[$key]['response'] = $path . DIRECTORY_SEPARATOR . $data['response'];
            }
        }

        $accountEmail = trim(GRAVATAR_ACCOUNT_EMAIL);
        if (!empty($accountEmail)) {
            self::$_accountEmail = $accountEmail;
        }

    }
    
    protected function setUp()
    {
        $this->_gravatarService = new NP_Service_Gravatar_Profiles();

        if (self::$_accountEmail === null) {
            require_once 'Zend/Http/Client/Adapter/Test.php';
            //Do not perform online tests
            $this->_httpAdapter = new Zend_Http_Client_Adapter_Test();
            $this->_httpClient = new Zend_Http_Client(
                'http://foo',
                array('adapter' => $this->_httpAdapter)
            );

            $this->_gravatarService->setHttpClient($this->_httpClient);
        }
        
    }

    //Helper methods
    protected function _setHttpResponse($data, $contentType)
    {
        $response = self::_createHttpResponseFrom($data, $contentType);

        $this->_httpAdapter->setResponse($response);
    }

    protected static function _createHttpResponseFrom($data, $contentType = 'application/xml', $status = 200, $message = 'OK')
    {
        $headers = array("HTTP/1.1 $status $message",
                         "Status: $status",
                         "Content-Type: $contentType; charset=utf-8",
                         'Content-Length: ' . strlen($data)
        );
        
        return implode("\r\n", $headers) . "\r\n\r\n$data\r\n\r\n";
    }
    
    public function testGetProfileDataForceResponseRetval()
    {
        foreach (self::$_responseFormatsContentTypes as $respFormat=>$data) {
            $retval = null;
            
            $this->_gravatarService->setResponseFormat($respFormat);

            if (self::$_accountEmail !== null) { //Perform online tests?
                $retval = $this->_gravatarService->getProfileInfo(self::$_accountEmail, true);

                if ($respFormat == 'VCard') {
                    $this->assertRegexp('/\.vcf/i', $retval->getHeader('Content-disposition'));
                }
            } else {
                if ($data['response'] !== null) {
                    $this->_setHttpResponse(
                        file_get_contents($data['response']),
                        $data['type']
                    );

                    $retval = $this->_gravatarService->getProfileInfo('foo@bar.com', true);
                    $this->assertTrue($retval instanceof Zend_Http_Response);
                    $this->assertRegExp('#' . $data['content_type'] . '#i', $retval->getHeader('Content-type'));
                }
            }

            if ($retval !== null) {
                $this->assertTrue($retval instanceof Zend_Http_Response);
                $this->assertRegExp('#' . $data['type'] . '#i', $retval->getHeader('Content-type'));
            }
        }
    }

    public function testGetProfileDataMethod()
    {
        foreach (self::$_responseFormatsContentTypes as $respFormat=>$data) {
            $retval = null;

            $this->_gravatarService->setResponseFormat($respFormat);

            if (self::$_accountEmail !== null) { //Perform online tests?
                $retval = $this->_gravatarService->getProfileInfo(self::$_accountEmail);
            } else {
                if ($data['response'] !== null) {
                    $this->_setHttpResponse(
                        file_get_contents($data['response']),
                        $data['type']
                    );

                    $retval = $this->_gravatarService->getProfileInfo('foo@bar.com');
                }
            }

            if ($retval !== null) {
                if (
                    $this->_gravatarService->getResponseFormat()
                    instanceof NP_Service_Gravatar_Profiles_ResponseFormat_ParserInterface
                ) {
                    $this->assertTrue($retval instanceof NP_Service_Gravatar_Profiles_Profile);
                } else {
                    $this->assertTrue($retval instanceof Zend_Http_Response);
                }
            }
        }
    }
}