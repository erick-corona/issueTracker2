<?php
/**
 * IssueTrackerAction abstract class.
 *
 * @category    Extensions
 * @package     IssueTracker
 * @author      Federico Cargnelutti
 * @copyright   Copyright (c) 2008 Federico Cargnelutti
 * @license     GNU General Public Licence 2.0 or later
 */
abstract class IssueTrackerAction extends ArrayObject
{
	/**
	 * Action name.
	 * @var string
	 */
	protected $_action = null;
	
	/**
	 * Instance of IssueTrackerConfig.
	 * @var IssueTrackerConfig
	 */
	protected $_config = null;
	
	/**
	 * Access control list
	 * @var array
	 */
	protected $_acl = array();
	
	/**
	 * Page output.
	 * @var array
	 */
	protected $_pageOutput = null;
	
	/**
	 * Arguments.
	 * @var array
	 */
	protected $_args = array();
	
	/**
	 * Instances of IssueTrackerModel
	 * @var IssueTrackerModel
	 */
	protected $_model = array();
	
	/**
	 * Namespace: DBKey and Text.
	 * @var array
	 */
	protected $_namespace = array();
	
	/**
	 * Parser hook or special page.
	 * @var bool
	 */
	protected $_isParserHook = false;
	
	/**
	 * Class constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct(array(), ArrayObject::ARRAY_AS_PROPS);
	}
	
	/**
	 * Returns true if the self::executeHook() method was called.
	 *
	 * @return bool
	 */
	public function isParserHook()
	{
		return $this->_isParserHook;
	}
	
	/**
	 * Sets the action name.
	 * 
	 * @param string $action
	 * @return void
	 */
	public function setAction($action)
	{
		$this->_action = strtolower($action);
	}
	
	/**
	 * Returns the action name.
	 * 
	 * @return string 
	 */
	public function getAction()
	{
		return $this->_action;
	}
	
	/**
	 * Sets the self::_isParserHook variable.
	 * 
	 * @param bool $isParserHook
	 * @return void
	 */
	public function setParserHook($isParserHook)
	{
		$this->_isParserHook = $isParserHook;
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
	 * Check whether the user's group has permission to perform this action.
	 *
	 * @param string $action
	 */
	public function hasPermission($action)
	{
		global $wgUser;
		return $wgUser->isAllowed('issuetracker-' . $action);
	}    
	
	/**
	 * Sets the arguments.
	 *
	 * @param array $args
	 * @return void
	 */
	public function setArguments($args) 
	{
		$this->_args = $args;
	}

	/**
	 * Factory method responsible of injecting model objects.
	 *
	 * @param string $namespace
	 * @param IssueTrackerModel $obj
	 */
	public function setModel(IssueTrackerModel $obj, $key = 'default')
	{
		if (! array_key_exists($key, $this->_model)) {
			$this->_model[$key] = $obj;
		}
	}

	/**
	 * Returns a model based on a given key name.
	 *
	 * @return IssueTrackerModel
	 * @throws Exception
	 */
	public function getModel($key)
	{
		if (! array_key_exists($key, $this->_model)) {
			throw new Exception('Model object undefined: ' . $key);
		}
		return $this->_model[$key];
	}
	
	/**
	 * Sets the page namespace.
	 *
	 * @param array $namespace
	 */
	public function setNamespace($namespace) 
	{
		$this->_namespace = $namespace;
	}
	
	/**
	 * Returns the page namespace.
	 *
	 * @return array self::$_namespace
	 */
	public function getNamespace($key = null) 
	{		
		if (null !== $key && array_key_exists($key, $this->_namespace)) {
			return $this->_namespace[$key];
		} else {
			return $this->_namespace;	
		}
	}
	
	/**
	 * Sets the special page response.
	 *
	 * @param string $output
	 * @return void
	 */
	public function setOutput($output)
	{
		$this->_pageOutput = $output;
	}
	
	/**
	 * Returns the special page response.
	 *
	 * @return void
	 */
	public function getOutput()
	{
		return $this->_pageOutput;
	}
	
	/**
	 * Returns true if the user is logged in.
	 *
	 * @return bool Returns true if the user is logged in, or false otherwise.
	 */
	public function isLoggedIn()
	{
		global $wgUser;
		return $wgUser->isLoggedIn();
	}
	
	/**
	 * Renders the template.
	 *
	 * @return string 
	 */
	public function render($filename = null) 
	{
		if (! isset($this->action)) {
			throw new Exception('Action undefined: $this->action');
		}
		
		if ($filename === null) {
			$filename = $this->action;
		}
		
		ob_start();
		$file = dirname(__FILE__) . '/../Views/' . $filename . '.html';
		if (file_exists($file)) {
			include dirname(__FILE__) . '/../Views/' . $filename . '.html';
		}
		$output = ob_get_clean();
		
		return $this->_processOutputEncoding($output);
	}
	
	/**
	 * Determines wether we need to hide content from the Parser or not.
	 *
	 * @return string
	 */
	protected function _processOutputEncoding($output) 
	{	
		if ($this->_isParserHook === true) {
			/* Hide content from the Parser using base64 to avoid mangling.
			   Content will be decoded after Tidy has finished its processing of the page */
	    	return '@ENCODED@'.base64_encode($output).'@ENCODED@';
		}
		return $output;
	}
}
