<?php
/** @see IssueTrackerActionAdd **/
require_once dirname(__FILE__) . '/IssueTrackerActionAdd.php';

/**
 * IssueTrackerActionEdit class.
 *
 * @category    Extensions
 * @package     IssueTracker
 * @author      Federico Cargnelutti
 * @copyright   Copyright (c) 2008 Federico Cargnelutti
 * @license     GNU General Public Licence 2.0 or later
 */
class IssueTrackerActionEdit extends IssueTrackerActionAdd
{	
	/**
	 * Executes the edit action.
	 *
	 * @return void 
	 */
	public function editAction()
	{
		global $wgUser;
		
		$this->_setDefaultVars();
		$this->_setHookPreferences();
		
		if (isset($_POST['bt_submit']) && $this->issueId !== 0) {
			$errorMessages = $this->_getErrors($this->_requiredFields);	
			if (count($errorMessages) == 0) {
				$userId = $wgUser->getID();
				$userName = $wgUser->getName();
				$result = $this->getModel('default')->updateIssue($this->issueId, $_POST);
				header('Location: ' . $this->listUrl);
			} else {
				$this->errors = implode('<br />', $errorMessages);
			}
		} elseif ($this->issueId !== 0) {
			$rs = $this->getModel('default')->getIssueById($this->issueId);
			$row = $rs->fetchObject();
			
			$_POST = array(
				'bt_issueid'  => $this->issueId,
				'bt_title'    => $row->title,
				'bt_summary'  => $row->summary,
				'bt_type'     => $row->type,
				'bt_status'   => $row->status,
				'bt_assignee' => $row->assignee,
				'bt_page' 	  => $row->page,
			);
		} else {
			header('Location: ' . $this->listUrl);
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
		global $wgRequest;
		
		parent::_setDefaultVars();
		
		$this->action = 'edit';
		$this->issueId = (int) $wgRequest->getText('bt_issueid');
	}
	
	/**
	 * Processes the tag attributes.
	 *
	 * @return void 
	 */
	protected function _setHookPreferences()
	{
		parent::_setHookPreferences();
	}
}
?>