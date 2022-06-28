<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class CustomLeadsController extends SugarController {
    public function action_getToken()
    {
        ob_clean();
        $GLOBALS['log']->fatal(__FUNCTION__);
        require_once 'custom/include/twilio/helpers/CallsHelper.php';
        $helper = new CallsHelper();
        $token = $helper->getAccessToken();
        echo $token;
        exit();
    }

    public function action_sendSMS()
    {
        ob_clean();
        global $sugar_config;
        require_once 'custom/include/twilio/helpers/MessagesHelper.php';
        $helper = new MessagesHelper();
        $helper->from = $sugar_config['TWILIO_CALLER_ID'];
        $helper->account_sid = $sugar_config['TWILIO_ACCOUNT_SID'];
        $helper->auth_token = $sugar_config['TWILIO_AUTH_TOKEN'];
        $to = $_REQUEST['to_number'];
        $body = $_REQUEST['message'];
        $lead_id = $_REQUEST['lead_id'];
        $msg = $helper->sendMessage($to, $body);
        if ($msg->sid) {
            $save = $helper->createSMS($msg, $lead_id);
            if ($save) {
                SugarApplication::redirect('index.php?module=Leads&action=DetailView&record='.$lead_id);
                die();
            } else {
                sugar_die("Unable to send sms");
            }
        }
    }
}