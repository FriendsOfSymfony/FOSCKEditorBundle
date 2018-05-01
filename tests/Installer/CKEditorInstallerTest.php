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

namespace FOS\CKEditorBundle\Tests\Installer;

use FOS\CKEditorBundle\Installer\CKEditorInstaller;
use FOS\CKEditorBundle\Tests\AbstractTestCase;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorInstallerTest extends AbstractTestCase
{
    /**
     * @var CKEditorInstaller
     */
    private $installer;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $proxy;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->installer = new CKEditorInstaller();
        $this->path = __DIR__.'/../../src/Resources/public';
        $this->proxy = 'http://178.32.218.91:80';

        $this->tearDown();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        if (file_exists($this->path)) {
            exec('rm -rf '.$this->path);
        }
    }

    public function testInstall()
    {
        $this->installer->install();

        $this->assertInstall();
    }

    public function testInstallWithPath()
    {
        $this->path = sys_get_temp_dir().'/fos-ckeditor-installer-test';
        $this->installer->install(['path' => $this->path]);

        $this->assertInstall();
    }

    public function testInstallWithRelease()
    {
        $this->installer->install($options = ['release' => CKEditorInstaller::RELEASE_BASIC]);

        $this->assertInstall($options);
    }

    public function testInstallWithVersion()
    {
        $this->installer->install($options = ['version' => '4.6.0']);

        $this->assertInstall($options);
    }

    public function testInstallWithExcludes()
    {
        $this->installer->install($options = ['excludes' => ['adapters', 'samples']]);

        $this->assertInstall($options);
    }

    public function testInstallWithHttpProxy()
    {
        putenv('http_proxy='.$this->proxy);
        $this->installer->install();
        putenv('http_proxy');

        $this->assertInstall();
    }

    public function testInstallWithHttpsProxy()
    {
        putenv('https_proxy='.$this->proxy);
        $this->installer->install();
        putenv('https_proxy');

        $this->assertInstall();
    }

    public function testInstallWithHttpProxyRequestFullUri()
    {
        putenv('http_proxy='.$this->proxy);
        putenv('http_proxy_request_fulluri=true');

        $this->installer->install();

        putenv('http_proxy');
        putenv('http_proxy_request_fulluri');

        $this->assertInstall();
    }

    public function testInstallWithHttpsProxyRequestFullUri()
    {
        putenv('https_proxy='.$this->proxy);
        putenv('https_proxy_request_fulluri=true');

        $this->installer->install();

        putenv('https_proxy');
        putenv('https_proxy_request_fulluri');

        $this->assertInstall();
    }

    public function testReinstall()
    {
        $this->installer->install();
        $this->installer->install();

        $this->assertInstall();
    }

    public function testReinstallWithClearDrop()
    {
        $this->installer->install();
        $this->installer->install($options = [
            'release' => CKEditorInstaller::RELEASE_BASIC,
            'clear' => CKEditorInstaller::CLEAR_DROP,
        ]);

        $this->assertInstall($options);
    }

    public function testReinstallWithClearKeep()
    {
        $this->installer->install(['release' => CKEditorInstaller::RELEASE_BASIC]);
        $this->installer->install($options = [
            'version' => '4.6.0',
            'release' => CKEditorInstaller::RELEASE_FULL,
            'clear' => CKEditorInstaller::CLEAR_KEEP,
        ]);

        $this->assertInstall($options);
    }

    public function testReinstallWithClearSkip()
    {
        $this->installer->install($options = ['version' => '4.6.0']);
        $this->installer->install(['clear' => CKEditorInstaller::CLEAR_SKIP]);

        $this->assertInstall($options);
    }

    /**
     * @param mixed[] $options
     */
    private function assertInstall(array $options = [])
    {
        $this->assertFileExists($this->path.'/ckeditor.js');

        if (isset($options['release'])) {
            $this->assertRelease($options['release']);
        }

        if (isset($options['version'])) {
            $this->assertVersion($options['version']);
        }

        if (!isset($options['excludes'])) {
            $options['excludes'] = ['samples'];
        }

        $this->assertExcludes($options['excludes']);
    }

    /**
     * @param string $release
     */
    private function assertRelease($release)
    {
        switch ($release) {
            case CKEditorInstaller::RELEASE_FULL:
                $this->assertFileExists($this->path.'/plugins/copyformatting');

                break;

            case CKEditorInstaller::RELEASE_BASIC:
                $this->assertFileExists($this->path.'/plugins/link');
                $this->assertFileNotExists($this->path.'/plugins/image');

                break;

            case CKEditorInstaller::RELEASE_STANDARD:
                $this->assertFileExists($this->path.'/plugins/image');
                $this->assertFileNotExists($this->path.'/plugins/copyformatting');

                break;
        }
    }

    /**
     * @param string $version
     */
    private function assertVersion($version)
    {
        $package = json_decode(file_get_contents($this->path.'/package.json'), true);

        $this->assertInternalType('array', $package);
        $this->assertArrayHasKey('version', $package);
        $this->assertSame($version, $package['version']);
    }

    /**
     * @param string[] $excludes
     */
    private function assertExcludes(array $excludes)
    {
        foreach ($excludes as $exclude) {
            $this->assertFileNotExists($this->path.'/'.$exclude);
        }
    }
}
