<?php

$dictionary['Lead']['fields']['lead_sms'] = array(
    'name' => 'lead_sms',
    'type' => 'link',
    'vname' => 'LBL_SMS',
    'relationship' => 'lead_sms',
    'module' => 'sr_sms',
    'bean' => 'sr_sms',
    'source' => 'non-db',
);

$dictionary['Lead']['relationships']['lead_sms'] = array(
    'lhs_module' => 'Leads',
    'lhs_table' => 'leads',
    'lhs_key' => 'id',
    'rhs_module' => 'sr_sms',
    'rhs_table' => 'sr_sms',
    'rhs_key' => 'lead_id',
    'relationship_type' => 'one-to-many'
);