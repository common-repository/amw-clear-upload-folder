<?php

$cufSettings = new AMVCUFSettingsClass();

?>
<h5 class="text-error">
    Please do not forget to click 'Rescan Images' or 'Scan Images' button on the
    'List of files' tab every time you change settings.
</h5>
<table class="table table-striped">
    <tbody>
    <tr>
        <td>Remove thumbnails?</td>
        <td>
            <?php
            $checkedThumbs = '';
            $valueThumbs = 'No';
            $thumbsArr = $cufSettings->getSettings('update_thumbs');
            if(!empty($thumbsArr[0])) {
                if($thumbsArr[0]['settings_value'] == 'true') {
                    $checkedThumbs = 'checked';
                    $valueThumbs = 'Yes';
                }
            }
            ?>
            <div class="amw-custom-checkbox-button amv-update-thumbs">
                <label for="amv-remove-thumbs-checkbox">
                    <input type="checkbox" id="amv-remove-thumbs-checkbox" name="amv-remove-thumbs-checkbox" value="" <?php echo $checkedThumbs; ?>>
                    <span><?php echo $valueThumbs; ?></span>
                </label>
            </div>
        </td>
    </tr>
    <tr>
        <td>File extensions to search</td>
        <td>
            <?php
            $extensionsArr = $cufSettings->getSettings('all_extensions');
            $extensionsArrVal = unserialize($extensionsArr[0]['settings_value']);

            $serachedExtensionsArr = $cufSettings->getSettings('update_extensions');
            $serachedExtensionsArrVal = unserialize($serachedExtensionsArr[0]['settings_value']);

            foreach($extensionsArrVal as $ext) {
                $checked = '';
                if(in_array($ext, $serachedExtensionsArrVal)) {
                    $checked = 'checked';
                } ?>
                <label class="extensions-checkbox checkbox">
                    <input type="checkbox" value="<?php echo $ext; ?>" <?php echo $checked; ?>> <?php echo $ext; ?>
                </label>
            <?php } ?>
        </td>
    </tr>
    </tbody>
</table>