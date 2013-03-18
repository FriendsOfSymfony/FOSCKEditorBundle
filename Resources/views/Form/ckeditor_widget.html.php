<textarea <?php echo $view['form']->block($form, 'attributes') ?>><?php echo $value ?></textarea>

<?php if ($enable) : ?>
    <script type="text/javascript">
        var CKEDITOR_BASEPATH = '<?php echo $base_path ?>';
    </script>

    <script type="text/javascript" src="<?php echo $js_path ?>"></script>

    <script type="text/javascript">
        if (CKEDITOR.instances['<?php echo $id ?>']) {
            delete CKEDITOR.instances['<?php echo $id ?>'];
        }

        <?php foreach ($plugins as $pluginName => $plugin): ?>
            CKEDITOR.plugins.addExternal('<?php echo $pluginName ?>', '<?php echo $plugin['path'] ?>', '<?php echo $plugin['filename'] ?>');
        <?php endforeach; ?>

        CKEDITOR.replace('<?php echo $id ?>', <?php echo $config ?>);
    </script>
<?php endif; ?>