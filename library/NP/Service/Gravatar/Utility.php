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
 * Collection of utilities that are used in various
 * NP_Service_Gravatar classes.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
class NP_Service_Gravatar_Utility
{
    /**
     * Generates email hash.
     *
     * @param string $email Email address.
     * @return string
     */
    public static function emailHash($email)
    {
        return md5((string)$email);
    }
    
    /**
     * Parses, validates and returns a valid Zend_Uri object
     * from given $value.
     *
     * @param string|Zend_Uri_Http $value
     * @return Zend_Uri_Http
     */
    public static function normalizeUri($value)
    {
        require_once 'Zend/Uri.php';
        require_once 'Zend/Uri/Http.php';

        if ($value instanceof Zend_Uri_Http) {
            return $value;
        }
        
        try {
            $uri = Zend_Uri::factory((string)$value);
        } catch (Exception $e) {
            require_once 'NP/Service/Gravatar/Exception.php';
            throw new NP_Service_Gravatar_Exception($e->getMessage());
        }

        return $uri;
    }

    /**
     * Normalizes bool values - converts strings containing
     * "true" to boolean true.
     *
     * @param mixed $value
     * @return bool
     */
    public static function normalizeBool($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        return (preg_match('/true/i', (string)$value) === 1) ? true : false;
    }
}