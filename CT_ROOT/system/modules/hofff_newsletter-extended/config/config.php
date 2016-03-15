<?php

/**
 * Front end modules
 */
array_insert($GLOBALS['FE_MOD']['newsletter'], array_search('subscribe', array_keys($GLOBALS['FE_MOD']['newsletter'])) + 1, array
(
	'hofff_subscribe-extended' => 'Hofff\\Contao\\NewsletterExtended\\ModuleSubscribeExtended'
));
