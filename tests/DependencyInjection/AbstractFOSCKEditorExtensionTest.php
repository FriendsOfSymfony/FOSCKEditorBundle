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
        $this->assertSame('bundles/fosckeditor/', $vars['base_path']);
        $this->assertSame('bundles/fosckeditor/ckeditor.js', $vars['js_path']);
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

    public function testCustomPaths(): void
    {
        $this->loadConfiguration($this->container, 'custom_paths');
        $this->container->compile();

        $vars = $this->getVars();

        $this->assertSame('foo/', $vars['base_path']);
        $this->assertSame('foo/ckeditor.js', $vars['js_path']);
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

    public function testInvalidDefaultConfig(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('The default config "bar" does not exist.');

        $this->loadConfiguration($this->container, 'invalid_default_config');
        $this->container->compile();
        $this->getVars();
    }
}
