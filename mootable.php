<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.Mootable
 *
 * @author      Roberto Segura <roberto@phproberto.com>
 * @copyright   (c) 2012 Roberto Segura. All Rights Reserved.
 * @license     GNU/GPL 2, http://www.gnu.org/licenses/gpl-2.0.htm
 */
defined('_JEXEC') or die;

JLoader::import('joomla.plugin.plugin');

/**
 * Main plugin class
 *
 * @package     Joomla.Plugin
 * @subpackage  System.Mootable
 * @since       2.5
 *
 */
class PlgSystemMootable extends JPlugin
{
	// Plugin info constants
	const TYPE = 'system';
	const NAME = 'mootable';

	/**
	 * Plugin details from DB
	 *
	 * @var  object
	 */
	private $plugin;

	/**
	 * Plugin parameters
	 *
	 * @var  JRegistry
	 */
	public $params;

	/**
	 * Path to the plugin folder
	 *
	 * @var  string
	 */
	private $pathPlugin = null;

	/**
	 * Base Url to the plugin folder
	 *
	 * @var  string
	 */
	private $urlPlugin;

	/**
	 * Components where the plugin is allowed to be loaded
	 *
	 * @var  array
	 */
	private $componentsEnabled = array('*');

	/**
	 * Views where the plugin is allowed to be loaded
	 *
	 * @var  array
	 */
	private $viewsEnabled 		= array('*');

	/**
	 * Is this plugin enabled in frontend?
	 *
	 * @var  boolean
	 */
	private $frontendEnabled 	= true;

	/**
	 * Is this plugin enabled on backend?
	 *
	 * @var  boolean
	 */
	private $backendEnabled 	= false;

	/**
	 * Constructor
	 *
	 * @param   mixed  &$subject  Subject
	 */
	function __construct( &$subject )
	{
		parent::__construct($subject);

		// Load plugin parameters
		$this->plugin = JPluginHelper::getPlugin(self::TYPE, self::NAME);
		$this->params = new JRegistry($this->plugin->params);

		// Init folder structure
		$this->initFolders();

		// Load plugin language
		$this->loadLanguage('plg_' . self::TYPE . '_' . self::NAME, JPATH_ADMINISTRATOR);
	}

	/**
	 * This event is triggered before the framework creates the Head section of the Document.
	 *
	 * @return boolean
	 */
	function onBeforeCompileHead()
	{
		// Validate view
		if (!$this->validateUrl())
		{
			return true;
		}

		// Required objects
		$app        = JFactory::getApplication();
		$doc        = JFactory::getDocument();
		$pageParams = $app->getParams();

		// Check if we have to disable Mootools for this item
		$mootoolsMode = $pageParams->get('mootable', $this->params->get('defaultMode', 0));
		$moreMode     = $pageParams->get('moreMode', $this->params->get('defaultMoreMode', 0));

		$disableOnDebug = $this->params->get('disableWhenDebug', 1);

		if (!$this->isAutoEnabled() && 0 == $mootoolsMode)
		{
			// Function used to replace window.addEvent()
			$doc->addScriptDeclaration("function do_nothing() { return; }");

			// Disable mootools javascript
			$this->disableScript('/media/system/js/mootools-core.js');

			// From v3.3 core does not require mootools anymore
			if (version_compare(JVERSION, '3.3', '<'))
			{
				$this->disableScript('/media/system/js/core.js');
			}

			$this->disableScript('/media/system/js/mootools-more.js');
			$this->disableScript('/media/system/js/caption.js');
			$this->disableScript('/media/system/js/modal.js');
			$this->disableScript('/media/system/js/mootools.js');
			$this->disableScript('/plugins/system/mtupgrade/mootools.js');

			// Disabled mootools javascript when debugging site
			if ($disableOnDebug)
			{
				$this->disableScript('/media/system/js/mootools-core-uncompressed.js');
				$this->disableScript('/media/system/js/mootools-core-uncompressed.js');
				$this->disableScript('/media/system/js/mootools-more-uncompressed.js');
				$this->disableScript('/media/system/js/core-uncompressed.js');
				$this->disableScript('/media/system/js/caption-uncompressed.js');
			}

			// Disable css stylesheets
			$this->disableStylesheet('/media/system/css/modal.css');
		}
		elseif (0 == $moreMode)
		{
			$this->disableScript('/media/system/js/mootools-more.js');

			if ($disableOnDebug)
			{
				$this->disableScript('/media/system/js/mootools-more-uncompressed.js');
			}
		}

		// Disable additional assets specified by the user
		$this->disablePageScripts();
		$this->disablePageStylesheets();

		return true;
	}

