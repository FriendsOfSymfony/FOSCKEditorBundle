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

        <?php foreach ($styles as $styleName => $style): ?>
            CKEDITOR.stylesSet.add('<?php echo $styleName ?>', <?php echo json_encode($style) ?>);
        <?php endforeach; ?>

        <?php foreach ($templates as $templateName => $template): ?>
            CKEDITOR.addTemplates('<?php echo $templateName ?>', <?php echo json_encode($template) ?>);
        <?php endforeach; ?>

        CKEDITOR.replace('<?php echo $id ?>', <?php echo $config ?>);
    </script>
<?php endif; ?>