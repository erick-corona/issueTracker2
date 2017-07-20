<?php
/**
 * Issue Tracking System
 * 
 * Setup and Hooks for the BugTracking extension
 *
 * @category    Extensions
 * @package     IssueTracker
 * @author      Federico Cargnelutti
 * @copyright   Copyright (c) 2008 Federico Cargnelutti
 * @license     GNU General Public Licence 2.0 or later
 */

if (! defined('MEDIAWIKI')) {
	echo 'This file is an extension to the MediaWiki software and cannot be used standalone.';
	die;
}
require_once dirname(__FILE__) . '/Models/IssueTrackerModelDefault.php';

$wgIssueTrackerExtensionVersion = '1.0';

$wgExtensionCredits['parserhook'][]  = array(
	'name'          => 'IssueTracker',
	'author'        => 'Federico Cargnelutti',
	'email'         => 'federico@kewnode.com',
	'description'   => 'Issue Tracking System',
	'description'   => 'Adds &lt;issues /&gt; parser function for viewing and adding issues',
	'version'       => $wgIssueTrackerExtensionVersion
);
$wgExtensionCredits['specialpage'][] = array(
	'name'          => 'IssueTracker',
	'author'        => 'Federico Cargnelutti',
	'email'         => 'federico@kewnode.com',
	'description'   => 'Issue Tracking System',
	'description'   => 'Adds a special page for managing issues',
	'version'       => $wgIssueTrackerExtensionVersion
);

$dir = dirname(__FILE__) . '/';

// Tell MediaWiki to load the extension body.
//$wgExtensionMessagesFiles['IssueTrackerMagic'] = __DIR__ . '/IssueTracker.i18n.magic.php';
//$wgExtensionMessagesFiles['IssueTracker'] = $dir . 'IssueTracker.i18n.magic.php';

// Autoload the IssueTracker class
//$wgAutoloadClasses['IssueTracker'] = $dir . 'IssueTracker.body.php';

// Let MediaWiki know about your new special page.
//$wgSpecialPages['IssueTracker'] = 'IssueTracker';
//$wgSpecialPageGroups['IssueTracker']="developer";

// Add Extension Functions
//$wgExtensionFunctions[] = 'wfIssueTrackerSetParserHook';

// Add any aliases for the special page
//$wgHooks['LanguageGetSpecialPageAliases'][] = 'wfIssueTrackerLocalizedTitle';
//$wgHooks['ParserAfterTidy'][] = 'wfIssueTrackerDecodeOutput';

/**
 * Converts an array of values in form [0] => "name=value" into a real
 * associative array in form [name] => value. If no = is provided,
 * true is assumed like this: [name] => true
 *
 * @param array string $options
 * @return array $results
 */
function extractOptions( array $options ) {
	$results = array();

	foreach ( $options as $option ) {
		$pair = explode( '=', $option, 2 );
		if ( count( $pair ) === 2 ) {
			$name = trim( $pair[0] );
			$value = trim( $pair[1] );
			$results[$name] = $value;
		}

		if ( count( $pair ) === 1 ) {
			$name = trim( $pair[0] );
			$results[$name] = true;
		}
	}
	//Now you've got an array that looks like this:
	//  [foo] => "bar"
	//	[apple] => "orange"
	//	[banana] => true
	return $results;
}

/**
 * replace text
 *
 */
function replace_callback( array $matches) {
	return base64_decode($matches[1]);
}

class IssueTracker {
	/**
	 * A hook to register an alias for the special page
	 * @return bool
	 */
	function wfIssueTrackerLocalizedTitle(&$specialPageArray, $code = 'en')
	{
		// The localized title of the special page is among the messages of the extension:
		wfLoadExtensionMessages('IssueTracker');

		// Convert from title in text form to DBKey and put it into the alias array:
		$text = wfMessage('issuetracker');
		$title = Title::newFromText($text);
		$specialPageArray['IssueTracker'][] = $title->getDBKey();

		return true;
	}

	/**
	 * Register parser hook
	 * @return void
	 */
	public static function wfIssueTrackerSetParserHook( &$parser )
	{
		$parser->setFunctionHook('issue', 'IssueTracker::executeHook');
	}

	/**
	 * Processes HTML comments with encoded content.
	 *
	 * @param OutputPage $out Handle to an OutputPage object presumably $wgOut (passed by reference).
	 * @param String $text Output text (passed by reference)
	 * @return Boolean Always true to give other hooking methods a chance to run.
	 */
	public static function wfIssueTrackerDecodeOutput(&$parser, &$text)
	{
		$text = preg_replace_callback (
			'/@ENCODED@([0-9a-zA-Z\\+\\/]+=*)@ENCODED@/',
			"replace_callback",
			$text
		);
		return true;
	}
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
//		parent::__construct('IssueTracker');
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
	 * @param1 string $text
	 * @param2 array $args
	 * @param3 obj $parser
	 * @return str
	 */
	public static function executeHook(&$parser )
	{
		$options = extractOptions( array_slice(func_get_args(), 1) );

		$parser->disableCache();

		// Set the page namespace
		$namespace['dbKey'] = $parser->getTitle()->getPrefixedDBkey();
		$namespace['text'] = $parser->getTitle()->getPrefixedText();

		$isParserHook = true;

		// Process request
		$instance = self::_getInstance();
		$output = $instance->_processActionRequest($namespace, $isParserHook, $options);

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

