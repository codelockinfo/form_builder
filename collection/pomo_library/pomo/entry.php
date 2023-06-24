<?php
if ( ! class_exists( 'Translation_Entry', false ) ):
class Translation_Entry {
	var $is_plural = false;
	var $context = null;
	var $singular = null;
	var $plural = null;
	var $translations = array();
	var $translator_comments = '';
	var $extracted_comments = '';
	var $references = array();
	var $flags = array();
	function __construct( $args = array() ) {
		if (!isset($args['singular'])) {
			return;
		}
		foreach ($args as $varname => $value) {
			$this->$varname = $value;
		}
		if (isset($args['plural']) && $args['plural']) $this->is_plural = true;
		if (!is_array($this->translations)) $this->translations = array();
		if (!is_array($this->references)) $this->references = array();
		if (!is_array($this->flags)) $this->flags = array();
	}
	public function Translation_Entry( $args = array() ) {
		self::__construct( $args );
	}
	function key() {
		if ( null === $this->singular || '' === $this->singular ) return false;
		$key = !$this->context? $this->singular : $this->context.chr(4).$this->singular;
		$key = str_replace( array( "\r\n", "\r" ), "\n", $key );

		return $key;
	}
	function merge_with(&$other) {
		$this->flags = array_unique( array_merge( $this->flags, $other->flags ) );
		$this->references = array_unique( array_merge( $this->references, $other->references ) );
		if ( $this->extracted_comments != $other->extracted_comments ) {
			$this->extracted_comments .= $other->extracted_comments;
		}

	}
}
endif;