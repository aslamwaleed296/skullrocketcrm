<?php
$module_name = 'sr_sms';
$listViewDefs [$module_name] = 
array (
  'NAME' => 
  array (
    'width' => '32%',
    'label' => 'LBL_NAME',
    'default' => true,
    'link' => true,
  ),
  'FROM_NUMBER' => 
  array (
    'type' => 'phone',
    'label' => 'LBL_FROM',
    'width' => '10%',
    'default' => true,
  ),
  'TO_NUMBER' => 
  array (
    'type' => 'phone',
    'label' => 'LBL_TO_NUMBER',
    'width' => '10%',
    'default' => true,
  ),
  'DIRECTION' => 
  array (
    'type' => 'enum',
    'studio' => 'visible',
    'label' => 'LBL_DIRECTION',
    'width' => '10%',
    'default' => true,
  ),
  'STATUS' => 
  array (
    'type' => 'enum',
    'studio' => 'visible',
    'label' => 'LBL_STATUS',
    'width' => '10%',
    'default' => true,
  ),
  'LEAD_NAME' => 
  array (
    'type' => 'relate',
    'link' => true,
    'label' => 'LBL_LEAD',
    'id' => 'LEAD_ID',
    'width' => '10%',
    'default' => true,
  ),
  'DATE_ENTERED' => 
  array (
    'type' => 'datetime',
    'label' => 'LBL_DATE_ENTERED',
    'width' => '10%',
    'default' => true,
  ),
);
;
?>
