<?php

/*
 * This file is part of the FOSCKEditor Bundle.
 *
 * (c) 2018 - present  Friends of Symfony
 * (c) 2009 - 2017     Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @var CKEditorRendererInterface|FormView[]
 * @var object                               $form
 * @var string                               $value
 * @var bool                                 $enable
 * @var bool                                 $async
 */
use FOS\CKEditorBundle\Renderer\CKEditorRendererInterface;
use Symfony\Component\Form\FormView;

?>
<textarea <?php echo $view['form']->block($form, 'attributes'); ?>><?php echo htmlspecialchars($value); ?></textarea>

<?php if ($enable && !$async) : ?>
    <?php include __DIR__.'/_ckeditor_javascript.html.php'; ?>
<?php endif; ?>
