<div class="container">
    <h2>Skelbimo talpinimas</h2>

<?php
if(!empty($success_msg)) {
    echo '<p class="status-msg success">'.$success_msg.'</p>';
} elseif(!empty($error_msg)) {
    echo '<p class="status-msg success">'.$error_msg.'</p>';
}
?>

    <div class="regisFrm">
        <form actions="" method="post">
            <div class="form-group">
                <input type="text" name="postname" placeholder="Skelbimo Pavadinimas" value="<?php echo ! empty($post['postname'])?$user['postname']:''; ?>" required>
                <?php echo form_error('postname', '<p class="help-block">','</p>'); ?>
            </div>
            <div class="form-group">
                <input type="text" name="postdesc" placeholder="Skelbimo Aprasymas" value="<?php echo ! empty($post['postdesc'])?$user['postdesc']:''; ?>" required>
                <?php echo form_error('postdesc', '<p class="help-block">','</p>'); ?>
            </div>
            <br/>
            <?php for($i = 0; $i < 4; $i++) { ?>
            <div class="form-group">
                <input name="userfile[]" type="file" />
            </div>
            <?php } ?>
            <br/>

            <div class="send-button">
                <input type="submit" name"postSubmit" value="Ikelti Skelbima">
            </div> 
        </form>
    </div>
</div>
