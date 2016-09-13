<?php
if(!defined('APPLICATION_LOADED') || !APPLICATION_LOADED) {
    die('No direct script access.');
}

$this->title = $this->project_name . ' - ' . $this->lang_php['title_create_space'];

if(!in_array($GLOBALS['CONFIG']['PERMISSIONS']['WIKI']['ADD_NEW_SPACES'], $this->permissions)) {
    redirect(base_url('wiki/' . $project_name));
}

if(isset($_POST['setspace'])) {
    $img_name = $this->uploadImage($GLOBALS['CONFIG']['IMAGESSPACESUPLOADDIR']);
    $_POST['image'] = $img_name;
    $result = $this->setSpace($_POST);
    if($result == true) {
        redirect(base_url('wiki/' . $project_name));
        $this->set_alert($this->lang_php['space_was_created'] . '!', 'success');
    }
}
?>
<script src="<?= base_url('assets/js/ckeditor/ckeditor.js') ?>"></script>
<h1><?= $this->lang_php['new_space'] ?></h1>
<?= $this->get_alert() ?>
<div class="alert alert-danger" id="sp-result"></div>
<div id="createspace">
    <form class="form-horizontal" role="form" method="post" action="" onsubmit="return validateSpace()" enctype="multipart/form-data">
        <input type="hidden" name="project_id" value="<?= $this->project_id ?>">
        <input type="hidden" name="update" value="0">
        <div class="form-group">
            <label class="control-label col-sm-2" for="name"><?= $this->lang_php['name'] ?> *</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="name" value="<?= isset($_POST['name']) ? $_POST['name'] : '' ?>" name="name">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2" for="sp_key"><?= $this->lang_php['space_key'] ?> *</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="sp_key" value="<?= isset($_POST['key_space']) ? $_POST['key_space'] : '' ?>" name="key_space">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-2" for="description"><?= $this->lang_php['description'] ?></label>
            <div class="col-sm-10">
                <textarea name="description" id="description" rows="50" class="form-control"><?= isset($_POST['description']) ? $_POST['description'] : '' ?></textarea>
                <script>
                    CKEDITOR.replace('description');
                </script>
            </div>
        </div>
        <div class="form-group">
            <img src="" alt="none" style="display:none;" id="cover">
            <label class="col-sm-2 control-label"><?= $this->lang_php['image'] ?></label>
            <div class="col-sm-10">
                <input type="file"  name="image" id="fileToUpload">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button class="btn btn-primary" name="setspace" type="submit"><?= $this->lang_php['create'] ?></button>
                <a class="btn btn-default" href="<?= base_url($this->url) ?>"><?= $this->lang_php['cancel'] ?></a>
            </div>
        </div>
    </form>
</div>
<script>
    function validateSpace() {
        var name = $("#name").val();
        var space_key = $("#sp_key").val();
        var errors = new Array();
        if (jQuery.trim(name).length <= 0 || jQuery.trim(name).length > 50) {
            errors[0] = '<strong>' + lang.space_name_err + '</strong> ' + lang.space_name_valid;
        }
        if (jQuery.trim(space_key).length > 20 || jQuery.trim(space_key).length < 3 || jQuery.trim(space_key).search(<?= $GLOBALS['CONFIG']['SPACEKEYREGEX'] ?>) === -1) {
            errors[1] = '<strong>' + lang.space_key_wrong + '</strong> ' + lang.space_key_allowed;
        } else {
            var taken;
            $.ajax({
                type: "POST",
                async: false,
                url: "<?= base_url('space_key_check') ?>",
                data: {space_key: jQuery.trim(space_key)}
            }).done(function (data) {
                if (data > 0) {
                    taken = true;
                }
            });
            if (taken == true) {
                errors[1] = '<strong>' + lang.space_taken + '</strong> ' + lang.space_select_another;
            }
        }
        if (errors.length > 0) {
            $("#sp-result").show();
            for (i = 0; i < errors.length; i++) {
                if (i == 0)
                    $("#sp-result").empty();
                if (typeof errors[i] !== 'undefined') {
                    $("#sp-result").append(errors[i] + "<br>");
                }
            }
            return false;
        } else {
            return true;
        }
    }
</script>