	/**
	 * This event is triggered after pushing the document buffers into the template placeholders,
	 * retrieving data from the document and pushing it into the into the JResponse buffer.
	 * http://docs.joomla.org/Plugin/Events/System
	 *
	 * @return boolean
	 */
	function onAfterRender()
	{
		// Validate view
		if (!$this->validateUrl())
		{
			return true;
		}

		// Required objects
		$app        = JFactory::getApplication();
		$doc        = JFactory::getDocument();
		$pageParams = $app->getParams();

		// Check if we have to disable Mootools for this item
		$mode = $pageParams->get('mootable', $this->params->get('defaultMode', 0));

		if (!$this->isAutoEnabled() && !$mode)
		{
			// Get the generated content
			$body = JResponse::getBody();

			// Remove JCaption JS calls
			$pattern     = "/(new JCaption\()(.*)(\);)/isU";
			$replacement = '';
			$body        = preg_replace($pattern, $replacement, $body);

			// Null window.addEvent( calls
			$pattern = "/(window.addEvent\()(.*)(,)/isU";
			$body    = preg_replace($pattern, 'do_nothing(', $body);
			JResponse::setBody($body);
		}

		return true;
	}

	/**
	 * Change forms before they are shown to the user
	 *
	 * @param   JForm  $form  JForm object
	 * @param   array  $data  Data array
	 *
	 * @return boolean
	 */
	public function onContentPrepareForm($form, $data)
	{
		// Check we have a form
		if (!($form instanceof JForm))
		{
			$this->_subject->setError('JERROR_NOT_A_FORM');

			return false;
		}

		// Extra parameters for menu edit
		if ($form->getName() == 'com_menus.item')
		{
			$form->loadFile($this->pathPlugin . '/forms/menuitem.xml');
		}

		return true;
	}

	/**
	 * Remove a javascript file call from header
	 *
	 * @param   string  $script  URL to script (both global/relative should work)
	 *
	 * @return  void
	 */
	private function disableScript($script)
	{
		$script = trim($script);

		if (!empty($script))
		{
			$doc = JFactory::getDocument();
			$uri = JUri::getInstance();

			$relativePath   = trim(str_replace($uri->getPath(), '', JUri::root()), '/');
			$relativeScript = trim(str_replace($uri->getPath(), '', $script), '/');
			$relativeUrl    = str_replace($relativePath, '', $script);

			// Try to disable relative and full URLs
			unset($doc->_scripts[$script]);
			unset($doc->_scripts[$relativeUrl]);
			unset($doc->_scripts[JUri::root(true) . $script]);
			unset($doc->_scripts[$relativeScript]);
		}
	}

	/**
	 * Remove a stylesheet file call from header
	 *
	 * @param   string  $stylesheet  URL to the stylesheet (both global/relative should work)
	 *
	 * @return  void
	 */
	private function disableStylesheet($stylesheet)
	{
		$stylesheet = trim($stylesheet);

		if (!empty($stylesheet))
		{
			$doc = JFactory::getDocument();
			$uri = JUri::getInstance();

			$relativePath   = trim(str_replace($uri->getPath(), '', JUri::root()), '/');
			$relativeStylesheet = trim(str_replace($uri->getPath(), '', $stylesheet), '/');
			$relativeUrl    = str_replace($relativePath, '', $stylesheet);

			// Try to disable relative and full URLs
			unset($doc->_styleSheets[$stylesheet]);
			unset($doc->_styleSheets[$relativeUrl]);
			unset($doc->_styleSheets[JUri::root(true) . $stylesheet]);
			unset($doc->_styleSheets[$relativeStylesheet]);
		}
	}

