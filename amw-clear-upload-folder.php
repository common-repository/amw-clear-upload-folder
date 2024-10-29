<?php
/*
Plugin Name: AMW Clear Upload Folder
Plugin URI: https://wordpress.org/plugins/amw-clear-upload-folder/
Description: Removes unused files from the 'uploads' folder
Version: 1.1.5
Author: Alimov Dmitriy
Author URI: https://www.facebook.com/alimov.dmitriy.me
License: GPL2
*/

/*  Copyright 2017  Alimov Dmitriy  (email : alimov.dmitriy.me@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * If this file is called directly, abort
 * */
if (!defined('WPINC')) {
    die;
}

/**
 * Define constants
 * */
define('AWM_CUF_VERSION', '1.1.5');
define('AWM_CUF_SITE_URL', $_SERVER['SERVER_NAME']);
define('AWM_CUF_URL', plugin_dir_url(__FILE__));
define('AWM_CUF_DIR', plugin_dir_path(__FILE__));
define('AWM_CUF_VIEWS_DIR', AWM_CUF_DIR.'/views/');

/**
 * Register hooks
 * */
register_activation_hook(__FILE__, 'amwCUFLoad_activation');

/**
 * Load neede files
 * */
function amwCUFLoad(){
    if(is_admin()) {
        require_once AWM_CUF_DIR.'loaderFunc.php';
    }
}

/**
 * Activation function
 * */
function amwCUFLoad_activation() {
    // actions on activating plugin
}

add_action('init', 'amwCUFLoad');
add_action('admin_notices', 'rateUsNotice');

?>