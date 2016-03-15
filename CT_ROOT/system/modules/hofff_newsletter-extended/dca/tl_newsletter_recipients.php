<?php

// List
$GLOBALS['TL_DCA']['tl_newsletter_recipients']['list']['sorting']['child_record_callback'] = array('tl_newsletter_recipients_hofff_newsletter_extended', 'listRecipient');

// Palettes 
$GLOBALS['TL_DCA']['tl_newsletter_recipients']['palettes']['default'] = str_replace('{email_legend},email', '{email_legend},email,salutation,firstname,lastname', $GLOBALS['TL_DCA']['tl_newsletter_recipients']['palettes']['default']);

// Fields 
$GLOBALS['TL_DCA']['tl_newsletter_recipients']['fields']['salutation'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_newsletter_recipients']['salutation'],
	'exclude'                 => true,
	'search'                  => false,
	'sorting'                 => false,
	'inputType'               => 'select',
	'options'                 => array('female', 'male'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_newsletter_recipients']['salutation_options'],
	'eval'                    => array('mandatory'=>false, 'tl_class'=>'clr w50', 'includeBlankOption'=>true),
	'sql'                     => "varchar(10) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_newsletter_recipients']['fields']['firstname'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_newsletter_recipients']['firstname'],
	'exclude'                 => true,
	'search'                  => true,
	'sorting'                 => false,
	'flag'                    => 1,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'clr w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_newsletter_recipients']['fields']['lastname'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_newsletter_recipients']['lastname'],
	'exclude'                 => true,
	'search'                  => true,
	'sorting'                 => false,
	'flag'                    => 1,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>false, 'maxlength'=>255, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_newsletter_recipients']['fields']['active']['eval']['tl_class'] .= ' clr';

/**
 * Class tl_newsletter_recipients_hofff_newsletter_extended
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @copyright  Hofff.com 2015-2016
 * @author     Cliff Parnitzky <cliff@hofff.com>
 * @package    Core
 */
class tl_newsletter_recipients_hofff_newsletter_extended extends tl_newsletter_recipients
{
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
	}
	
		/**
	 * List a recipient
	 * @param array
	 * @return string
	 */
	public function listRecipient($row)
	{
		$label = parent::listRecipient($row);
		
		$labelAddition = '';
		
		if ($row['salutation'] || $row['firstname'] || $row['lastname'])
		{
			$arrParts = array();
			if ($row['salutation'])
			{
				$arrParts[] = &$GLOBALS['TL_LANG']['tl_newsletter_recipients']['salutation_options'][$row['salutation']];
			}
			if ($row['firstname'])
			{
				$arrParts[] = $row['firstname'];
			}
			if ($row['lastname'])
			{
				$arrParts[] = $row['lastname'];
			}
			$labelAddition = '<span style="color:#8b8b8b;padding-left:3px">[' . implode(' ', $arrParts) . ']</span>';
		}
		
		$label = str_replace($row['email'], $row['email'] . $labelAddition, $label);

		return $label;
	}
}