/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';

	config.allowedContent = true;

	config.toolbar_Basic =
        [
        	{ name: 'document', items : [ 'Source' ] },
        	{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
        	{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },
	        { name: 'links', items : [ 'Link','Unlink' ] },
	        { name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
	        { name: 'colors', items : [ 'TextColor','BGColor' ] },
	        { name: 'tools', items : [ 'Maximize', 'ShowBlocks' ] }
        ];
};
