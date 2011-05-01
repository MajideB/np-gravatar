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
 * Provides API for easier access to the profile properties.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
abstract class NP_Service_Gravatar_Profiles_Profile_Abstract implements ArrayAccess
{
    /**
     * Plugin loader for profile classes.
     *
     * @var Zend_Loader_PluginLoader
     */
    protected static $_pluginLoader;

    /**
	 * Constructor.
	 *
	 * @param array Options for object's initialization.
	 * @return void
	 */
    public function __construct(array $options = null)
    {
        if ($options) {
            $this->setOptions($options);
        }
    }

    /**
     * Gets profile classes plugin loader.
     *
     * @return Zend_Loader_PluginLoader
     */
    public static function getPluginLoader()
    {
        if (!self::$_pluginLoader) {
            require_once 'Zend/Loader/PluginLoader.php';
            self::$_pluginLoader = new Zend_Loader_PluginLoader(array(
                'NP_Service_Gravatar_Profiles_Profile_'=>'NP/Service/Gravatar/Profiles/Profile/')
            );
        }

        return self::$_pluginLoader;
    }

    /**
	 * Sets object state.
	 *
	 * @param array Object members.
	 * @return NP_Service_Gravatar_Profiles_Profile_Abstract
	 */
	public function setOptions(array $options)
    {
        foreach ($options as $key=>$value) {
			$this->$key = $value;
        }

        return $this;
    }

    /**
	 * Allows access to properties of this class.
	 *
	 * @param string Name of the property.
	 * @param mixed Value that will be set.
	 * @return mixed
	 */
	public function __set($name, $value)
    {
        $method = 'set' . ucfirst($name);
        
        if (method_exists($this, $method)) { //Only if property exists.
             return $this->$method($value);
        }
    }

	/**
	 * Allows access to properties of this class.
	 *
	 * @param string Name of the property.
	 * @return mixed
	 */
	public function __get($name)
    {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) { //Only if property exists.
            return $this->$method();
        } else {
            return null;
        }
    }

    /**
     * Generates and returns value for some array-type property in some
     * of Profile classes, based on supplied $data array, and creates
     * $profileClass instances if necessary, so that value of $property
     * is collection of $profileClass instances.
     *
     * @param string $profileClass Name of the profile class.
     * @param array $data Data that needs to be set for $property.
     * @return array|null
     */
    protected function _normalizeArrayPropertyValue($profileClass, array $data)
    {
        $className = '';
        try {
            $className = self::getPluginLoader()->load($profileClass);
        } catch (Zend_Loader_PluginLoader_Exception $e) {
            require_once 'NP/Service/Gravatar/Exception.php';
            throw new NP_Service_Gravatar_Exception($e->getMessage());
        }

        $value = array();

        $current = current($data);
        if (!is_array($current) && !$current instanceof $className) {
            $data = array($data);
        }

        foreach ($data as $val) {
            $profileInstance = $this->_normalizePropertyValue($profileClass, $val);
            if ($profileInstance !== null) {
                $value[] = $profileInstance;
            }
        }

        //Valid value? Return it. Otherwise, return null.
        return (!empty($value)) ? $value : null;
    }

    /**
     * Generates and returns value for some property that holds instance
     * of some Profile class.
     *
     * @param string $profileClass
     * @param mixed $value
     * @return NP_Service_Gravatar_Profiles_Profile_Abstract|null
     */
    protected function _normalizePropertyValue($profileClass, $value)
    {
        try {
            $className = self::getPluginLoader()->load($profileClass);
        } catch (Zend_Loader_PluginLoader_Exception $e) {
            require_once 'NP/Service/Gravatar/Exception.php';
            throw new NP_Service_Gravatar_Exception($e->getMessage());
        }

        if ($value instanceof $className) { //Already instantiated?
            return $value;
        } elseif (is_array($value)) { //Array? Generate profile instance.
            return new $className($value);
        }

        return null;
    }

    //ArrayAccess interface implementation

	/**
	 * Required by the ArrayAccess implementation.
	 * Gets object member for the given offset.
	 *
	 * @param string Offset.
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
        return $this->$offset;
    }

	/**
	 * Required by the ArrayAccess implementation.
	 *
	 * @param string Offset.
	 * @param mixed Value to set.
	 * @return Model_Base
	 */
	public function offsetSet($offset, $value)
	{
		$this->$offset = $value;
    }

	/**
	 * Required by the ArrayAccess implementation.
	 * Checks if offset exists.
	 *
	 * @param string Offset.
	 * @return bool
	 */
    public function offsetExists($offset)
	{
        return (null === $this->$offset);
    }

	/**
	 * Required by the ArrayAccess implementation.
	 * Does nothing.
	 *
	 * @param string Offset.
	 * @return void
	 */
    public function offsetUnset($offset)
	{
    }
}