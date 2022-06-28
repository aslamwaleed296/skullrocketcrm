<?php 

$GLOBALS['log']->fatal("DATA", $_POST['From'], $_POST['Body']);
$GLOBALS['log']->fatal("ALL DATASSS", $_POST);

global $db, $sugar_config;

$from = $_POST['From'];
$body = $_POST['Body'];

$lead_id = $db->getOne("SELECT id FROM leads WHERE deleted=0 AND phone_mobile='".$from."'");
$GLOBALS['log']->fatal("lead_id", $lead_id);
$smsBean = BeanFactory::newBean('sr_sms');
$smsBean->name = "Incoming SMS from '".$from."'";
$smsBean->to_number = $sugar_config['TWILIO_CALLER_ID'];
$smsBean->from_number = $from;
$smsBean->direction = 'Inbound';
$smsBean->status = 'sent';
$smsBean->description = $body;

if ($lead_id) {
    $smsBean->lead_id = $lead_id;
}

$smsBean->save();

header('Content-Type: text/xml');
?>

<Response>
    <Message>
        Your message is received. Let us get back to you shortly.
    </Message>
</Response>