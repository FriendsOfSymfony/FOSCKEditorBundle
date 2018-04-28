<?php

namespace Ivory\CKEditorBundle\Tests\Builder;

use Ivory\CKEditorBundle\Builder\JsonBuilder;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class JsonBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var JsonBuilder
     */
    private $builder;
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    public function setUp()
    {
        $this->propertyAccessor = $this->createMock(PropertyAccessorInterface::class)
        $this->builder = new JsonBuilder();
    }
}
