<?php

function AMVCUFAddIgnoredFolder() {
    $postArray = (filter_input_array(INPUT_POST, $_POST));

    if(isset($postArray['action']) && $postArray['action'] == 'add_ignored_folder') {
        $cuf = new AMVCUFClass();
        $result = $cuf->addIgnoredFolders($postArray);

        if($result) {
            $output = json_encode(
                array(
                    'status' => 'success',
                    'status_code' => 200,
                    'result' => 'Folder '.$postArray['folderName'].' was added to the IGNORED'
                )
            );
            die($output);
        } else {
            $output = json_encode(
                array(
                    'status' => 'fail',
                    'status_code' => 500
                )
            );
            die($output);
        }
    }
}

function AMVCUFRemoveIgnoredFolder() {
    $postArray = (filter_input_array(INPUT_POST, $_POST));

    if(isset($postArray['action']) && $postArray['action'] == 'remove_ignored_folder') {
        $cuf = new AMVCUFClass();
        $result = $cuf->removeIgnoredFolders($postArray);
        if($result) {
            $output = json_encode(
                array(
                    'status' => 'success',
                    'status_code' => 200,
                    'result' => 'Folder '.$postArray['folderName'].' was removed from the IGNORED'
                )
            );
            die($output);
        } else {
            $output = json_encode(
                array(
                    'status' => 'fail',
                    'status_code' => 500
                )
            );
            die($output);
        }
    }
}

function AMVCUFRenderImagesMarkup() {
    $postArray = (filter_input_array(INPUT_POST, $_POST));

    if(isset($postArray['action']) && $postArray['action'] == 'render_images_markup') {
        $cuf = new AMVCUFClass();
        $result = $cuf->renderImagesAccordion($postArray);
        if($result) {
            $output = json_encode(
                array(
                    'status' => 'success',
                    'status_code' => 200,
                    'result' => $result
                )
            );
            die($output);
        } else {
            $output = json_encode(
                array(
                    'status' => 'fail',
                    'status_code' => 500
                )
            );
            die($output);
        }
    }
}

function AMVCUFRunCleaner() {
    $postArray = (filter_input_array(INPUT_POST, $_POST));

    if(isset($postArray['action']) && $postArray['action'] == 'run_cleaner') {
        $cuf = new AMVCUFClass();
        $result = $cuf->runCleaner();
        if(!empty($result)) {
            $output = json_encode(
                array(
                    'status' => 'success',
                    'status_code' => 200,
                    'result' => $result
                )
            );
            die($output);
        } else if(empty($result)) {
            $output = json_encode(
                array(
                    'status' => 'success',
                    'status_code' => 200,
                    'result' => $result
                )
            );
            die($output);
        } else {
            array(
                'status' => 'fail',
                'status_code' => 500
            );
            die($output);
        }
    }
}

function AMVCUFUpdateThumbs() {
    $postArray = (filter_input_array(INPUT_POST, $_POST));

    if(isset($postArray['action']) && $postArray['action'] == 'update_thumbs') {
        $cufSettings = new AMVCUFSettingsClass();
        $result = $cufSettings->updateSettings($postArray);
        if(!empty($result)) {
            $output = json_encode(
                array(
                    'status' => 'success',
                    'status_code' => 200,
                    'result' => $result
                )
            );
            die($output);
        } else if(empty($result)) {
            $output = json_encode(
                array(
                    'status' => 'success',
                    'status_code' => 200,
                    'result' => $result
                )
            );
            die($output);
        } else {
            array(
                'status' => 'fail',
                'status_code' => 500
            );
            die($output);
        }
    }
}

function AMVCUFUpdateExtensions() {
    $postArray = (filter_input_array(INPUT_POST, $_POST));

    if(isset($postArray['action']) && $postArray['action'] == 'update_extensions') {
        $cufSettings = new AMVCUFSettingsClass();
        $result = $cufSettings->updateSettings($postArray);
        if(!empty($result)) {
            $output = json_encode(
                array(
                    'status' => 'success',
                    'status_code' => 200,
                    'result' => $result
                )
            );
            die($output);
        } else if(empty($result)) {
            $output = json_encode(
                array(
                    'status' => 'success',
                    'status_code' => 200,
                    'result' => $result
                )
            );
            die($output);
        } else {
            array(
                'status' => 'fail',
                'status_code' => 500
            );
            die($output);
        }
    }
}