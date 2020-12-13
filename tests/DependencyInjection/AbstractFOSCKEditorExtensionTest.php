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
use FOS\CKEditorBundle\Exception\ConfigException;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use FOS\CKEditorBundle\FOSCKEditorBundle;
use FOS\CKEditorBundle\Tests\DependencyInjection\Compiler\TestContainerPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Form\FormFactoryBuilderInterface;
use Symfony\Component\Form\FormRendererInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * @author GeLo <geloen.eric@gmail.com>
 * @author Adam Misiorny <adam.misiorny@gmail.com>
 */
abstract class AbstractFOSCKEditorExtensionTest extends TestCase
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
     * @var PropertyAccessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $propertyAccessor;

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

    protected function setUp(): void
    {
        $this->router = $this->createMock(RouterInterface::class);
        $this->formRenderer = $this->createMock(FormRendererInterface::class);
        $this->propertyAccessor = $this->createMock(PropertyAccessorInterface::class);
        $this->packages = $this->getMockBuilder(Packages::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->container = new ContainerBuilder();
        $this->twig = $this->createMock(Environment::class);

        $this->container->set('assets.packages', $this->packages);
        $this->container->set('router', $this->router);
        $this->container->set('templating.form.renderer', $this->formRenderer);
        $this->container->set('property_accessor', $this->propertyAccessor);
        $this->container->set('twig.form.renderer', $this->formRenderer);
        $this->container->set('request_stack', $this->requestStack);
        $this->container->set('twig', $this->twig);
        $this->container->setParameter('kernel.bundles', []);
        $this->container->registerExtension($extension = new FOSCKEditorExtension());
        $this->container->loadFromExtension($extension->getAlias());

        $toBePublic = [
            'fos_ck_editor.form.type',
        ];

        $this->container->addCompilerPass(new TestContainerPass($toBePublic), PassConfig::TYPE_OPTIMIZE);
        (new FOSCKEditorBundle())->build($this->container);

        $this->factory = Forms::createFormFactoryBuilder();
        $this->formType = CKEditorType::class;
    }

    abstract protected function loadConfiguration(ContainerBuilder $container, string $configuration): void;

    public function testFormType(): void
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

    private function getVars(): array
    {
        $this->factory = $this->factory
            ->addType($this->container->get('fos_ck_editor.form.type'))
            ->getFormFactory();

        $form = $this->factory->create($this->formType);

        return $form->createView()->vars;
    }

    public function testFormTag(): void
    {
        $this->container->compile();

        $tag = $this->container->getDefinition('fos_ck_editor.form.type')->getTag('form.type');

        $this->assertSame([[]], $tag);
    }

    public function testDisable(): void
    {
        $this->loadConfiguration($this->container, 'disable');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertFalse($vars['enable']);
    }

    public function testAsync(): void
    {
        $this->loadConfiguration($this->container, 'async');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertTrue($vars['async']);
    }

    public function testAutoload(): void
    {
        $this->loadConfiguration($this->container, 'autoload');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertFalse($vars['autoload']);
    }

    public function testAutoInline(): void
    {
        $this->loadConfiguration($this->container, 'auto_inline');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertFalse($vars['auto_inline']);
    }

    public function testInline(): void
    {
        $this->loadConfiguration($this->container, 'inline');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertTrue($vars['inline']);
    }

    public function testInputSync(): void
    {
        $this->loadConfiguration($this->container, 'input_sync');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertTrue($vars['input_sync']);
    }

    public function testRequireJs(): void
    {
        $this->loadConfiguration($this->container, 'require_js');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertTrue($vars['require_js']);
    }

    public function testJquery(): void
    {
        $this->loadConfiguration($this->container, 'jquery');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertTrue($vars['jquery']);
    }

    public function testJqueryPath(): void
    {
        $this->loadConfiguration($this->container, 'jquery_path');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertSame('foo/jquery.js', $vars['jquery_path']);
    }

    public function testCustomPaths(): void
    {
        $this->loadConfiguration($this->container, 'custom_paths');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertSame('foo/', $vars['base_path']);
        $this->assertSame('foo/ckeditor.js', $vars['js_path']);
    }

    public function testFilebrowsers(): void
    {
        $this->loadConfiguration($this->container, 'filebrowsers');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertSame(
            ['VideoBrowse', 'VideoUpload'],
            $vars['filebrowsers']
        );
    }

    public function testPlugins(): void
    {
        $this->loadConfiguration($this->container, 'plugins');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertSame([
            'plugin-name' => [
                'path' => '/my/path',
                'filename' => 'plugin.js',
            ],
        ], $vars['plugins']);
    }

    public function testStylesSets(): void
    {
        $this->loadConfiguration($this->container, 'styles_sets');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertSame([
            'styles-set-name' => [
                [
                    'name' => 'Blue Title',
                    'element' => 'h2',
                    'styles' => ['text-decoration' => 'underline'],
                    'attributes' => [],
                ],
                [
                    'name' => 'CSS Style',
                    'element' => 'span',
                    'attributes' => ['data-class' => 'my-style'],
                    'styles' => [],
                ],
                [
                    'name' => 'Widget Style',
                    'type' => 'widget',
                    'widget' => 'my-widget',
                    'attributes' => ['data-class' => 'my-style'],
                    'styles' => [],
                ],
                [
                    'name' => 'Multiple Elements Style',
                    'element' => ['span', 'p', 'h3'],
                    'attributes' => ['data-class' => 'my-style'],
                    'styles' => [],
                ],
            ],
        ], $vars['styles']);
    }

    public function testTemplates(): void
    {
        $this->loadConfiguration($this->container, 'templates');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertSame([
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
        ], $vars['templates']);
    }

    public function testToolbars(): void
    {
        $this->loadConfiguration($this->container, 'toolbars');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertSame(
            [
                [
                    'Source',
                    '-',
                    'Save',
                ],
                '/',
                [
                    'Anchor',
                ],
                '/',
                [
                    'Maximize',
                ],
            ],
            $vars['config']['toolbar']
        );
    }

    public function testInvalidDefaultConfig(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('The default config "bar" does not exist.');

        $this->loadConfiguration($this->container, 'invalid_default_config');
        $this->container->compile();
        $this->getVars();
    }
}
