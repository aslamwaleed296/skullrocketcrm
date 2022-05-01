<?php 

require_once 'vendor/autoload.php';
use Twilio\TwiML\VoiceResponse;

$GLOBALS['log']->fatal("CALL RECORDING ENTRY POINT");
$GLOBALS['log']->fatal($_REQUEST);

function get_voice_response($phone) {
    global $sugar_config;
    $response = new VoiceResponse();
    if ($phone == $_ENV['TWILIO_CALLER_ID']) {
        $GLOBALS['log']->fatal("INCOMING...");
        # Receiving an incoming call to the browser from an external phone
        $response = new VoiceResponse();
        $dial = $response->dial('');
        $dial->client($_SESSION['identity']);
    } else if (!empty($phone) && strlen($phone) > 0) {
        $GLOBALS['log']->fatal("OUTGOING...");
        $number = htmlspecialchars($phone);
        $dial = $response->dial('', ['callerId' => $sugar_config['TWILIO_CALLER_ID']]);
        
        // wrap the phone number or client name in the appropriate TwiML verb
        // by checking if the number given has only digits and format symbols
        if (preg_match("/^[\d\+\-\(\) ]+$/", $number)) {
            $dial->number($number);
        } else {
            $dial->client($number);
        }
    } else {
        $response->say("Thanks for calling!");
    }
    $GLOBALS['log']->fatal("RESPONSE IN ENTRY POINT", (string)$response);
    return (string)$response;
}

// get the phone number from the page request parameters, if given
header('Content-Type: text/xml');
$toNumber = $_REQUEST['To'] ?? null;
echo get_voice_response($toNumber);