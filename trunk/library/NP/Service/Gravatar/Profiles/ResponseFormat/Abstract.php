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
 * Abstract response format.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 * @license New BSD License
 */
abstract class NP_Service_Gravatar_Profiles_ResponseFormat_Abstract
{
    /**
     * Gets response format id.
     * 
     * @return string
     */
    abstract public function getResponseFormatId();

    /**
     * __toString() implementation.
     *
     * @return string
     */
    public function  __toString()
    {
        return $this->getResponseFormatId();
    }
}