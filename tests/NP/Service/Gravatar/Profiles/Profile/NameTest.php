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
 * NP_Service_Gravatar_Profiles Name class tests.
 *
 * @group NP-Gravatar
 * @group NP-Gravatar_Service
 * @group NP-Gravatar_Service_Profiles
 * @group NP-Gravatar_Service_Profiles_Profile
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles/Profile/Name.php';

class NP_Service_Gravatar_Profiles_Profile_NameTest extends PHPUnit_Framework_TestCase
{
    protected static $_nameData = array(
        'givenName'=>'Foo',
        'familyName'=>'Bar',
        'formatted'=>'Foo Bar'
    );

    public function testConstructor()
    {
        $name = new NP_Service_Gravatar_Profiles_Profile_Name(self::$_nameData);

        $this->assertSame($name->givenName, self::$_nameData['givenName']); //__get
        $this->assertSame($name->getFamilyName(), self::$_nameData['familyName']);
        $this->assertSame($name->getFormatted(), self::$_nameData['formatted']);
    }
}