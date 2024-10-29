<?php

class AMVCUFSettingsClass {

    /*------- METHODS -------*/

    function __construct(){
        ini_set('max_execution_time', 0);
        $thumbs = $this->getSettings('update_thumbs');
        $extensions = $this->getSettings('update_extensions');
        $allExtensions = $this->getSettings('all_extensions');

        if(empty($thumbs)) {
            $this->updateSettings(array('action' => 'update_thumbs', 'value' => 'false'));
        }

        if(empty($allExtensions)) {
            $extData = serialize($this->getExtensions('arr'));
            $this->updateSettings(array('action' => 'all_extensions', 'value' => $extData));
        }

        if(empty($extensions)) {
            if(empty($extData)) {
                $extData = serialize($this->getExtensions('arr'));
            }
            $this->updateSettings(array('action' => 'update_extensions', 'value' => $extData));
        }
    }

    /**
     * Get settings from the database
     **/
    public function getSettings($name) {
        global $wpdb;
        $sql = $wpdb->get_results(
            "SELECT * FROM `wp_amv_settings` WHERE `settings_name` = '$name'", ARRAY_A
        );

        if($sql && !empty($sql)) {
            return $sql;
        } else {
            return array();
        }
    }

    /**
     * Update settings from the database
     **/
    public function updateSettings($postArray) {
        global $wpdb;
        $name = $postArray['action'];
        $value = $postArray['value'];

        $settingsArr = $this->getSettings($name);

        if(!empty($settingsArr[0])) {
            if($name == 'update_extensions') {
                $tempExtArr = unserialize($settingsArr[0]['settings_value']);

                if(!in_array($value, $tempExtArr)) {
                    array_push($tempExtArr, $value);
                } else {
                    if(($key = array_search($value, $tempExtArr)) !== false) {
                        unset($tempExtArr[$key]);
                    }
                }

                $value = serialize($tempExtArr);
            }

            $sql = $wpdb->query(
                "UPDATE `wp_amv_settings` SET settings_value = '$value' WHERE settings_name = '$name'"
            );
        } else {
            $sql = $wpdb->query(
                "INSERT INTO `wp_amv_settings` (`settings_name`, `settings_value`) VALUES ('$name', '$value')"
            );
        }
        if($sql) {
            return true;
        }
    }

    /**
     * Get files extensions from db
     * $outputFormat: str or arr
     **/
    public function getExtensions($outputFormat) {
        $cuf = new AMVCUFClass();
        $allFilesArr = $cuf->getAllDBFiles();
        $extArr = array();
        $extStr = '';

        foreach($allFilesArr as $file) {
            $fileParts = pathinfo($file);
            if (!in_array($fileParts['extension'], $extArr) && !empty($fileParts['extension'])){
                if(!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $fileParts['extension'])) {
                    array_push($extArr, $fileParts['extension']);
                    $extStr .= $fileParts['extension'] . ', ';
                }
            }
        }

        $this->updateSettings(array('action' => 'all_extensions', 'value' => serialize($extArr)));

        if($outputFormat == 'arr') {
            return $extArr;
        } else if ($outputFormat == 'str') {
            return $extStr;
        }
    }
}