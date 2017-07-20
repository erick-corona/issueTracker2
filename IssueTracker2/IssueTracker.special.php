<?php
class SpecialIssueTracker extends SpecialPage {
	function __construct() {
		parent::__construct( 'IssueTracker' );
	}

	function execute( $par ) {
		$request = $this->getRequest();
		$output = $this->getOutput();
		$this->setHeaders();

		# Get request data from, e.g.
		$param = $request->getText( 'param' );

		# Do stuff
		# ...
		$wikitext = 'Hello world!';
		$output->addWikiText( $wikitext );
	}
}