	/**
	 * Disable the page scripts
	 *
	 * @return  void
	 */
	private function disablePageScripts()
	{
		$pageParams = JFactory::getApplication()->getParams();

		// Other scripts disabled
		$globalDisabled = str_replace("\n", ",", $this->params->get('manualDisable', null));
		$menuDisabled   = str_replace("\n", ",", $pageParams->get('disabledScripts', null));

		$scriptsDisabled = array_unique(array_merge(explode(',', $globalDisabled), explode(',', $menuDisabled)));

		// Disable 3rd party extensions added by the user
		if (!empty($scriptsDisabled))
		{
			foreach ($scriptsDisabled as $script)
			{
				$this->disableScript($script);
			}
		}
	}

	/**
	 * Disable the page stylesheets
	 *
	 * @return  void
	 */
	private function disablePageStylesheets()
	{
		$pageParams = JFactory::getApplication()->getParams();

		// Other scripts disabled
		$globalDisabled = str_replace("\n", ",", $this->params->get('disabledStylesheets', null));
		$menuDisabled   = str_replace("\n", ",", $pageParams->get('disabledStylesheets', null));

		$stylesheetsDisabled = array_unique(array_merge(explode(',', $globalDisabled), explode(',', $menuDisabled)));

		if (!empty($stylesheetsDisabled))
		{
			foreach ($stylesheetsDisabled as $stylesheet)
			{
				$this->disableStylesheet($stylesheet);
			}
		}
	}

	/**
	 * initialize folder structure
	 *
	 * @return none
	 */
	private function initFolders()
	{
		// Path
		$this->pathPlugin = JPATH_PLUGINS . '/' . self::TYPE . '/' . self::NAME;

		// Url
		$this->urlPlugin = JURI::root(true) . "/plugins/" . self::TYPE . "/" . self::NAME;
	}

	/**
	 * Detect if the user is editing an article
	 *
	 * @return boolean
	 */
	private function isAutoEnabled()
	{
		$app    = JFactory::getApplication();
		$jinput = $app->input;
		$option = $jinput->get('option', null);
		$view 	= $jinput->get('view', null);
		$id     = $jinput->get('id', null);
		$layout = $jinput->get('layout', null);

		// Always enable mootools for given components
		if ($alwaysEnable = $this->params->get('alwaysEnable', null))
		{
			// Allow ENTER separated and remove spaces
			$components = str_replace(array("\n", " "), array(",", ""), $alwaysEnable);
			$components = explode(',', $components);

			if (in_array($option, $components))
			{
				return true;
			}
		}

		// Allways enable for content edition
		$isContentEdit = $this->params->get('contentEdition', 1);

		if ($app->isSite() &&  $isContentEdit && $option == 'com_content' && $view == 'form' && $layout == 'edit')
		{
			return true;
		}

		// Allways enable for frontend com_users (login, profile edit, etc.)
		$enableComUsers = $this->params->get('enableComUsers', 1);

		if ($app->isSite() && $enableComUsers && $option == 'com_users')
		{
			return true;
		}

		return false;
	}

	/**
	 * validate if the plugin is enabled for current application (frontend / backend)
	 *
	 * @return boolean
	 */
	private function validateApplication()
	{
		$app = JFactory::getApplication();

		if ( ($app->isSite() && $this->frontendEnabled) || ($app->isAdmin() && $this->backendEnabled) )
		{
			return true;
		}

		return false;
	}

	/**
	 * Validate option in url
	 *
	 * @return boolean
	 */
	private function validateComponent()
	{
		$option = JFactory::getApplication()->input->get('option');

		if ( in_array('*', $this->componentsEnabled) || in_array($option, $this->componentsEnabled) )
		{
			return true;
		}

		return false;
	}

	/**
	 * Custom method for extra validations
	 *
	 * @return true
	 */
	private function validateExtra()
	{
		return $this->validateApplication();
	}

	/**
	 * Is the plugin enabled for this url?
	 *
	 * @return boolean
	 */
	private function validateUrl()
	{
		if ( $this->validateComponent() && $this->validateView())
		{
			if (method_exists($this, 'validateExtra'))
			{
				return $this->validateExtra();
			}
			else
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * validate view parameter in url
	 *
	 * @return boolean
	 */
	private function validateView()
	{
		$view = JFactory::getApplication()->input->get('view');

		if ( in_array('*', $this->viewsEnabled) || in_array($view, $this->viewsEnabled))
		{
			return true;
		}

		return false;
	}
}
