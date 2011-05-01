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
    protected $_gravatarService;

    protected static $_responseFormatsContentTypes = array(
        'php'=>'text/plain',
        'json'=>'application/json',
        'xml'=>'application/xml',
        'QRCode'=>'image/png',
        'VCard'=>'text/directory'
    );

    protected function setUp()
    {
        $this->_gravatarService = new NP_Service_Gravatar_Profiles();
    }

    public function testGetProfileDataForceResponseRetval()
    {
        foreach (self::$_responseFormatsContentTypes as $respFormat=>$type) {
            $this->_gravatarService->setResponseFormat($respFormat);

            $retval = $this->_gravatarService->getProfileInfo(GRAVATAR_ACCOUNT_EMAIL, true);

            $this->assertTrue($retval instanceof Zend_Http_Response);
            $this->assertRegExp('#' . $type . '#i', $retval->getHeader('Content-type'));
            if ($respFormat == 'VCard') {
                $this->assertRegexp('/\.vcf/i', $retval->getHeader('Content-disposition'));
            }
        }
    }
}