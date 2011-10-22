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
 * Common unit tests for response formats of the
 * NP_Service_Gravatar_Profiles.
 *
 * @group NP-Gravatar
 * @group NP-Gravatar_Service
 * @group NP-Gravatar_Service_Profiles
 * @group NP-Gravatar_Service_Profiles_ResponseFormat
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles/ResponseFormat/Abstract.php';

class NP_Service_Gravatar_Profiles_ResponseFormat_AbstractTest extends PHPUnit_Framework_TestCase
{
    protected $_responseFormat;

    protected function setUp()
    {
        $this->_responseFormat = new ResponseFormat_AbstractTest_CustomFormat();
    }

    public function testResponseFormatId()
    {
        $this->assertSame($this->_responseFormat->getResponseFormatId(), 'foo');
        $this->assertSame((string)$this->_responseFormat, 'foo');
    }
}

class ResponseFormat_AbstractTest_CustomFormat
    extends NP_Service_Gravatar_Profiles_ResponseFormat_Abstract
{
    public function getResponseFormatId()
    {
        return 'foo';
    }
}