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
 * NP_Service_Gravatar_Profiles PhoneNumber class tests.
 *
 * @group NP-Gravatar
 * @group NP-Gravatar_Service
 * @group NP-Gravatar_Service_Profiles
 * @group NP-Gravatar_Service_Profiles_Profile
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles/Profile/PhoneNumber.php';

class NP_Service_Gravatar_Profiles_Profile_PhoneNumberTest extends PHPUnit_Framework_TestCase
{
    protected static $_phoneNumberData = array(
        array('type'=>'mobile', 'value'=>1234567)
    );

    public function testConstructor()
    {
        $phoneNumber = new NP_Service_Gravatar_Profiles_Profile_PhoneNumber(self::$_phoneNumberData);

        $this->assertSame($phoneNumber->type, self::$_phoneNumberData['type']); //__get
        $this->assertSame($phoneNumber->getValue(), self::$_phoneNumberData['value']);
    }
}