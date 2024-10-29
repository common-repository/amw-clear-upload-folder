<?php

$cuf = new AMVCUFClass();

if (false === get_transient('server_files_names_array')) { ?>
    <div class="refresh-img-text">
        <strong>You need to refresh list of images on the server.</strong><br/>
        <strong>Please navigate to the 'List of files' tab and click 'Scan images' buttons.</strong>
    </div>
<?php } else { ?>
    <div class="btn amw-run-cleaner-button" data-action="clean">Run cleaner</div>
<?php } ?>


    <div class="preloader-wrapper-box"></div>
    <div class="accordion-wrapper">
        <div class="accordion" id="amw-removed-files-accordion">
            <?php
            $deletedFilesArr = $cuf->getDeletedFiles();
            if(!empty($deletedFilesArr)) {
                $deletedFilesMarkup = '<div class="accordion-group">';
                $deletedFilesMarkup .= '<div class="accordion-heading">';
                $deletedFilesMarkup .= '<a class="accordion-toggle" data-toggle="collapse" data-parent="#amw-server-files-accordion" href="#collapse-removed-files">Last cleaning was '.$deletedFilesArr[0]['date'].'</a>';
                $deletedFilesMarkup .= '</div>';
                $deletedFilesMarkup .= '<div id="collapse-removed-files" class="accordion-body collapse"><div class="accordion-inner"><ol>';
                foreach($deletedFilesArr as $deletedFile) {
                    $deletedFilesMarkup .= '<li>'.$deletedFile['file_name'].'</li>';
                }
                $deletedFilesMarkup .= '</ol></div></div></div>';
                echo $deletedFilesMarkup;
            }
            ?>
        </div>
    </div>
<?php
//                    echo $cuf->checkUsedMemory(memory_get_usage(true));
                ?>