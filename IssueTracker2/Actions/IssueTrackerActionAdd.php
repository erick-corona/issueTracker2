<?php
/** @see IssueTrackerAction **/
require_once dirname(__FILE__) . '/IssueTrackerAction.php';

/**
 * IssueTrackerActionAdd class.
 *
 * @category    Extensions
 * @package     IssueTracker
 * @author      Federico Cargnelutti
 * @copyright   Copyright (c) 2008 Federico Cargnelutti
 * @license     GNU General Public Licence 2.0 or later
 */
class IssueTrackerActionAdd extends IssueTrackerAction
{
	/**
	 * Required form fields.
	 * @var array
	 */
	protected $_requiredFields = array('bt_title');
	
	/**
	 * Initialize class.
	 * 
	 * @return bool
	 */
	public function init()
	{
		return $this->isLoggedIn();
	}
	
	/**
	 * Executes the add action.
	 *
	 * @return void 
	 */
	public function addAction()
	{
		global $wgScript, $wgUser;
		
		$this->_setDefaultVars();
		$this->_setHookPreferences();
		
		if (isset($_POST['bt_submit'])) {
			$errorMessages = $this->_getErrors($this->_requiredFields);	
			if (count($errorMessages) == 0) {
				$userId = $wgUser->getID();
				$userName = $wgUser->getName();
				$this->getModel('default')->addIssue($_POST, $userId, $userName);
				header('Location: ' . $this->listUrl);
			} else {
				$this->errors = implode('<br />', $errorMessages);
			}
		}
		
		$this->usersList = $this->_getUsers();
		$this->setOutput($this->render());
	}
	
	/**
	 * Sets the default vars.
	 *
	 * @return void 
	 */
	protected function _setDefaultVars()
	{
		global $wgScript, $wgRequest;
		
		$this->action = 'add';
		$this->pageKey = $this->getNamespace('dbKey');
		$this->project = $this->getNamespace('dbKey');
		$this->listUrl = $wgScript . '?title=' . $this->pageKey . '&bt_action=list';
		$this->typeArray = $this->_config->getIssueType();
		$this->statusArray = $this->_config->getIssueStatus();
		$this->formAction = $wgScript;
	}

	/**
	 * Processes the tag attributes.
	 *
	 * @return void 
	 */
	protected function _setHookPreferences()
	{
		if (array_key_exists('project', $this->_args) && $this->_args['project'] !== '') {
			$this->project = $this->_args['project'];
		}
	}
	
	/**
	 * Data validation.
	 *
	 * @param array $requiredFields
	 * @return array Returns an array with error messages. 
	 */
	protected function _getErrors($requiredFields)
	{
		$errors = array();
		foreach ($requiredFields as $field) {
			if (! isset($_POST[$field]) || '' == $_POST[$field]) {
				$errors[] = wfMessage('error_' . $field);
			}
		}
		
		return $errors;
	}
	
	/**
	 * Returns the list of users.
	 *
	 * @return string
	 */
	protected function _getUsers()
	{
		global $IP, $issueTrackerDeveloperGroup;
		
		$group = $issueTrackerDeveloperGroup;
		
		/** @see SpecialListusers **/
		require_once( $IP."/includes/specials/SpecialListusers.php") ;
		$specialListUsers = new SpecialListUsers();
		$users = new UsersPager($specialListUsers->getContext());
		if (! $users->mQueryDone) {
		  $users->doQuery();
		}
		$users->mResult->rewind();
		
		$list = '';
		while ($row = $users->mResult->fetchObject()) {
			$list .= '<option value="' . $row->user_name . '"';
			$list .= (isset($_POST['bt_assignee']) && $_POST['bt_assignee'] == $row->user_name) ? ' selected="true"' : '';
			$list .= '>' . $row->user_name . '</option>';
		}
		
		return $list;
	}
}
?>
