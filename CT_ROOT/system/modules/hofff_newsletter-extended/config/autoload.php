<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'Hofff',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Modules
	'Hofff\Contao\NewsletterExtended\ModuleSubscribeExtended' => 'system/modules/hofff_newsletter-extended/modules/ModuleSubscribeExtended.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'nl_ext_default' => 'system/modules/hofff_newsletter-extended/templates/newsletter',
));
