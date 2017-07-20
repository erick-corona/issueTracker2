<?php
/** @see IssueTrackerAction **/
require_once dirname(__FILE__) . '/IssueTrackerAction.php';

/**
 * IssueTrackerActionView class.
 *
 * @category    Extensions
 * @package     IssueTracker
 * @author      Federico Cargnelutti
 * @copyright   Copyright (c) 2008 Federico Cargnelutti
 * @license     GNU General Public Licence 2.0 or later
 */
class IssueTrackerActionView extends IssueTrackerAction
{	
	/**
	 * Executes the action.
	 *
	 * @return void 
	 */
	public function viewAction()
	{
		// Mediawiki globals
		global $wgOut;
		
		$this->_setDefaultVars();
		$this->_setHookPreferences();
		
		if ($this->issueId) {
			$rs = $this->getModel('default')->getIssueById($this->issueId);
			$this->issue = $rs->fetchObject();
			if (isset($this->issue->summary)) {
			}
			$output = $this->render();
		} else {
			$output = wfMessage('invalid_id');
		}
		
		$this->setOutput($output);
	}
	
	/**
	 * Sets the default vars.
	 *
	 * @return void 
	 */
	protected function _setDefaultVars()
	{
		// Mediawiki globals
		global $wgScript, $wgRequest;
		
		$this->action      = $this->getAction();
		$this->issueId     = $wgRequest->getText('bt_issueid');
		$this->pageKey     = $this->getNamespace('dbKey');
		$this->pageTitle   = $this->getNamespace('text');
		$this->typeArray   = $this->_config->getIssueType();
		$this->statusArray = $this->_config->getIssueStatus();
		$this->formAction  = $wgScript;
		$this->url         = $wgScript . '?title=' . $this->pageKey . '&bt_action=';
		$this->editUrl     = $this->url . 'edit&bt_issueid=' . $this->issueId;
		$this->urlpage	   = $wgScript . '?title=' ;
		$this->deleteUrl   = $this->url . 'archive&bt_issueid=' . $this->issueId;
		$this->listUrl     = $this->url . 'list';
		$this->isLoggedIn  = $this->isLoggedIn();
	}
	
	/**
	 * Processes the tag arguments.
	 *
	 * @return void 
	 */
	protected function _setHookPreferences()
	{
		if (array_key_exists('project', $this->_args) && $this->_args['project'] !== '') {
			$this->pageKey = $this->_args['project'];
		}
	}
}
?>