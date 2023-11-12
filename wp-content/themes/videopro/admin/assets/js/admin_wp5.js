

jQuery(document).ready(function(){
	var el = window.wp.element.createElement,
    registerBlockType = window.wp.blocks.registerBlockType,
    blockStyle = { backgroundColor: '#fff', color: '#fff', padding: '0' };

	registerBlockType( 'gutenberg-boilerplate-es5/videopro-example', {
		title: load_trigger(),
		category: 'layout',

	    edit: function() {
	    	console.log('edit');
	    	console.log( $('body .div.components-base-control.editor-page-attributes__template') );
	        return el( 'p', { style: blockStyle }, '' );
	    },

	    save: function() {
	    	console.log('save');
	    	console.log( $('body .editor-page-attributes__template') );
	        return el( 'p', { style: blockStyle }, '' );
	    },

	    callback: function(){
	    	console.log('init');
	    }
	} );

	function load_trigger(){
		console.log('load trigger');
		console.log( $('.editor-page-attributes__template') );
		return 'Nothing';
	}

})
