<?php

class AMVCUFClass {

    /*------- METHODS -------*/

    function __construct(){
        ini_set('max_execution_time', 0);
        $this->checkDBTable();
    }

    /**
     * Clear transient caches
     **/
    public function cleanTransient() {
        delete_transient('db_files_names_array');
        delete_transient('server_files_names_array');
        delete_transient('server_dir_names_array');
    }

    public function checkUsedMemory($size) {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    /**
     * Check if plugin DB table exist, if not create
     **/
    public function checkDBTable() {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        global $wpdb;

        $resultTableName = $wpdb->prefix.'amv_data_table_result';
        $ignoredFolders = $wpdb->prefix.'amv_ignored_folders';
        $deletedFiles = $wpdb->prefix.'amv_deleted_files';
        $settingsTableName = $wpdb->prefix.'amv_settings';
        if($wpdb->get_var("SHOW TABLES LIKE '$resultTableName'") != $resultTableName) {
            $charset_collate = $wpdb->get_charset_collate();

            $resultTableNameSql = "CREATE TABLE $resultTableName (
              id mediumint(9) NOT NULL AUTO_INCREMENT,
              image_link text NOT NULL,
              result text NOT NULL,
              UNIQUE KEY id (id)
            ) $charset_collate;";
            dbDelta($resultTableNameSql);
        }

        if($wpdb->get_var("SHOW TABLES LIKE '$ignoredFolders'") != $ignoredFolders) {
            $charset_collate = $wpdb->get_charset_collate();

            $ignoredFoldersSql = "CREATE TABLE $ignoredFolders (
              id mediumint(9) NOT NULL AUTO_INCREMENT,
              folder_name text NOT NULL,
              folder_path text NOT NULL,
              UNIQUE KEY id (id)
            ) $charset_collate;";
            dbDelta($ignoredFoldersSql);
        }

