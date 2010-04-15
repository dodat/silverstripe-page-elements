<?php

/**
 * turn on versioning by adding
 * Element::set_versioning("Element");
 * to your mysite/_config.php
 */


define('SSPE_DIR', 'page-elements');
define('SSPE_PATH', BASE_PATH . DIRECTORY_SEPARATOR . SSPE_DIR);

Director::addRules(50, array('SlotManager/$Action/$ID/$Name' => 'SlotManager_Controller'));

Object::add_extension("LeftAndMain", "ElementExtension");
