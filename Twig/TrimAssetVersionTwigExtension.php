<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Twig;

use Twig_Extension,
    Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper;

/**
 * Trim asset version twig extension.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TrimAssetVersionTwigExtension extends Twig_Extension
{
    /** @var \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper */
    protected $assetsVersionTrimerHelper;

    /**
     * Creates a trim asset version twig extension.
     *
     * @param \Ivory\CKEditorBundle\Helper\AssetsVersionTrimerHelper $assetsVersionTrimerHelper The assets version trimer helper.
     */
    public function __construct(AssetsVersionTrimerHelper $assetsVersionTrimerHelper)
    {
        $this->assetsVersionTrimerHelper = $assetsVersionTrimerHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'trim_asset_version' => new \Twig_Filter_Method($this, 'trimAssetVersion', array('is_safe' => array('html'))),
        );
    }

    /**
     * Trim the version of an asset.
     *
     * @param string $asset The versionned asset.
     *
     * @return string The asset without version.
     */
    public function trimAssetVersion($asset)
    {
        return $this->assetsVersionTrimerHelper->trim($asset);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'trim_asset_version';
    }
}
