<?php 

require_once 'vendor/autoload.php';

use Twilio\Rest\Client;

class MessagesHelper {
    public $from;
    public $account_sid;
    public $auth_token;
   
    public function sendMessage($to, $body)
    {
        $twilio = new Client($this->account_sid, $this->auth_token);
        $message = $twilio->messages->create($to, ["body" => $body, "from" => $this->from]);
        return $message;
    }

    public function createSMS($msg, $lead_id)
    {
        $smsBean = BeanFactory::newBean('sr_sms');
        $smsBean->name = $msg->sid;
        $smsBean->to_number = $msg->to;
        $smsBean->from_number = $msg->from;
        $smsBean->direction = 'Inbound';
        if ($msg->direction == 'outbound-api') {
            $smsBean->direction = 'Outbound';
        }
        $smsBean->status = 'sent';
        $smsBean->description = $msg->body;
        $smsBean->lead_id = $lead_id;
        $smsBean->save();
        return true;
    }
}