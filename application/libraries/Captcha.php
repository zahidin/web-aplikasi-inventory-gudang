<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/phpcaptcha/Captcha.php';

class Captcha extends Captcha
{
    function __construct()
    {
        parent::__construct();
    }
}
