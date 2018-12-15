/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';

	config.toolbar_Zoo =
	[
	    { name: 'document',    items : [ 'Source', /*'-', 'Save','NewPage','DocProps','Preview','Print','-', 'Templates' */ ] },
	    { name: 'clipboard',   items : [ 'Cut','Copy','Paste','PasteText' /*,'PasteFromWord'*/ ,'-','Undo','Redo' ] },
	    { name: 'editing',     items : [ 'Find','Replace','-','SelectAll' /*,'-','SpellChecker', 'Scayt'*/ ] },
	    //{ name: 'forms',       items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
	    //'/',
	    { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','-','RemoveFormat' ] },
	    { name: 'paragraph',   items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' /*,'-', 'BidiLtr','BidiRtl'*/ ] },
	    { name: 'links',       items : [ 'Link','Unlink' /*,'Anchor' */] },
	    { name: 'insert',      items : [ 'Image', /*'Flash',*/ 'Table','HorizontalRule','Smiley','SpecialChar','PageBreak' ] },
	    //'/',
	    { name: 'styles',      items : [ 'Styles','Format','Font','FontSize' ] },
	    { name: 'colors',      items : [ 'TextColor','BGColor' ] },
	    //{ name: 'tools',       items : [ 'Maximize', 'ShowBlocks','-','About' ] }
	];

	//config.skin = 'office2003';
	//console.log(CKEDITOR.version);
	//config.skin = 'minimalist';
	config.language = 'da';

	config.toolbar_edderkopper =
	[
	    { name: 'document',    items : [ 'Source' ] },
	    { name: 'clipboard',   items : [ 'Cut','Copy','Paste' ,'-','Undo','Redo' ] },
	    { name: 'editing',     items : [ 'Find','Replace','-','SelectAll'  ] },
	    { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
	    //{ name: 'paragraph',   items : [ 'NumberedList','BulletedList','-', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },
	    { name: 'links',       items : [ 'Link','Unlink' ]},
	    //{ name: 'styles',      items : [ 'Styles','Format','Font','FontSize' ] },
	    //{ name: 'colors',      items : [ 'TextColor','BGColor' ] },
	];

};

// 25.07.2013
CKEDITOR.plugins.load('pgrfilemanager');
