<div class="postbox" style="margin-right:1rem;padding:1rem">
    <h1>Paramètres</h1>
    <form class="" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" method="post">
        <?php foreach( $this->settingsList as $setting ) :?>
            <div class="" style="padding:1rem 0">
                <label for="<?php echo $setting['id']?>" style="display:inline-block;min-width:200px"><?php echo $setting['label']?></label>
                <input type="<?php echo $setting['type'] ?>" name="<?php echo $setting['id'] ?>" id="<?php echo $setting['id']?>" aria-describedby="<?php $setting['id'].'-description' ?>" class="popular-category" value="<?php echo $this->getSetting( $setting['id'] ) ?>" <?php echo $this->getSetting( $setting['id'] ) === 'checked' ? 'checked' : '' ?>/>
                <p id="<?php echo $setting['id'].'-description' ?>" class="description">
                	<?php echo $setting['description'] ?>
                </p>
            </div>
        <?php endforeach;?>
        <input type="submit" class="button button-primary button-large" value="Sauvegarder">
        <input type="hidden" name="action" value="save_settings">
    </form>
    <script type="text/javascript">
    jQuery(function(){
        jQuery("input[type=checkbox]").change(function(){
            jQuery(this).prop('checked') ? jQuery(this).val("checked") : jQuery(this).val("no");
        });
    });
    </script>
</div>
