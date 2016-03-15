<?php

$GLOBALS['TL_DCA']['tl_module']['palettes']['hofff_subscribe-extended'] = $GLOBALS['TL_DCA']['tl_module']['palettes']['subscribe'];

/**
 * Table tl_module
 */
$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = array('tl_module_hofff_subscribe_extended', 'modifyPaletteAndFields');

/**
 * Class tl_module_hofff_subscribe_extended
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Hofff.com 2015-2016
 * @author     Cliff Parnitzky <cliff@hofff.com>
 * @package    Core
 */
class tl_module_hofff_subscribe_extended extends tl_module
{
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Modify the pallete and fields for this module
	 */
	public function modifyPaletteAndFields($dc)
	{
		$objModule = \ModuleModel::findById($dc->id);
		if ($objModule != null && $objModule->type == 'hofff_subscribe-extended')
		{
			$GLOBALS['TL_DCA']['tl_module']['fields']['nl_template']['options_callback'] = array('tl_module_hofff_subscribe_extended', 'getNewsletterTemplates');
		}
	}
		
	/**
	 * Return all newsletter templates as array
	 * @return array
	 */
	public function getNewsletterTemplates()
	{
		return $this->getTemplateGroup('nl_ext_');
	}
} 