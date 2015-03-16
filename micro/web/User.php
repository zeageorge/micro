<?php /** MicroUser */

namespace Micro\web;

use Micro\base\Registry;

/**
 * Micro user class file
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/lugnsk/micro
 * @copyright Copyright &copy; 2013 Oleg Lunegov
 * @license /LICENSE
 * @package micro
 * @subpackage web\helpers
 * @version 1.0
 * @since 1.0
 */
class User
{
    /**
     * Set User ID
     *
     * @access public
     * @global      Registry
     *
     * @param mixed $id user id
     *
     * @return void
     */
    public function setID($id)
    {
        Registry::get('session')->UserID = $id;
    }

    /**
     * Check access by current user
     *
     * @access public
     * @global       Registry
     *
     * @param string $permission permission to check
     * @param array $data arguments
     *
     * @return bool
     */
    public function check($permission, array $data = [])
    {
        if (!$this->isGuest()) {
            return Registry::get('permission')->check($this->getID(), $permission, $data);
        } else {
            return false;
        }
    }

    /**
     * Login user
     *
     * @access public
     *
     * @param int|string $userId User identify
     *
     * @return void
     */
    public function login($userId)
    {
        $this->setID($userId);
    }

    /**
     * Logout user
     *
     * @access public
     *
     * @return void
     */
    public function logout()
    {
        if (!$this->isGuest()) {
            $this->setID(null);
            Registry::get('session')->destroy();
        }
    }

    /**
     * Get state user
     *
     * @access public
     * @global Registry
     * @return bool
     */
    public function isGuest()
    {
        return !Registry::get('session') || !Registry::get('session')->UserID;
    }

    /**
     * Get user ID
     *
     * @access public
     * @global Registry
     * @return bool|integer
     */
    public function getID()
    {
        return (!$this->isGuest()) ? Registry::get('session')->UserID : false;
    }

    /**
     * Get captcha code
     *
     * @access public
     * @global Registry
     * @return string
     */
    public function getCaptcha()
    {
        return Registry::get('session')->captchaCode;
    }

    /**
     * Make captcha from source
     *
     * @access public
     *
     * @param string $code source captcha
     *
     * @return string
     */
    public function makeCaptcha($code)
    {
        return md5($code);
    }
}