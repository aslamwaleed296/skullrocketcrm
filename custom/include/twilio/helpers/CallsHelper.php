<?php

require_once 'vendor/autoload.php';
use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VoiceGrant;

class CallsHelper {
    public $config;
    
    public function __construct() {
        $GLOBALS['log']->fatal("CONSTRUCTOR CALLED");
        global $sugar_config;
        $this->config = $sugar_config;
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
}