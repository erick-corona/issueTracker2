<?php
/**
 * IssueTrackerModel abstract class.
 *
 * @category    Extensions
 * @package     IssueTracker
 * @author      Federico Cargnelutti
 * @copyright   Copyright (c) 2008 Federico Cargnelutti
 * @license     GNU General Public Licence 2.0 or later
 */
abstract class IssueTrackerModel
{
	/**
	 * Instance of the Database class.
	 * @var Database
	 */
	protected $_dbr = null;
	
	/**
	 * Class constructor.
	 *
	 * @param int $db
	 */
	public function __construct($db = DB_SLAVE)
	{
		$this->_dbr = wfGetDB($db);
	}
}
?>