<?php

require_once 'vendor/autoload.php';
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VoiceGrant;
use Twilio\Rest\Client;

class CallsHelper {
    public $config;
    public $bean_list;
    public $bean_files;
    public $file_type = '.mp3';
    
    public function __construct() {
        $GLOBALS['log']->fatal("CONSTRUCTOR CALLED");
        global $sugar_config, $beanList, $beanFiles;
        $this->config = $sugar_config;
        $this->bean_list = $beanList;
        $this->bean_files = $beanFiles;
    }

    public function getAccessToken() {
        $GLOBALS['log']->fatal(__FUNCTION__);
        // An identifier for your app - can be anything you'd like
        $identity = $this->getRandomUsername();
        if (!session_id()) {
            session_start();
        }
        $_SESSION['identity'] = $identity;
        $GLOBALS['log']->fatal("getAccessToken", $identity, $this->config['TWILIO_ACCOUNT_SID'],$this->config['TWILIO_API_KEY'],$this->config['TWILIO_API_SECRET'], $this->config['TWILIO_TWIML_APP_SID']);

        // Create access token, which we will serialize and send to the client
        $token = new AccessToken(
            $this->config['TWILIO_ACCOUNT_SID'],
            $this->config['TWILIO_API_KEY'],
            $this->config['TWILIO_API_SECRET'],
            3600,
            $identity
        );
        
        // Create Voice grant
        $voiceGrant = new VoiceGrant();
        $voiceGrant->setOutgoingApplicationSid($this->config['TWILIO_TWIML_APP_SID']);

        // Optional: add to allow incoming calls
        $voiceGrant->setIncomingAllow(true);
        
        // Add grant to token
        $token->addGrant($voiceGrant);
        $GLOBALS['log']->fatal("token", $token->toJWT());
        // render token to string
        return $token->toJWT();
    }

    public function getRandomUsername() {
        $ADJECTIVES = array(
            'Awesome', 'Bold', 'Creative', 'Dapper', 'Eccentric', 'Fiesty', 'Golden',
            'Holy', 'Ignominious', 'Jolly', 'Kindly', 'Lucky', 'Mushy', 'Natural',
            'Oaken', 'Precise', 'Quiet', 'Rowdy', 'Sunny', 'Tall',
            'Unique', 'Vivid', 'Wonderful', 'Xtra', 'Yawning', 'Zesty',
        );
    
        $FIRST_NAMES = array(
            'Anna', 'Bobby', 'Cameron', 'Danny', 'Emmett', 'Frida', 'Gracie', 'Hannah',
            'Isaac', 'Jenova', 'Kendra', 'Lando', 'Mufasa', 'Nate', 'Owen', 'Penny',
            'Quincy', 'Roddy', 'Samantha', 'Tammy', 'Ulysses', 'Victoria', 'Wendy',
            'Xander', 'Yolanda', 'Zelda',
        );
    
        $LAST_NAMES = array(
            'Anchorage', 'Berlin', 'Cucamonga', 'Davenport', 'Essex', 'Fresno',
            'Gunsight', 'Hanover', 'Indianapolis', 'Jamestown', 'Kane', 'Liberty',
            'Minneapolis', 'Nevis', 'Oakland', 'Portland', 'Quantico', 'Raleigh',
            'SaintPaul', 'Tulsa', 'Utica', 'Vail', 'Warsaw', 'XiaoJin', 'Yale',
            'Zimmerman',
        );
    
        // Choose random components of username and return it
        $adj = $ADJECTIVES[array_rand($ADJECTIVES)];
        $fn = $FIRST_NAMES[array_rand($FIRST_NAMES)];
        $ln = $LAST_NAMES[array_rand($LAST_NAMES)];
        
        return $adj . $fn . $ln;
    }

    public function getRecording($recordingId, $file_type) {
        $sid = $this->config['TWILIO_ACCOUNT_SID'];
        $token = $this->config['TWILIO_AUTH_TOKEN'];
        $twilio = new Client($sid, $token);
        
        $recording = $twilio->recordings($recordingId)->fetch();
        $context = stream_context_create(["http"=>["header"=>"Authorization: Basic ".base64_encode("{$sid}:{$token}")]]);
        
        $GLOBALS['log']->fatal("CONTEXT", $context);
        $GLOBALS['log']->fatal("RECORDING URI", $recording->uri);
        
        $uri = 'https://api.twilio.com'.str_replace('.json', $file_type, $recording->uri);
        $recording_file = file_get_contents($uri, false, $context);
        
        return $recording_file;
    }

    public function saveCall($leadId, $dateStart, $callSid) {
        $call_class = $this->bean_list['Calls'];

        $GLOBALS['log']->fatal($call_class);
        $GLOBALS['log']->fatal($this->bean_files[$call_class]);
    
        // Create Call
        require_once($this->bean_files[$call_class]);
        $call = new $call_class();
        $call->parent_id = $leadId;
        $call->parent_type = 'Leads';
        $call->date_start = date_format(date_create($dateStart),"Y-m-d H:i:s");
        $call->name = "Call - " . $callSid;
        $call->assigned_user_id = '1';
        $call->status = 'Planned';
        $call->direction = 'Outbound';
        $call->duration_hours = 0;
        $call->duration_minutes = 0;
        $call->deleted = 0;
        $call->save();

        return $call->id;
    }

    public function saveRecording($call_id, $recordingId) {
        // Create Note
        $note_class = $this->bean_list['Notes'];

        $GLOBALS['log']->fatal($note_class);
        $GLOBALS['log']->fatal($this->bean_files[$note_class]);

        // Create Call
        require_once($this->bean_files[$note_class]);
        $note = new $note_class();
        $note->parent_id = $call_id;
        $note->parent_type = 'Calls';
        $note->name = "Recording - " . $recordingId;
        $note->filename = "Recording - " . $recordingId;
        $note->file_mime_type = "audio/mpeg";
        $note->assigned_user_id = '1';
        $note->created_by = '1';
        $note->deleted = 0;
        $note->save();

        $path = "upload/" . $note->id;
        $GLOBALS['log']->fatal("PATH", $path);

        $recording_file = $this->getRecording($recordingId, $this->file_type);
        
        if (sugar_file_put_contents($path, $recording_file)) {
            return true;
        }

        return;
    }

    public function removeCallRecordingFromTwilio($recordingId) {
        $GLOBALS['log']->fatal("removeCallRecordingFromTwilio", $recordingId);
        $sid = $this->config['TWILIO_ACCOUNT_SID'];
        $token = $this->config['TWILIO_AUTH_TOKEN'];
        $twilio = new Client($sid, $token);
        $delete = $twilio->recordings($recordingId)->delete();
        $GLOBALS['log']->fatal("delete", $delete);
        return true;
    }
}