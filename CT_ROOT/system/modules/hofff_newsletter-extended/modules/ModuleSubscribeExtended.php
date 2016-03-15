<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */

namespace Hofff\Contao\NewsletterExtended;


/**
 * Front end module "newsletter subscribe extended".
 *
 * @copyright  Hofff.com 2015-2016
 * @author     Cliff Parnitzky <cliff@hofff.com>
 */
class ModuleSubscribeExtended extends \Contao\ModuleSubscribe
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'nl_ext_default';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['hofff_subscribe-extended'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		parent::compile();

		// Default template variables
		\System::loadLanguageFile('tl_newsletter_recipients');
		$this->Template->salutation = ''; 
		$this->Template->firstname = ''; 
		$this->Template->lastname = ''; 
		$this->Template->salutationLabel = $GLOBALS['TL_LANG']['tl_newsletter_recipients']['salutation'][0];
		$this->Template->salutationFemaleOption = $GLOBALS['TL_LANG']['tl_newsletter_recipients']['salutation_options']['female'];
		$this->Template->salutationMaleOption = $GLOBALS['TL_LANG']['tl_newsletter_recipients']['salutation_options']['male'];
		$this->Template->firstnameLabel = $GLOBALS['TL_LANG']['tl_newsletter_recipients']['firstname'][0];
		$this->Template->lastnameLabel = $GLOBALS['TL_LANG']['tl_newsletter_recipients']['lastname'][0];
	}


	/**
	 * Add a new recipient
	 */
	protected function addRecipient()
	{
		$arrChannels = \Input::post('channels');

		if (!is_array($arrChannels))
		{
			$_SESSION['UNSUBSCRIBE_ERROR'] = $GLOBALS['TL_LANG']['ERR']['noChannels'];
			$this->reload();
		}

		$arrChannels = array_intersect($arrChannels, $this->nl_channels); // see #3240

		// Check the selection
		if (!is_array($arrChannels) || empty($arrChannels))
		{
			$_SESSION['SUBSCRIBE_ERROR'] = $GLOBALS['TL_LANG']['ERR']['noChannels'];
			$this->reload();
		}

		$varInput = \Idna::encodeEmail(\Input::post('email', true));

		// Validate the e-mail address
		if (!\Validator::isEmail($varInput))
		{
			$_SESSION['SUBSCRIBE_ERROR'] = $GLOBALS['TL_LANG']['ERR']['email'];
			$this->reload();
		}

		$arrSubscriptions = array();

		// Get the existing active subscriptions
		if (($objSubscription = \NewsletterRecipientsModel::findBy(array("email=? AND active=1"), $varInput)) !== null)
		{
			$arrSubscriptions = $objSubscription->fetchEach('pid');
		}

		$arrNew = array_diff($arrChannels, $arrSubscriptions);

		// Return if there are no new subscriptions
		if (!is_array($arrNew) || empty($arrNew))
		{
			$_SESSION['SUBSCRIBE_ERROR'] = $GLOBALS['TL_LANG']['ERR']['subscribed'];
			$this->reload();
		}

		// Remove old subscriptions that have not been activated yet
		if (($objOld = \NewsletterRecipientsModel::findBy(array("email=? AND active=''"), $varInput)) !== null)
		{
			while ($objOld->next())
			{
				$objOld->delete();
			}
		}

		$time = time();
		$strToken = md5(uniqid(mt_rand(), true));

		// Add the new subscriptions
		foreach ($arrNew as $id)
		{
			$objRecipient = new \NewsletterRecipientsModel();

			$objRecipient->pid = $id;
			$objRecipient->tstamp = $time;
			$objRecipient->email = $varInput;
			$objRecipient->salutation = \Input::post('salutation', true);
			$objRecipient->firstname = \Input::post('firstname', true);
			$objRecipient->lastname = \Input::post('lastname', true);
			$objRecipient->active = '';
			$objRecipient->addedOn = $time;
			$objRecipient->ip = $this->anonymizeIp(\Environment::get('ip'));
			$objRecipient->token = $strToken;
			$objRecipient->confirmed = '';

			$objRecipient->save();
		}

		// Get the channels
		$objChannel = \NewsletterChannelModel::findByIds($arrChannels);

		// Prepare the e-mail text
		$strText = str_replace('##token##', $strToken, $this->nl_subscribe);
		$strText = str_replace('##domain##', \Idna::decode(\Environment::get('host')), $strText);
		$strText = str_replace('##link##', \Idna::decode(\Environment::get('base')) . \Environment::get('request') . (($GLOBALS['TL_CONFIG']['disableAlias'] || strpos(\Environment::get('request'), '?') !== false) ? '&' : '?') . 'token=' . $strToken, $strText);
		$strText = str_replace(array('##channel##', '##channels##'), implode("\n", $objChannel->fetchEach('title')), $strText);

		// Activation e-mail
		$objEmail = new \Email();
		$objEmail->from = $GLOBALS['TL_ADMIN_EMAIL'];
		$objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
		$objEmail->subject = sprintf($GLOBALS['TL_LANG']['MSC']['nl_subject'], \Idna::decode(\Environment::get('host')));
		$objEmail->text = $strText;
		$objEmail->sendTo($varInput);

		// Redirect to the jumpTo page
		if ($this->jumpTo && ($objTarget = $this->objModel->getRelated('jumpTo')) !== null)
		{
			$this->redirect($this->generateFrontendUrl($objTarget->row()));
		}

		$_SESSION['SUBSCRIBE_CONFIRM'] = $GLOBALS['TL_LANG']['MSC']['nl_confirm'];
		$this->reload();
	}
}
