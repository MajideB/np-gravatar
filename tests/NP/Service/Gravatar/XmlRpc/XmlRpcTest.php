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
 * NP_Service_Gravatar_XmlRpc tests.
 *
 * @group NP-Gravatar
 * @group NP-Gravatar_Service
 * @group NP-Gravatar_Service_XmlRpc
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'NP/Service/Gravatar/XmlRpc.php';

require_once 'Zend/XmlRpc/Client.php';

class NP_Service_Gravatar_Profiles_XmlRpcTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var NP_Service_Gravatar_XmlRpc
     */
    protected $_gravatarXmlRpc;

    protected $_xmlRpcMock;

    protected static $_apiKey = 'foo123';

    protected static $_accountEmail = 'foo@bar.com';

    protected static $_onlineTest = false;

    public static function setUpBeforeClass()
    {
        $apiKey = trim(GRAVATAR_API_KEY);
        $accountEmail = trim(GRAVATAR_ACCOUNT_EMAIL);

        if (!empty($apiKey) && !empty($accountEmail)){
            self::$_apiKey = $apiKey;
            self::$_accountEmail = $accountEmail;
            self::$_onlineTest = true;
        }
    }

    protected function setUp()
    {
        $this->_gravatarXmlRpc = new NP_Service_Gravatar_XmlRpc(self::$_apiKey, self::$_accountEmail);
    }

    protected function _mockXmlRpcClient($method, $retval, $params = array())
    {
        $this->_xmlRpcMock = $this->getMock(
            'Zend_XmlRpc_Client',
            array('setSkipSystemLookup', 'call'),
            array('http://foo')
        );
        $this->_xmlRpcMock->expects($this->once())
                 ->method('setSkipSystemLookup')
                 ->with(true);

        $params = array_merge($params, array('apikey'=>$this->_gravatarXmlRpc->getApiKey()));
        $this->_xmlRpcMock->expects($this->once())
                 ->method('call')
                 ->with(
                     'grav.' . $method,
                     array(Zend_XmlRpc_Value::getXmlRpcValue($params, Zend_XmlRpc_Value::XMLRPC_TYPE_STRUCT))
                 )
                ->will($this->returnValue($retval));

        $this->_gravatarXmlRpc->setXmlRpcClient($this->_xmlRpcMock);
    }

    public function testApiKeyAndEmailAreSetFromTheConstructor()
    {
        $this->assertSame($this->_gravatarXmlRpc->getApiKey(), self::$_apiKey);
        $this->assertSame($this->_gravatarXmlRpc->getEmail(), self::$_accountEmail);
    }

    public function testSettingXmlRpcClientSetsUriOfItsHttpClient()
    {
        $this->_gravatarXmlRpc->setXmlRpcClient(new Zend_XmlRpc_Client('http://foo'));
        
        $uri = $this->_gravatarXmlRpc->getXmlRpcClient()->getHttpClient()->getUri();
        
        $this->assertContains($uri->getHost(), NP_Service_Gravatar_XmlRpc::SECURE_XMLRPC_SERVER);
        $this->assertEquals('user=' . NP_Service_Gravatar_Utility::emailHash($this->_gravatarXmlRpc->getEmail()), $uri->getQuery());
    }

    public function testTestingMethod()
    {
        if (self::$_onlineTest) {
            $result = $this->_gravatarXmlRpc->test();

            $this->assertTrue(array_key_exists('apikey', $result));
            $this->assertTrue(array_key_exists('response', $result));
        } else {
            $retval = array('apikey'=>'foo', 'response'=>'bar');

            $this->_mockXmlRpcClient('test', $retval);

            $this->assertEquals($retval, $this->_gravatarXmlRpc->test());
        }
    }

    public function testExistsMethodStringAsParamShouldBeConvertedToArray()
    {
        $email = 'foo@bar.com';

        $params = $params = array(
            'hashes'=>array_map(array('NP_Service_Gravatar_Utility', 'emailHash'), array($email))
        );
        $retval = array($email=>true);
        $this->_mockXmlRpcClient('exists', $retval, $params);

        $this->assertTrue((bool)current($this->_gravatarXmlRpc->exists($email)));
    }

    public function testExistsMethodMultipleEmails()
    {
        $emails = array('foo@bar.com', 'bar@baz.com');

        $params = $params = array(
            'hashes'=>array_map(array('NP_Service_Gravatar_Utility', 'emailHash'), $emails)
        );
        $retval = array_combine($emails, array(true, false));
        $this->_mockXmlRpcClient('exists', $retval, $params);

        $result = $this->_gravatarXmlRpc->exists($emails);

        $values = array_values($result);
        $this->assertTrue((bool)$values[0]);
        $this->assertFalse((bool)$values[1]);
    }

    public function testAdressesMethodRawResult()
    {
        $retval = array(
            'foo@bar.com'=>array(
                'userimage'=>'123',
                'userimage_url'=>'http://gravatar.com/foo.png',
                'rating'=>NP_Service_Gravatar_XmlRpc::G_RATED
            ),
            'bar@baz.com'=>array(
                'userimage'=>'123',
                'userimage_url'=>'http://gravatar.com/bar.jpg',
                'rating'=>NP_Service_Gravatar_XmlRpc::PG_RATED
            )
        );
        $this->_mockXmlRpcClient('addresses', $retval);
        
        $rawResult = $this->_gravatarXmlRpc->addresses(true);
        $this->assertEquals($retval, $rawResult);
    }

    public function testAdressesMethodNormalResult()
    {
        $retval = array(
            'foo@bar.com'=>array(
                'userimage'=>'123',
                'userimage_url'=>'http://gravatar.com/foo.png',
                'rating'=>NP_Service_Gravatar_XmlRpc::G_RATED
            ),
            'bar@baz.com'=>array(
                'userimage'=>'123',
                'userimage_url'=>'http://gravatar.com/bar.jpg',
                'rating'=>NP_Service_Gravatar_XmlRpc::PG_RATED
            )
        );
        $this->_mockXmlRpcClient('addresses', $retval);

        $result = $this->_gravatarXmlRpc->addresses();
        $address = $result[0];
        $this->assertTrue($address instanceof NP_Service_Gravatar_XmlRpc_UserEmail);
        $this->assertTrue($address->getImage() instanceof NP_Service_Gravatar_XmlRpc_UserImage);
    }

    public function testUserImagesMethod()
    {
        $retval = array(
            'foo@bar.com'=>array(
                NP_Service_Gravatar_XmlRpc::G_RATED,
                'http://gravatar.com/foo.png'
            ),
            'bar@baz.com'=>array(
                NP_Service_Gravatar_XmlRpc::PG_RATED,
                'http://gravatar.com/bar.jpg'
            )
        );
        $this->_mockXmlRpcClient('userimages', $retval);

        $result = $this->_gravatarXmlRpc->userImages();

        $image = $result[0];
        $this->assertTrue($image instanceof NP_Service_Gravatar_XmlRpc_UserImage);
        $this->assertTrue($image->getUrl() instanceof Zend_Uri_Http);
        $this->assertTrue(array_key_exists($image->getRating(), NP_Service_Gravatar_XmlRpc::getValidRatings()));
    }

    public function testSaveDataMethodShouldThrowExceptionOnInvalidRating()
    {
        $this->setExpectedException('NP_Service_Gravatar_XmlRpc_Exception');

        $this->_gravatarXmlRpc->saveData('/path/to/image.jpg', 77);
    }

    public function testSaveDataMethodPath()
    {
        $path = dirname(__FILE__) . '/_files/defaultGravatarThumb.png';
        $rating = NP_Service_Gravatar_XmlRpc::PG_RATED;

        $retval = true;
        $params = array(
            'data'=>base64_encode(file_get_contents($path)),
            'rating'=>$rating
        );
        $this->_mockXmlRpcClient('saveData', $retval, $params);

        $this->assertEquals($retval, $this->_gravatarXmlRpc->saveData($path, $rating));
    }

    public function testSaveUrlMethodShouldThrowExceptionOnInvalidRating()
    {
        $this->setExpectedException('NP_Service_Gravatar_XmlRpc_Exception');

        $this->_gravatarXmlRpc->saveUrl('www.example.com/path/to/image.jpg', 77);
    }

    public function testSaveUrlMethod()
    {
        $url = 'http://foobar.com/foo.jpg';
        $rating = NP_Service_Gravatar_XmlRpc::G_RATED;

        $retval = 'image123';
        $params = array(
            'url'=>$url,
            'rating'=>$rating
        );
        $this->_mockXmlRpcClient('saveUrl', $retval, $params);

        $this->assertEquals($retval, $this->_gravatarXmlRpc->saveUrl($url, $rating));
    }

    public function testUseUserImageMethod()
    {
        $imageId = 'image123';
        $email = 'foo@bar.com';

        $retval = array($email=>true);
        $params = array(
            'userimage'=>$imageId,
            'addresses'=>array($email)
        );
        $this->_mockXmlRpcClient('useUserimage', $retval, $params);

        $this->assertEquals($retval, $this->_gravatarXmlRpc->useUserImage($imageId, $email));
    }

    public function testRemoveImageMethod()
    {
        $emails = array('foo@bar.com', 'bar@baz.com');

        $params = array(
            'addresses'=>$emails
        );
        $retval = array_combine($emails, array(true, false));
        $this->_mockXmlRpcClient('removeImage', $retval, $params);
        
        $this->assertEquals($retval, $this->_gravatarXmlRpc->removeImage($emails));
    }

    public function testDeleteUserImageMethod()
    {
        $imageId = 'image123';

        $params = array(
            'userimage'=>$imageId
        );
        $this->_mockXmlRpcClient('deleteUserimage', true, $params);

        $this->assertTrue($this->_gravatarXmlRpc->deleteUserImage($imageId));
    }
}