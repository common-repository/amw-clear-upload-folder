<?php

$cuf = new AMVCUFClass();

if(false === get_transient('server_files_names_array')) { ?>
    <div class="btn amw-scan-images-button" data-action="scan">Scan images</div>
    <div class="accordion-wrapper">
        <div class="accordion" id="amw-server-files-accordion"></div>
    </div>
<?php } else { ?>
    <div class="btn amw-rescan-images-button" data-action="rescan">ReScan images</div>
    <div class="accordion-wrapper">
        <div class="accordion" id="amw-server-files-accordion">
            <?php $cuf->renderImagesAccordion(); ?>
        </div>
    </div>
<?php } ?>