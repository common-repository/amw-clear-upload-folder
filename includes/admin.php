<?php

/**
 * Display page content
 * */
function amwCUFAdminPageScreen() {
    global $submenu;

    // access page settings
    $page_data = array();
    foreach ($submenu['tools.php'] as $i => $menu_item) {
        if ($submenu['tools.php'][$i][2] == 'amw-clear-upload-folder') {
            $page_data = $submenu['tools.php'][$i];
        }
    } ?>
    <div class="wrap">
        <h2>
            <?php echo $page_data[3]; ?>
        </h2>
        <ul id="amw-tabs" class="nav nav-tabs">
            <li><a href="#amw-main" data-toggle="tab" class="amw-tab-link">Main</a></li>
            <li><a href="#amw-folders" data-toggle="tab" class="amw-tab-link">List of folders</a></li>
            <li><a href="#amw-files" data-toggle="tab" class="amw-tab-link">List of files</a></li>
            <li><a href="#amw-settings" data-toggle="tab" class="amw-tab-link">Settings</a></li>
        </ul>
        <div id="amw-tabs-content" class="tab-content">
            <div class="tab-pane fade in active" id="amw-main">
                <?php load_template(AWM_CUF_VIEWS_DIR . 'main-admin.php'); ?>
            </div>
            <div class="tab-pane fade" id="amw-folders">
                <?php load_template(AWM_CUF_VIEWS_DIR . 'folders-admin.php'); ?>
            </div>
            <div class="tab-pane fade" id="amw-files">
                <?php load_template(AWM_CUF_VIEWS_DIR . 'files-admin.php'); ?>
            </div>
            <div class="tab-pane fade" id="amw-settings">
                <?php load_template(AWM_CUF_VIEWS_DIR . 'settings-admin.php'); ?>
            </div>
        </div>
    </div>
    <?php
}