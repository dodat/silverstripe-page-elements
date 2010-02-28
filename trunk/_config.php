<?php

/**
 * turn on versioning by adding
 * Element::setVersioning("Element");
 * to your mysite/_config.php
 * You can also set enable it for individual elements
 * by passing an array of classnames:
 * Element::setVersioning(array("HTMLElement", "RawTextElement"));
 */


define('SSPE_DIR', 'page-elements');
define('SSPE_PATH', BASE_PATH . DIRECTORY_SEPARATOR . SSPE_DIR);

Director::addRules(50, array('SlotManager/$Action/$ID/$Name' => 'SlotManager_Controller'));

