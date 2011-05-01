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
 * NP_Service_Gravatar_Profiles Account class tests.
 *
 * @group NP-Gravatar
 * @group NP-Gravatar_Service
 * @group NP-Gravatar_Service_Profiles
 * @group NP-Gravatar_Service_Profiles_Profile
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/Profiles/Profile/Account.php';

class NP_Service_Gravatar_Profiles_Profile_AccountTest extends PHPUnit_Framework_TestCase
{
    protected $_profileAccount;

    protected static $_accountData = array(
        'domain'=>'facebook.com',
        'username'=>'foobar',
        'display'=>'foobar',
        'url'=>'http://www.facebook.com/foobar',
        'verified'=>true,
        'shortname'=>'facebook'
    );

    protected function setUp()
    {
        $this->_profileAccount = new NP_Service_Gravatar_Profiles_Profile_Account();
    }

    public function testConstructor()
    {
        $account = new NP_Service_Gravatar_Profiles_Profile_Account(self::$_accountData);

        $this->assertSame($account->domain, self::$_accountData['domain']); //__get
        $this->assertSame($account->getUsername(), self::$_accountData['username']);
        $this->assertSame($account->display, self::$_accountData['display']);
        $this->assertTrue($account->getUrl() instanceof Zend_Uri_Http);
        $this->assertTrue($account->verified);
        $this->assertSame($account->getShortname(), self::$_accountData['shortname']);
    }

    public function testInvalidUrlShouldThrowException()
    {
        $this->setExpectedException('NP_Service_Gravatar_Exception');

        $this->_profileAccount->setUrl('invalid');
    }

    public function testSettingVerifiedAsStringShouldBeNormalizedToBool()
    {
         $this->_profileAccount->verified = 'true'; //__set

         $this->assertTrue($this->_profileAccount->getVerified());
         $this->assertTrue(is_bool($this->_profileAccount->getVerified()));
    }
}