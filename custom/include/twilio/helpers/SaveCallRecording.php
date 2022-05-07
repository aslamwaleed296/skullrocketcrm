<?php 

$GLOBALS['log']->fatal("SAVE CALL RECORDING", $_REQUEST);

$status = $_REQUEST['RecordingStatus'];
$recordingId = $_REQUEST['RecordingSid'];
$callSid = $_REQUEST['CallSid'];
$leadId = $_REQUEST['leadId'];
$dateStart = $_REQUEST['RecordingStartTime'];

if (isset($status) && $status == 'completed') {
    require_once 'custom/include/twilio/helpers/CallsHelper.php';
    $helper = new CallsHelper();
    if ($leadId) {
        $callId = $helper->saveCall($leadId, $dateStart, $callSid);
        if ($callId) {
            $helper->saveRecording($callId, $recordingId);
        }
    }
}