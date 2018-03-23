<?php

/**
 * Assets static helper.
 *
 * Modified from simple MVC framework view class.
 *
 * @link https://github.com/simple-mvc-framework/v2/blob/master/app/helpers/assets.php
 * @package ShopSmart
 */
class Assets {
	
	/**
	 * Asset templates.
	 * 
	 * @var array 
	 */
	protected static $templates = array (
		'js'  => '<script src="%s" type="text/javascript"></script>',
		'css' => '<link href="%s" rel="stylesheet" type="text/css">'
	);
	
	/**
	 * Common templates for assets.
	 *
	 * @param string|array $files
	 * @param string       $template
	 */
	protected static function resource ($files, $template) {
		$template = self::$templates[$template];
		
		if (is_array($files)) {

			foreach ($files as $file) {
				echo sprintf($template, $file) . "\n";
			}

		} else {
			echo sprintf($template, $files) . "\n";
		}
	}
	
	/**
	 * Output script
	 * 
	 * @param array|string $file
	 */
	public static function js ($files) {
		static::resource($files, 'js');
	}
	
	/**
	 * Output stylesheet
	 * 
	 * @param string $file
	 */
	public static function css ($files) {
		static::resource($files, 'css');
	}

}
?>