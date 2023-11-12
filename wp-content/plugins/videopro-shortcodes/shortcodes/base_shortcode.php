<?php

class CactusShortcode {
	public $id = '';

	public $attributes = array();
	
	public $tag = '';
	
	public $content = '';
	
	public function __construct( $tag, $attrs = null, $content = '' ) {
		$this->tag = $tag;
		
		if( isset($attrs) ){
			$this->attributes = $attrs;
			$this->content = $content;
		} else {
			add_shortcode( $this->tag, array($this, 'parse_shortcode' ) );
		}
	}
	
	
	public function generate_id(){
		$id = 'ct_custom_' . rand(0, 1000) . time();
		$this->id = $id;
	}
	
	/** 
	 * print shortcode output
	 *
	 * $attritube_only - to print out attributes string only
	 *
	 * @return - string
	 */
	public function to_string($attritube_only = false){
		$attrs = '';

		if($this->id == ''){
			$this->generate_id();
		}
		
		$tag = '[' . $this->tag;
		
		$attrs .=  ' id="'. $this->id . '"';
		
		if(isset($this->attributes) && is_array($this->attributes)){
			foreach($this->attributes as $key => $val){
				if($key != 'id'){
					$attrs .= ' ' . $key . '="' . $val . '"';
				}
			}
		}
		
		$tag .= $attrs;

		$tag .= ']';
		
		if($this->content != ''){
			$tag .= $this->content . '[/' . $this->tag .']';
		}
		
		if($attritube_only) return $attrs;
		
		return $tag;
	}
	
	/**
	 * do the shortcode
	 */
	protected function parse_shortcode($atts, $content){}
	
	/**
	 * if shortcode has some attritubes that need to be put in custom css, put it here
	 *
	 * @return custom css string
	 */
	public function generate_inline_css($attrs = array()){}
	
}