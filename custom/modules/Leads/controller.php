<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
require_once 'custom/include/twilio/helpers/CallsHelper.php';

class CustomLeadsController extends SugarController {
    public function action_getToken() {
        $GLOBALS['log']->fatal(__FUNCTION__);
        ob_clean();
        $helper = new CallsHelper();
        $token = $helper->getAccessToken();
        echo $token;
        exit();
    }
}