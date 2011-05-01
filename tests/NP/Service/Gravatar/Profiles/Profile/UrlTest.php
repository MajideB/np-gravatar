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
 * NP_Service_Gravatar_Profiles Url class tests.
 *
 * @group NP-Gravatar
 * @group NP-Gravatar_Service
 * @group NP-Gravatar_Service_Profiles
 * @group NP-Gravatar_Service_Profiles_Profile
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles/Profile/Url.php';

class NP_Service_Gravatar_Profiles_Profile_UrlTest extends PHPUnit_Framework_TestCase
{
    protected $_url;

    protected static $_urlData = array('title'=>'Google', 'value'=>'http://www.google.com');

    protected function setUp()
    {
        $this->_url = new NP_Service_Gravatar_Profiles_Profile_Url();
    }

    public function testConstructor()
    {
        $url = new NP_Service_Gravatar_Profiles_Profile_Url(self::$_urlData);

        $this->assertSame($url->title, self::$_urlData['title']); //__get
        $this->assertTrue($url->getValue() instanceof Zend_Uri_Http);
    }

    public function testInvalidUrlShouldThrowException()
    {
        $this->setExpectedException('NP_Service_Gravatar_Exception');

        $this->_url->setValue('invalid');
    }
}