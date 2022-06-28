<?php

$layout_defs["Leads"]["subpanel_setup"]['lead_sms'] = array (
  'order' => 40,
  'module' => 'sr_sms',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_SR_SMS_SUBPANEL_TITLE',
  'get_subpanel_data' => 'lead_sms',
  'top_buttons' => 
  array (
    0 => 
    array (
      'widget_class' => 'SubPanelTopButtonQuickCreate',
    ),
    1 => 
    array (
      'widget_class' => 'SubPanelTopSelectButton',
      'mode' => 'MultiSelect',
    ),
  ),
);
