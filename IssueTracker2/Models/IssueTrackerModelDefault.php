<?php
/** @see IssueTrackerModel **/
require_once dirname(__FILE__) . '/IssueTrackerModel.php';

/**
 * IssueTrackerModelDefault class.
 *
 * @category    Extensions
 * @package     IssueTracker
 * @author      Federico Cargnelutti
 * @copyright   Copyright (c) 2008 Federico Cargnelutti
 * @license     GNU General Public Licence 2.0 or later
 */
class IssueTrackerModelDefault extends IssueTrackerModel
{
	/**
	 * Database table name.
	 * @var string 
	 */
	protected $_table = 'issue_tracker';
	
	/**
	 * Selects a limited number of issues ordered by id.
	 *
	 * @param mixed $conds Conditions
	 * @param int $offset
	 * @param int $limit
	 * @return ResultSet
	 */
	public function getIssues($conds, $offset, $limit = 30)
	{		
		$options = array(
			'ORDER BY' => 'priority_date DESC, issue_id ASC',
			'LIMIT'    => $limit,
			'OFFSET'   => (int) $offset
		);

		return $this->_dbr->select($this->_table, '*', $conds, 'Database::select', $options);
	}
	
	/**
	 * Searches for a string in the title, summary and id fields.
	 *
	 * @param string $string Search keyword
	 * @param string $project Project name
	 * @param int $offset
	 * @return ResultSet
	 */
	public function getIssuesBySrting($string, $project, $offset)
	{
		$project = addslashes($project);
		$string = addslashes($string);
		$conds["project_name"] = $project ." AND (`issue_id` LIKE '%".$string."%' OR `title` LIKE '%".$string."%' OR `summary` LIKE '%".$string."%')";
		
		return $this->getIssues($conds, $offset);
	}
	
	/**
	 * Selects an issue based on a given id.
	 *
	 * @param int $issueId
	 * @return ResultSet
	 */
	public function getIssueById($issueId)
	{
		$conds['issue_id'] = (int) $issueId;
		return $this->_dbr->select($this->_table, '*', $conds, 'Database::select');
	}
	
	/**
	 * Adds a new issue to the database.
	 *
	 * @param array $postData
	 * @param int $userId
	 * @param string $userName
	 * @return bool Returns true on success or false on failure.
	 */
	public function addIssue($postData, $userId, $userName)
	{
		$data = array(
			'title'         => $postData['bt_title'], 
			'summary'       => $postData['bt_summary'], 
			'type'          => $postData['bt_type'], 
			'status'        => $postData['bt_status'], 
			'assignee'      => $postData['bt_assignee'],
			'user_id'       => $userId,
			'user_name'     => $userName,
			'project_name'  => $postData['bt_project'],
			'priority_date' => date('Y-m-d H:i:s'),
			'page'			=> $postData['bt_page'],
		);
		
		return $this->_dbr->insert($this->_table, $data);
	}
	
	/**
	 * Updates an issue.
	 *
	 * @param array $postData
	 * @param int $userId
	 * @param string $userName
	 * @return bool Returns true on success or false on failure.
	 */
	public function updateIssue($issueId, $postData)
	{
		$value = array(
			'title'    => $postData['bt_title'], 
			'summary'  => $postData['bt_summary'], 
			'type'     => $postData['bt_type'], 
			'status'   => $postData['bt_status'], 
			'assignee' => $postData['bt_assignee'],
			'page'	   => $postData['bt_page'],
		);
		
		if ($postData['bt_status'] == 't_bug') {
			$value['priority_date'] = date('Y-m-d H:i:s');
		}
		
		$conds['issue_id'] = (int) $issueId;
		
		return $this->_dbr->update($this->_table, $value, $conds);
	}
	
	/**
	 * Archives an issue.
	 *
	 * @param int $issueId
	 * @return bool Returns true on success or false on failure.
	 */
	public function archiveIssue($issueId)
	{
		$value['deleted'] = 1;
		$conds['issue_id'] = (int) $issueId;
		
		return $this->_dbr->update($this->_table, $value, $conds);
	}
}
?>