        if($wpdb->get_var("SHOW TABLES LIKE '$deletedFiles'") != $deletedFiles) {
            $charset_collate = $wpdb->get_charset_collate();

            $deletedFilesSql = "CREATE TABLE $deletedFiles (
              id mediumint(9) NOT NULL AUTO_INCREMENT,
              file_name text NOT NULL,
              date date NOT NULL default '0000-00-00',
              UNIQUE KEY id (id)
            ) $charset_collate;";
            dbDelta($deletedFilesSql);
        }

        if($wpdb->get_var("SHOW TABLES LIKE '$settingsTableName'") != $settingsTableName) {
            $charset_collate = $wpdb->get_charset_collate();

            $settingsTableNameSql = "CREATE TABLE $settingsTableName (
              id mediumint(9) NOT NULL AUTO_INCREMENT,
              settings_name text NOT NULL,
              settings_value text NOT NULL,
              UNIQUE KEY id (id)
            ) $charset_collate;";
            dbDelta($settingsTableNameSql);
        }
    }

    /**
     * Get upload folders
    **/
    public function getUploadFolders(){
        $serverFoldersArr = get_transient('server_dir_names_array');
        $upload_dir = wp_upload_dir();
        $serverPath = $upload_dir['basedir'];

        // Put dir names to the transient cache for 12 hours
        if (false === $serverFoldersArr) {
            $serverFoldersArr = array();
            $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($serverPath), RecursiveIteratorIterator::SELF_FIRST);
            foreach($objects as $name => $object){
                if(is_dir($name) && $object->getFilename() == '.') {
                    $folderPath = explode('wp-content/uploads', rtrim($name, "."));
                    $fullFolderPath = rtrim($name, ".");
                    $serverFoldersArr[] = array(
                        'folderPath' => str_replace('\\', '/', $folderPath[1]),
                        'fullFolderPath' => str_replace('\\', '/', $fullFolderPath)
                    );
                }
            }

            set_transient('server_dir_names_array', $serverFoldersArr, 12 * HOUR_IN_SECONDS);
        }

        return $serverFoldersArr;
    }

    /**
     * Get upload files
     **/
    public function getUploadFiles($type=null){
        if($type == 'rescan') {
            delete_transient('server_files_names_array');
        }

        $serverFilesArr = get_transient('server_files_names_array');

        $ignoredFoldersArr = $this->getIgnoredFolders();
        $foldersArr = $this->getUploadFolders();

        // Remove ignored folders
        if(!empty($ignoredFoldersArr)) {
            foreach($foldersArr as $key => $folder) {
                foreach($ignoredFoldersArr as $ignoredFolder) {
                    if($ignoredFolder['folder_name'] == $folder['folderPath']){
                        unset($foldersArr[$key]);
                    }
                }
            }
        }

        if (false === $serverFilesArr) {
            $cufSettings = new AMVCUFSettingsClass();
            $serverFilesArr = array();
            $extArr = $cufSettings->getSettings('update_extensions');
            $extArr = unserialize($extArr[0]['settings_value']);
            $extStr = '{'.implode(',', $extArr).'}';

            foreach($foldersArr as $folder) {
                if($folder['folderPath'] != '\\') {
                    $directory = $folder['fullFolderPath'];
                    $images = glob("$directory*.$extStr", GLOB_BRACE);

                    foreach($images as $key => $image) {
                        $imgUrl = explode('/wp-content/uploads', $image);
                        $serverFilesArr[$folder['folderPath']][$key] .= str_replace('\\', '/', $imgUrl[1]);
                    }
                }
            }
            set_transient('server_files_names_array', $serverFilesArr, 12 * HOUR_IN_SECONDS);
        }

        return $serverFilesArr;
    }

    /**
     * Get all files from DB
     **/
    public function getAllDBFiles() {
        $allDBArr = get_transient('all_db_files_names_array');

        if(false === $allDBArr) {
            global $wpdb;
            $allDBArr = array();
            $postStr = '';
            $postMetaStr = '';

            $sqlPost = $wpdb->get_results(
                "SELECT guid FROM wp_posts WHERE post_type = 'attachment' AND guid LIKE '%".AWM_CUF_SITE_URL."%' ", ARRAY_A
            );
            $sqlPostMeta = $wpdb->get_results(
                "SELECT meta_value FROM wp_postmeta WHERE meta_value LIKE '%".AWM_CUF_SITE_URL."%' ", ARRAY_A
            );

            if(!empty($sqlPost)) {
                foreach($sqlPost as $img) {
                    $imgUrl = explode('/wp-content/uploads', $img['guid']);
                    array_push($allDBArr, $imgUrl[1]);
                }
            }

            if(!empty($sqlPostMeta)) {
                foreach($sqlPostMeta as $img) {
                    $imgUrl = explode('/wp-content/uploads', $img['meta_value']);
                    array_push($allDBArr, $imgUrl[1]);
                }
            }

            set_transient('all_db_files_names_array', $allDBArr, 12 * HOUR_IN_SECONDS);
        }

        return $allDBArr;
    }

    /**
     * Get files from DB
     **/
    public function getDBImages() {
        $noIgnoredFoldersFilesArr = get_transient('db_files_names_array');

        if(false === $noIgnoredFoldersFilesArr) {
            $filesDBArr = $this->getAllDBFiles();

            if(!empty($filesDBArr)) {
                $cufSettings = new AMVCUFSettingsClass();
                $postStr = '';
                $postMetaStr = '';
                $noIgnoredFoldersFilesArr = array();
                $extArr = $cufSettings->getSettings('update_extensions');
                $extArr = unserialize($extArr[0]['settings_value']);
                $ignoredFoldersArr = $this->getIgnoredFolders();

                foreach($filesDBArr as $file) {
                    $exist = false;
                    foreach($ignoredFoldersArr as $ignoredFolder) {
                        if(strpos($file, $ignoredFolder['folder_name']) !== false) {
                            $exist = true;
                        }
                    }
                    if(!$exist) {
                        $fileParts = pathinfo($file);
                        if (in_array($fileParts['extension'], $extArr)){
                            array_push($noIgnoredFoldersFilesArr, $file);
                        }
                    }
                }
            }

            set_transient('db_files_names_array', $noIgnoredFoldersFilesArr, 12 * HOUR_IN_SECONDS);
        }

        return $noIgnoredFoldersFilesArr;
    }

    /**
     * Get ignored folders to the database
     **/
    public function getIgnoredFolders() {
        global $wpdb;
        $sql = $wpdb->get_results(
            "SELECT * FROM `wp_amv_ignored_folders`", ARRAY_A
        );

        if($sql && !empty($sql)) {
            foreach($sql as $key => $folder) {
                $sql[$key]['folder_name'] = base64_decode($folder['folder_name']);
                $sql[$key]['folder_path'] = base64_decode($folder['folder_path']);
            }
            return $sql;
        } else {
            return array();
        }
    }

    /**
     * Add ignored folders to the database
     **/
    public function addIgnoredFolders($data) {
        global $wpdb;
        $inArray = false;
        $foldersArr = $this->getIgnoredFolders();

        // Check if folder exist in DB
        foreach ($foldersArr as $folder) {
            if($folder['folder_name'] == $data['folderName']) {
                $inArray = true;
            }
        }
        if(!$inArray) {
            $sql = $wpdb->query(
                "INSERT INTO `wp_amv_ignored_folders` (`folder_name`, `folder_path`) VALUES ('".base64_encode(str_replace('\\', '/', $data['folderName']))."', '".base64_encode(str_replace('\\', '/', $data['folderFullPath']))."')"
            );
            if($sql) {
                $this->cleanTransient();
                return true;
            }
        }
    }

    /**
     * Remove ignored folders from database
     **/
    public function removeIgnoredFolders($data) {
        global $wpdb;
        $foldersArr = $this->getIgnoredFolders();
        foreach ($foldersArr as $folder) {
            if($folder['folder_name'] == $data['folderName']) {
                $sql = $wpdb->query(
                    "DELETE FROM `wp_amv_ignored_folders` WHERE id = ".$folder['id']
                );
                if($sql) {
                    $this->cleanTransient();
                    return true;
                }
            }
        }
    }

    /**
     * Remove ignored folders from database
     **/
    public function getDeletedFiles() {
        global $wpdb;
        $sql = $wpdb->get_results(
            "SELECT * FROM `wp_amv_deleted_files`", ARRAY_A
        );
        return $sql;
    }

    /**
     * Render markup for list of images on admin page
     **/
    public function renderImagesAccordion($postArray=null) {
        $markup = '';
        $imagesArr = $this->getUploadFiles($postArray['type']);
        if(!empty($imagesArr)) {
            $i = 0;
            foreach($imagesArr as $folder => $image) {
                $markup .= '<div class="accordion-group">';
                $markup .= '<div class="accordion-heading">';
                $markup .= '<a class="accordion-toggle" data-toggle="collapse" data-parent="#amw-server-files-accordion" href="#collapse-'.$i.'">'.$folder.'</a>';
                $markup .= '</div>';
                $markup .= '<div id="collapse-'.$i.'" class="accordion-body collapse">';
                $markup .= '<div class="accordion-inner"><ol>';
                foreach($image as $img) {
                    $markup .= '<li><a href="'.get_site_url().'/wp-content/uploads'.$img.'" target="_blank">'.$img.'</a></li>';
                }
                $markup .= '</ol></div>';
                $markup .= '</div>';
                $markup .= '</div>';

                $i++;
            }
        }
        if($postArray['action'] == 'render_images_markup'){
            return $markup;
        } else {
            echo $markup;
        }
    }

    /**
     * Check if part of name in array LIKE (for thumbnails)
     **/
    public function isThumbnailsExist($string, $arr) {
        preg_match('/\/([a-zA-Z0-9_-—\D]*)\./', $string, $stringPart);
//        preg_match('/\/([a-zA-Z0-9_-]*)\./', $string, $stringPart);
        preg_match('/(\d+)(x|X)(\d+)$/', $stringPart[1], $fileName);

        // Checking if thumb size array not empty
        if(!empty($fileName)) {
            $searchedName = str_replace('-'.$fileName[0], '', $stringPart[1]);
        } else {
            $searchedName = $stringPart[1];
        }

        foreach($arr as $elem) {
            if(strpos($elem, $searchedName) !== false) {
                return true;
            }
        }
    }

    /**
     * Run cleaner
     **/
    public function runCleaner() {
        global $wpdb;
        $wpdb->query("DELETE FROM `wp_amv_deleted_files`");
        $upload_dir = wp_upload_dir();
        $serverPath = $upload_dir['basedir'];
        $imagesDBArr = $this->getDBImages();
        $imagesLocalArr = $this->getUploadFiles();
        $removedFilesArr = array();
        $unRemovedFilesArr = array();
        $today = date('Y-m-d');

        // If remove thumbs setting == false
        $cufSettings = new AMVCUFSettingsClass();
        $settingsArr = $cufSettings->getSettings('update_thumbs');

        if($settingsArr[0]['settings_value'] == 'false') {
            foreach($imagesLocalArr as $folder => $valArr) {
                foreach($valArr as $val) {
                    if(!$this->isThumbnailsExist($val, $imagesDBArr)) {
                        if(unlink($serverPath.$val)) {
                            $sql = $wpdb->query(
                                "INSERT INTO `wp_amv_deleted_files` (`file_name`, `date`) VALUES ('".$val."', '".$today."')"
                            );
                            array_push($removedFilesArr, $val);
                        } else {
                            array_push($unRemovedFilesArr, $val);
                        }
                    }
                }
            }
        } else {
            foreach($imagesLocalArr as $folder => $valArr) {
                foreach($valArr as $val) {
                    if(!in_array($val, $imagesDBArr)) {
                        if(unlink($serverPath.$val)) {
                            $sql = $wpdb->query(
                                "INSERT INTO `wp_amv_deleted_files` (`file_name`, `date`) VALUES ('".$val."', '".$today."')"
                            );
                            array_push($removedFilesArr, $val);
                        } else {
                            array_push($unRemovedFilesArr, $val);
                        }
                    }
                }
            }
        }

        // Check if all unused files were removed
        if(!empty($unRemovedFilesArr)) {
            foreach($unRemovedFilesArr as $file) {
                if(unlink($serverPath.$file)) {
                    $sql = $wpdb->query(
                        "INSERT INTO `wp_amv_deleted_files` (`file_name`, `date`) VALUES ('".$file."', '".$today."')"
                    );
                    array_push($removedFilesArr, $file);
                }
            }
        }

        return $removedFilesArr;
    }
}