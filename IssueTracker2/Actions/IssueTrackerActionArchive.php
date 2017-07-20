<?php
/** @see IssueTrackerAction **/
require_once dirname(__FILE__) . '/IssueTrackerAction.php';

/**
 * IssueTrackerActionArchive class.
 *
 * @category    Extensions
 * @package     IssueTracker
 * @author      Federico Cargnelutti
 * @copyright   Copyright (c) 2008 Federico Cargnelutti
 * @license     GNU General Public Licence 2.0 or later
 */
class IssueTrackerActionArchive extends IssueTrackerAction
{
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
	 * Executes the action.
	 *
	 * @return void 
	 */
	public function archiveAction()
	{
		global $wgUser, $wgScript, $wgRequest;
		
		$listUrl = $wgScript . '?title=' . $this->getNamespace('dbKey') . '&bt_action=list';
		
		$userId = $wgUser->getID();
		$userName = $wgUser->getName();
		
		$issueId = $wgRequest->getText('bt_issueid');
		$this->getModel('default')->archiveIssue($issueId);
		
		header('Location: ' . $listUrl);
	}
}
?>