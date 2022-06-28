<?php

if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

require_once('modules/Leads/views/view.detail.php');

class CustomLeadsViewDetail extends LeadsViewDetail {
    function display() {
        echo '<link rel="stylesheet" href="custom/include/utilities/css/style.css" type="text/css" />';
        $smarty = new Sugar_Smarty();
        $smarty->assign('phone_mobile', $this->bean->phone_mobile);
        $smarty->assign('lead_id', $this->bean->id);
        $callModal = $smarty->fetch('custom/modules/Leads/tpls/call.tpl');
        $smsModal = $smarty->fetch('custom/modules/Leads/tpls/sms.tpl');
        echo $callModal;
        echo $smsModal;
        parent::display();
	}
}

?>