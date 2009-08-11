<?php
/**
 * This module depends on dataobject manager
 *
 */

define('KELP_DIR', 'kelp');
define('KELP_PATH', BASE_PATH . '/' . KELP_DIR);



Director::addRules(50, array('SlotManager/$Action/$ID/$Name' => 'SlotManager_Controller'));
