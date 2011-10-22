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
 * NP_Service_Gravatar_Profiles common tests.
 *
 * @group NP-Gravatar
 * @group NP-Gravatar_Service
 * @group NP-Gravatar_Service_Profiles
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles.php';

class NP_Service_Gravatar_Profiles_CommonTest extends PHPUnit_Framework_TestCase
{
    protected $_gravatarService;

    protected function setUp()
    {
        $this->_gravatarService = new NP_Service_Gravatar_Profiles();
    }

    public function testPhpShouldBeDefaultResponseFormat()
    {
        $this->assertTrue($this->_gravatarService->getResponseFormat() instanceof NP_Service_Gravatar_Profiles_ResponseFormat_Php);
    }

    public function testSettingResponseFormat()
    {
        $this->_gravatarService->setResponseFormat('xml'); //As string
        $this->assertTrue($this->_gravatarService->getResponseFormat() instanceof NP_Service_Gravatar_Profiles_ResponseFormat_Xml);

        require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/Json.php';
        $this->_gravatarService->setResponseFormat(new NP_Service_Gravatar_Profiles_ResponseFormat_Json()); //As instance
        $this->assertTrue($this->_gravatarService->getResponseFormat() instanceof NP_Service_Gravatar_Profiles_ResponseFormat_Json);
    }

    public function testSettingResponseFormatFromConstructor()
    {
        $gravatarService = new NP_Service_Gravatar_Profiles('QRCode');
        
        $this->assertTrue($gravatarService->getResponseFormat() instanceof NP_Service_Gravatar_Profiles_ResponseFormat_QRCode);
    }

    public function testExceptionShouldBeThrownOnUnsupportedResponseFormat()
    {
        $this->setExpectedException('NP_Service_Gravatar_Profiles_Exception');

        $this->_gravatarService->setResponseFormat('foobar');
    }

    public function testExceptionShouldBeThrownOnInvalidResponseFormat()
    {
        $this->setExpectedException('NP_Service_Gravatar_Profiles_Exception');

        $this->_gravatarService->setResponseFormat(123);
    }
}