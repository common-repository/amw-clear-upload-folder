<?php

$cuf = new AMVCUFClass();

?>
<h2>List of folders: </h2>
<span class="label label-important">IMPORTANT!</span>
<p>Please choose folders that you <strong>DO NOT WANT TO CLEAN</strong></p>
<fieldset>
    <?php
    $ignoredFolders = $cuf->getIgnoredFolders();
    $folders = $cuf->getUploadFolders();
    $i = 0;
    foreach($folders as $folder) {
        if($folder['folderPath'] != '\\' && $folder['folderPath'] != '/') {
            $activeClass = '';
            $checked = '';
            // If not empty 'IGNORED' folders arr
            if(!empty($ignoredFolders)) {
                foreach($ignoredFolders as $ignoredFolder) {
                    if($folder['folderPath'] == $ignoredFolder['folder_name']) {
                        $checked = 'checked';
                    }
                }
                echo '<div id="amw-checkbox-folder-'.$i.'" class="amw-custom-checkbox-button amw-folder-button"><label for="amw-checkbox-folder-'.$i.'">';
                echo '<input type="checkbox" id="amw-checkbox-folder-'.$i.'" name="amw-checkbox-folder-'.$i.'" value="'.$folder['fullFolderPath'].'" '.$checked.'><span>'.$folder['folderPath'].'</span>';
                echo '</label></div>';
            } else {
                echo '<div id="amw-checkbox-folder-'.$i.'" class="amw-custom-checkbox-button amw-folder-button"><label for="amw-checkbox-folder-'.$i.'">';
                echo '<input type="checkbox" id="amw-checkbox-folder-'.$i.'" name="amw-checkbox-folder-'.$i.'" value="'.$folder['fullFolderPath'].'" '.$checked.'><span>'.$folder['folderPath'].'</span>';
                echo '</label></div>';
            }
            $i++;
        }
    }
    ?>
</fieldset>