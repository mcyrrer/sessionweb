/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for two toolbar rows.
//	config.toolbarGroups = [
//		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
//		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
//		{ name: 'links' },
//		{ name: 'insert' },
//		{ name: 'forms' },
//		{ name: 'tools' },
//		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
//		{ name: 'others' },
//		'/',
//		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
//		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
//		{ name: 'styles' },
//		{ name: 'colors' },
//		{ name: 'about' }
//	];

    // Toolbar configuration generated automatically by the editor based on config.toolbarGroups.

    config.toolbar = [
        { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
        { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Scayt' ] },
        { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
        { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
        { name: 'tools', items: [ 'Maximize' ] },
        { name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source' ] },
        { name: 'others', items: [ '-' ] },
        '/',
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Strike', '-', 'RemoveFormat' ] },
        { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
        { name: 'styles', items: [ 'Styles', 'Format' ] },
        { name: 'colours', items: [ 'BGColor', 'TextColor' ] },
        { name: 'about', items: [ 'About' ] }
    ];

// Toolbar groups configuration.
//    config.toolbarGroups = [
//        { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
//        { name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ] },
//        { name: 'links' },
//        { name: 'insert' },
//        { name: 'forms' },
//        { name: 'tools' },
//        { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
//        { name: 'others' },
//        '/',
//        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
//        { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align' ] },
//        { name: 'styles' },
//        { name: 'colors' },
//        { name: 'about' }
//    ];


    config.height = 500;

	// Remove some buttons, provided by the standard plugins, which we don't
	// need to have in the Standard(s) toolbar.
	config.removeButtons = 'Underline,Subscript,Superscript';

    config.extraPlugins='onchange';
    config.minimumChangeMilliseconds = 2000; // 100 milliseconds (default value)
};
