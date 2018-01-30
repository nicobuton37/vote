<label style="display:inline-block;min-width:100px" for="<?= $metabox['args']['id'] ?>"><?= $metabox['args']['label'] ?></label>
<input type="text" name="<?= $metabox['args']['id'] ?>" value="<?= get_post_meta( $metabox['args']['post_id'], $metabox['args']['id'], true) ?>">
