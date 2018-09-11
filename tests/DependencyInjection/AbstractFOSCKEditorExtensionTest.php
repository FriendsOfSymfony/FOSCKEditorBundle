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

namespace FOS\CKEditorBundle\Tests\DependencyInjection;

use FOS\CKEditorBundle\DependencyInjection\FOSCKEditorExtension;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use FOS\CKEditorBundle\FOSCKEditorBundle;
use FOS\CKEditorBundle\Tests\AbstractTestCase;
use FOS\CKEditorBundle\Tests\DependencyInjection\Compiler\TestContainerPass;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormFactoryBuilderInterface;
use Symfony\Component\Form\FormRendererInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * @author GeLo <geloen.eric@gmail.com>
 * @author Adam Misiorny <adam.misiorny@gmail.com>
 */
abstract class AbstractFOSCKEditorExtensionTest extends AbstractTestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var Packages|\PHPUnit_Framework_MockObject_MockObject
     */
    private $packages;

    /**
     * @var RouterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $router;

    /**
     * @var FormRendererInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $formRenderer;

    /**
     * @var RequestStack|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestStack;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var FormFactoryBuilderInterface
     */
    private $factory;

    /**
     * @var string
     */
    private $formType;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->router = $this->createMock(RouterInterface::class);
        $this->formRenderer = $this->createMock(FormRendererInterface::class);
        $this->packages = $this->getMockBuilder(Packages::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->container = new ContainerBuilder();
        $this->twig = $this->createMock(Environment::class);

        $this->container->set('assets.packages', $this->packages);
        $this->container->set('router', $this->router);
        $this->container->set('templating.form.renderer', $this->formRenderer);
        $this->container->set('twig.form.renderer', $this->formRenderer);
        $this->container->set('request_stack', $this->requestStack);
        $this->container->set('twig', $this->twig);
        $this->container->setParameter('kernel.bundles', []);
        $this->container->registerExtension($extension = new FOSCKEditorExtension());
        $this->container->loadFromExtension($extension->getAlias());

        $toBePublic = [
            'fos_ck_editor.template_manager',
            'fos_ck_editor.form.type',
            'fos_ck_editor.config_manager',
            'fos_ck_editor.plugin_manager',
            'fos_ck_editor.styles_set_manager',
            'fos_ck_editor.toolbar_manager',
        ];
        $this->container->addCompilerPass(new TestContainerPass($toBePublic), PassConfig::TYPE_OPTIMIZE);
        (new FOSCKEditorBundle())->build($this->container);

        $this->factory = Forms::createFormFactoryBuilder();
        $this->formType = method_exists(AbstractType::class, 'getBlockPrefix') ? CKEditorType::class : 'ckeditor';
    }

    /**
     * @param ContainerBuilder $container
     * @param string           $configuration
     */
    abstract protected function loadConfiguration(ContainerBuilder $container, $configuration);

    public function testFormType()
    {
        $this->container->compile();

        $vars = $this->getVars();
        $this->assertTrue($vars['enable']);
        $this->assertTrue($vars['autoload']);
        $this->assertTrue($vars['auto_inline']);
        $this->assertFalse($vars['inline']);
        $this->assertFalse($vars['jquery']);
        $this->assertFalse($vars['input_sync']);
        $this->assertFalse($vars['require_js']);
        $this->assertEmpty($vars['filebrowsers']);
        $this->assertSame('bundles/fosckeditor/', $vars['base_path']);
        $this->assertSame('bundles/fosckeditor/ckeditor.js', $vars['js_path']);
        $this->assertSame('bundles/fosckeditor/adapters/jquery.js', $vars['jquery_path']);
    }

    private function getVars()
    {
        $this->factory = $this->factory
            ->addType($this->container->get('fos_ck_editor.form.type'))
            ->getFormFactory();

        $form = $this->factory->create($this->formType);

        return $form->createView()->vars;
    }

    public function testFormTag()
    {
        $this->container->compile();

        $tag = $this->container->getDefinition('fos_ck_editor.form.type')->getTag('form.type');

        if (!method_exists(AbstractType::class, 'getBlockPrefix')) {
            $this->assertSame([['alias' => 'ckeditor']], $tag);
        } else {
            $this->assertSame([[]], $tag);
        }
    }

    public function testDisable()
    {
        $this->loadConfiguration($this->container, 'disable');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertFalse($vars['enable']);
    }

    public function testAsync()
    {
        $this->loadConfiguration($this->container, 'async');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertTrue($vars['async']);
    }

    public function testAutoload()
    {
        $this->loadConfiguration($this->container, 'autoload');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertFalse($vars['autoload']);
    }

    public function testAutoInline()
    {
        $this->loadConfiguration($this->container, 'auto_inline');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertFalse($vars['auto_inline']);
    }

    public function testInline()
    {
        $this->loadConfiguration($this->container, 'inline');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertTrue($vars['inline']);
    }

    public function testInputSync()
    {
        $this->loadConfiguration($this->container, 'input_sync');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertTrue($vars['input_sync']);
    }

    public function testRequireJs()
    {
        $this->loadConfiguration($this->container, 'require_js');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertTrue($vars['require_js']);
    }

    public function testJquery()
    {
        $this->loadConfiguration($this->container, 'jquery');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertTrue($vars['jquery']);
    }

    public function testJqueryPath()
    {
        $this->loadConfiguration($this->container, 'jquery_path');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertSame('foo/jquery.js', $vars['jquery_path']);
    }

    public function testCustomPaths()
    {
        $this->loadConfiguration($this->container, 'custom_paths');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertSame('foo/', $vars['base_path']);
        $this->assertSame('foo/ckeditor.js', $vars['js_path']);
    }

    public function testFilebrowsers()
    {
        $this->loadConfiguration($this->container, 'filebrowsers');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertSame(
            ['VideoBrowse', 'VideoUpload'],
            $vars['filebrowsers']
        );
    }

    public function testSingleConfiguration()
    {
        $this->loadConfiguration($this->container, 'single_configuration');
        $this->container->compile();

        $configManager = $this->container->get('fos_ck_editor.config_manager');

        $expected = [
            'default' => [
                'toolbar' => 'default',
                'uiColor' => '#000000',
            ],
        ];

        $this->assertSame('default', $configManager->getDefaultConfig());
        $this->assertSame($expected, $configManager->getConfigs());
    }

    public function testMultipleConfiguration()
    {
        $this->loadConfiguration($this->container, 'multiple_configuration');
        $this->container->compile();

        $configManager = $this->container->get('fos_ck_editor.config_manager');

        $expected = [
            'default' => [
                'toolbar' => 'default',
                'uiColor' => '#000000',
            ],
            'custom' => [
                'toolbar' => 'custom',
                'uiColor' => '#ffffff',
            ],
        ];

        $this->assertSame('default', $configManager->getDefaultConfig());
        $this->assertSame($expected, $configManager->getConfigs());
    }

    public function testDefaultConfiguration()
    {
        $this->loadConfiguration($this->container, 'default_configuration');
        $this->container->compile();

        $configManager = $this->container->get('fos_ck_editor.config_manager');

        $expected = [
            'default' => ['uiColor' => '#000000'],
            'custom' => ['uiColor' => '#ffffff'],
        ];

        $this->assertSame('default', $configManager->getDefaultConfig());
        $this->assertSame($expected, $configManager->getConfigs());
    }

    public function testPlugins()
    {
        $this->loadConfiguration($this->container, 'plugins');
        $this->container->compile();

        $expected = [
            'plugin-name' => [
                'path' => '/my/path',
                'filename' => 'plugin.js',
            ],
        ];

        $this->assertSame($expected, $this->container->get('fos_ck_editor.plugin_manager')->getPlugins());
    }

    public function testStylesSets()
    {
        $this->loadConfiguration($this->container, 'styles_sets');
        $this->container->compile();

        $expected = [
            'styles-set-name' => [
                [
                    'name' => 'Blue Title',
                    'element' => 'h2',
                    'styles' => ['text-decoration' => 'underline'],
                ],
                [
                    'name' => 'CSS Style',
                    'element' => 'span',
                    'attributes' => ['data-class' => 'my-style'],
                ],
                [
                    'name' => 'Widget Style',
                    'type' => 'widget',
                    'widget' => 'my-widget',
                    'attributes' => ['data-class' => 'my-style'],
                ],
                [
                    'name' => 'Multiple Elements Style',
                    'element' => ['span', 'p', 'h3'],
                    'attributes' => ['data-class' => 'my-style'],
                ],
            ],
        ];

        $this->assertSame($expected, $this->container->get('fos_ck_editor.styles_set_manager')->getStylesSets());
    }

    public function testTemplates()
    {
        $this->loadConfiguration($this->container, 'templates');
        $this->container->compile();

        $expected = [
            'template-name' => [
                'imagesPath' => '/my/path',
                'templates' => [
                    [
                        'title' => 'My Template',
                        'image' => 'image.jpg',
                        'description' => 'My awesome description',
                        'html' => '<h1>Template</h1><p>Type your text here.</p>',
                        'template' => 'AppBundle:CKEditor:template.html.twig',
                        'template_parameters' => ['foo' => 'bar'],
                    ],
                ],
            ],
        ];

        $this->assertSame($expected, $this->container->get('fos_ck_editor.template_manager')->getTemplates());
    }

    public function testToolbars()
    {
        $this->loadConfiguration($this->container, 'toolbars');
        $this->container->compile();

        $toolbarManager = $this->container->get('fos_ck_editor.toolbar_manager');

        $this->assertSame(
            [
                'document' => ['Source', '-', 'Save'],
                'tools' => ['Maximize'],
            ],
            array_intersect_key($toolbarManager->getItems(), ['document' => true, 'tools' => true])
        );

        $this->assertSame(
            [
                'default' => [
                    '@document',
                    '/',
                    ['Anchor'],
                    '/',
                    '@tools',
                ],
                'custom' => [
                    '@document',
                    '/',
                    ['Anchor'],
                ],
            ],
            array_intersect_key($toolbarManager->getToolbars(), ['default' => true, 'custom' => true])
        );
    }

    /**
     * @expectedException \FOS\CKEditorBundle\Exception\DependencyInjectionException
     * @expectedExceptionMessage The default config "bar" does not exist.
     */
    public function testInvalidDefaultConfig()
    {
        $this->loadConfiguration($this->container, 'invalid_default_config');
        $this->container->compile();
    }
}
