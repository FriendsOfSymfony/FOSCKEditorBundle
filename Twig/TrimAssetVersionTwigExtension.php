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

use \Twig_Extension;

/**
 * Trim asset version twig extension.
 *
 * @author GeLo <geloen.eric@gmail.com>
 */
class TrimAssetVersionTwigExtension extends Twig_Extension
{
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
        if (($position = strpos($asset, '?')) !== false) {
            return substr($asset, 0, $position);
        }

        return $asset;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'trim_asset_version';
    }
}
