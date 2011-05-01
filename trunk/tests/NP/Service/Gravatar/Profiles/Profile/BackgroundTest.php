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
 * NP_Service_Gravatar_Profiles Background class tests.
 *
 * @group NP-Gravatar
 * @group NP-Gravatar_Service
 * @group NP-Gravatar_Service_Profiles
 * @group NP-Gravatar_Service_Profiles_Profile
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles/Profile/Background.php';

class NP_Service_Gravatar_Profiles_Profile_BackgroundTest extends PHPUnit_Framework_TestCase
{
    protected $_profileBackground;

    protected static $_backgroundData = array(
        'color'=>'#d1d1d1',
        'position'=>'left',
        'repeat'=>'repeat',
        'url'=>'http://www.gravatar.com/bg/1/1111'
    );

    protected function setUp()
    {
        $this->_profileBackground = new NP_Service_Gravatar_Profiles_Profile_Background();
    }

    public function testConstructor()
    {
        $profileBackground = new NP_Service_Gravatar_Profiles_Profile_Background(self::$_backgroundData);

        $this->assertSame($profileBackground->color, self::$_backgroundData['color']); //__get
        $this->assertSame($profileBackground->getPosition(), self::$_backgroundData['position']);
        $this->assertSame($profileBackground->repeat, self::$_backgroundData['repeat']);
        $this->assertTrue($profileBackground->getUrl() instanceof Zend_Uri_Http);
    }

    public function testInvalidUrlShouldThrowException()
    {
        $this->setExpectedException('NP_Service_Gravatar_Exception');

        $this->_profileBackground->setUrl('invalid');
    }
}