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
 * NP_Service_Gravatar_Profiles Email class tests.
 *
 * @group NP-Gravatar
 * @group NP-Gravatar_Service
 * @group NP-Gravatar_Service_Profiles
 * @group NP-Gravatar_Service_Profiles_Profile
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles/Profile/Email.php';

class NP_Service_Gravatar_Profiles_Profile_EmailTest extends PHPUnit_Framework_TestCase
{
    protected $_email;

    protected static $_emailData = array('value'=>'foo@bar.com', 'primary'=>true);

    protected function setUp()
    {
        $this->_email = new NP_Service_Gravatar_Profiles_Profile_Email();
    }

    public function testConstructor()
    {
        $email = new NP_Service_Gravatar_Profiles_Profile_Email(self::$_emailData);

        $this->assertSame($email->value, self::$_emailData['value']); //__get
        $this->assertTrue($email->getPrimary());
    }

    public function testSettingPrimaryAsStringShouldBeNormalizedToBool()
    {
         $this->_email->primary = 'false'; //__set

         $this->assertFalse($this->_email->getPrimary());
         $this->assertTrue(is_bool($this->_email->getPrimary()));
    }
}