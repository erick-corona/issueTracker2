<?php
/** @see IssueTrackerModelDefault **/
require_once dirname(__FILE__) . '/Models/IssueTrackerModelDefault.php';

/**
 * Issue Tracking System
 * 
 * This class will be loaded automatically when the special page or hook 
 * is requested.
 *
 * @category    Extensions
 * @package     IssueTracker
 * @author      Federico Cargnelutti
 * @copyright   Copyright (c) 2008 Federico Cargnelutti
 * @license     GNU General Public Licence 2.0 or later
 */
class SpecialIssueTracker extends SpecialPage
{
	/**
	 * Instance of the class.
	 * @var obj
	 */
	protected static $_instance = null;
	
	/**
	 * Instance of IssueTrackerConfig
	 * @var IssueTrackerConfig
	 */
	protected $_config = null;
	
	/**
	 * Class constructor
	 * 
	 * @return void
	 */
	public function __construct() 
	{
		parent::__construct('IssueTracker');
		$this->_loadConfigFile();
	}
	
	/**
	 * Special Page
	 * 
	 * This methods overrides SpecialPage::execute(), it passes a single 
	 * parameter, usually referred to cryptically as $par.
	 *
	 * @return void
	 */
	public function execute($par) 
	{
		global $wgOut;
		$request = $this->getRequest();
		$output = $this->getOutput();
		$this->setHeaders();

		# Get request data from, e.g.
		$param = $request->getText( 'param' );

		# Do stuff
		# ...
		$wikitext = 'Hello world!';
		$output->addWikiText( $wikitext );
		// Set the page namespace
//		$title = Title::makeTitle(NS_SPECIAL, $this->getName());
//		$namespace['dbKey'] = $title->getPrefixedDbKey();
//		$namespace['text'] = $title->getPrefixedDbKey();
		
		// Process request
//		$output = $this->_processActionRequest($namespace);
		
		// Output
//		$this->setHeaders();
//		$wgOut->addHtml($output);
	}

	/**
	 * Parser Hook
	 * 
	 * The following method is assigned to a hook, which will be run whenever
	 * the user adds a <bugs /> tag in the main MediaWiki code.
	 *
	 * @param string $text
	 * @param array $args
	 * @param obj $parser
	 * @return str
	 */
	public static function executeHook($text, $args = array(), $parser)
	{		
		$parser->disableCache();
		
		// Set the page namespace
		$namespace['dbKey'] = $parser->getTitle()->getPrefixedDBkey();
		$namespace['text'] = $parser->getTitle()->getPrefixedText();
		
		$isParserHook = true;
		
		// Process request
		$instance = self::_getInstance();
		$output = $instance->_processActionRequest($namespace, $isParserHook, $args);
		
		return $output;
	}

	/**
	 * Returns a single instance of the class.
	 *
	 * @return obj
	 */
	protected static function _getInstance() 
	{
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Loads the config file.
	 *
	 * @return void
	 * @throws Exception
	 */
	protected function _loadConfigFile() 
	{
		$file = dirname(__FILE__) . '/IssueTracker.config.php';
		if (! file_exists($file)) {
			throw new Exception('Unable to load the configuration file: ' . $file);
		}

		require_once $file;
		$class = 'IssueTrackerConfig';
		$this->setConfig(new $class());
	}
	
	/**
	 * Processes the action request.
	 * 
	 * @return string
	 * @throws Exception
	 */
	protected function _processActionRequest(array $namespace, $isParserHook = false, $args = array())
	{
		global $wgRequest;
		
		$action = $wgRequest->getText('bt_action', 'list');
		$class = __CLASS__ . 'Action' . ucfirst(strtolower($action));
		$method = $action . 'Action';
		$file = dirname(__FILE__) . '/Actions/' . $class . '.php';
		
		if (! file_exists($file)) {
			throw new Exception('Invalid file: ' . $file);
		}
		
		if (array_key_exists($action, array('add'=>'add', 
											'archive'=>'archive', 
											'edit'=>'edit',
											'list'=>'list',
											'view'=>'view')
										))
		{
				require_once dirname(__FILE__) . '/Actions/' . $class . '.php';
				$controller = new $class();
				$controller->setConfig($this->getConfig());
				if ($controller->hasPermission($action)) {
					$controller->setAction($action);
					$controller->setParserHook($isParserHook);
					$controller->setNamespace($namespace);
					$controller->setModel(new IssueTrackerModelDefault());
					$controller->setArguments($args);
					if (!method_exists($controller, 'init') || $controller->init() === true) {
						$controller->$method();
						return $controller->getOutput();
					}
				}
				return wfMessage('not_authorized');
		}
		return wfMessage('invalid_action') . ': ' . $action;
	}

	/**
	 * Sets the Config object.
	 *
	 * @param IssueTrackerConfig
	 * @return void
	 */
	public function setConfig(IssueTrackerConfig $config)
	{
		$this->_config = $config;
	}

	/**
	 * Returns the config object.
	 *
	 * @return array self::$_config
	 */
	public function getConfig()
	{
		return $this->_config;
	}
}
