/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
    config.extraPlugins='onchange';
    config.minimumChangeMilliseconds = 5000; // 100 milliseconds (default value)
    var h = $(document).height()-400;
    config.height = h;    // CSS length.
//    config.w = '300px';    // CSS length.


};
