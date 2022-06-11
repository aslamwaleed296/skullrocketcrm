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
            $save = $helper->saveRecording($callId, $recordingId);
            if ($save) {
                $GLOBALS['log']->fatal("REMOVE CALL RECORDING FROM TWILIO");
                $helper->removeCallRecordingFromTwilio($recordingId);
            }
        }
    }
}