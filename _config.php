<?php
/**
 * This module depends on dataobject manager
 *
 */

define('SSPE_DIR', 'kelp');
define('SSPE_PATH', BASE_PATH . '/' . SSPE_DIR);



Director::addRules(50, array('SlotManager/$Action/$ID/$Name' => 'SlotManager_Controller'));
