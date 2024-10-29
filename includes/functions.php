<?php

/**
 * Register menu item
 * */
add_action('admin_menu', 'amwCUFAdminMenuSetup');

function amwCUFAdminMenuSetup() {
    add_submenu_page(
        'tools.php',               // $parent_slug
        'AMW CUF Settings',        // $page_title
        'AMW Clear Upload Folder', // $menu_title
        'manage_options',          // $capability
        'amw-clear-upload-folder', // $menu_slug
        'amwCUFAdminPageScreen'    // $function
    );
}

/**
 * Settings link in plugin management screen
 * */
function amwCUFSettingsLink($actions, $file) {
    if (false !== strpos($file, 'amw-clear-upload-folder')) {
        $actions['settings'] = '<a href="tools.php?page=amw-clear-upload-folder">Settings</a>';
    }
    return $actions;
}

add_filter('plugin_action_links', 'amwCUFSettingsLink', 2, 2);

/**
 * Load needed styles and scripts
 * */
add_action('admin_enqueue_scripts', 'amwCUFScripts');
function amwCUFScripts($hook) {
    if($hook == 'tools_page_amw-clear-upload-folder') {
        wp_enqueue_script('bootstrapJS', AWM_CUF_URL.'assets/js/bootstrap.js', array('jquery'));
        wp_enqueue_script('sweetalertJS', AWM_CUF_URL.'assets/js/sweetalert.min.js', array('jquery'));
        wp_enqueue_script('amwAdminJS', AWM_CUF_URL.'assets/js/amw-admin.js', array('jquery', 'sweetalertJS'));
        wp_enqueue_script('amwSettingsJS', AWM_CUF_URL.'assets/js/amw-settings.js', array('jquery', 'sweetalertJS'));

        wp_enqueue_style('bootstrapCSS', AWM_CUF_URL.'assets/css/bootstrap.css');
        wp_enqueue_style('bootstrapResponsiveCSS', AWM_CUF_URL.'assets/css/bootstrap-responsive.css');
        wp_enqueue_style('amwStyleCSS', AWM_CUF_URL.'assets/css/amw-style.css');
    }
}

/**
 * Catch ajax actions
 * */
add_action('wp_ajax_add_ignored_folder'       , 'AMVCUFAddIgnoredFolder');
add_action('wp_ajax_nopriv_add_ignored_folder', 'AMVCUFAddIgnoredFolder');

add_action('wp_ajax_remove_ignored_folder'       , 'AMVCUFRemoveIgnoredFolder');
add_action('wp_ajax_nopriv_remove_ignored_folder', 'AMVCUFRemoveIgnoredFolder');

add_action('wp_ajax_render_images_markup'       , 'AMVCUFRenderImagesMarkup');
add_action('wp_ajax_nopriv_render_images_markup', 'AMVCUFRenderImagesMarkup');

add_action('wp_ajax_run_cleaner'       , 'AMVCUFRunCleaner');
add_action('wp_ajax_nopriv_run_cleaner', 'AMVCUFRunCleaner');

add_action('wp_ajax_update_thumbs'       , 'AMVCUFUpdateThumbs');
add_action('wp_ajax_nopriv_update_thumbs', 'AMVCUFUpdateThumbs');

add_action('wp_ajax_update_extensions'       , 'AMVCUFUpdateExtensions');
add_action('wp_ajax_nopriv_update_extensions', 'AMVCUFUpdateExtensions');

/**
 * Add rate us notice
 * */
function rateUsNotice() {
    if(strpos(get_current_screen()->base, 'amw-clear-upload-folder')) { ?>
        <div class="updated notice is-dismissible">
            <h5>Would you mind rating us?</h5>
            <p>It won’t take more than a minute and helps to promote our plugin. Thanks for your support!</p>
            <a target="_blank" href="https://wordpress.org/support/plugin/amw-clear-upload-folder/reviews/#new-post">Rate now</a>
        </div>
<?php }
}