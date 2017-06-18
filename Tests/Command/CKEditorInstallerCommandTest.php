<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Tests\Command;

use Ivory\CKEditorBundle\Command\CKEditorInstallerCommand;
use Ivory\CKEditorBundle\Installer\CKEditorInstaller;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorInstallerCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Application
     */
    private $application;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->application = new Application();
        $this->application->addCommands([new CKEditorInstallerCommand()]);

        $this->tearDown();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        if (file_exists($path = __DIR__.'/../../Resources/public')) {
            exec('rm -rf '.$path);
        }
    }

    public function testInstall()
    {
        $command = $this->application->find('ckeditor:install');

        $tester = new CommandTester($command);
        $tester->execute(['command' => $command->getName()]);

        $this->assertInstall($tester);
    }

    public function testReinstall()
    {
        if (!method_exists(CommandTester::class, 'setInputs')) {
            $this->markTestSkipped();
        }

        $command = $this->application->find('ckeditor:install');

        $tester1 = new CommandTester($command);
        $tester1->execute($input = ['command' => $command->getName()]);

        $tester2 = new CommandTester($command);
        $tester2->setInputs([CKEditorInstaller::CLEAR_DROP]);
        $tester2->execute($input);

        $this->assertInstall($tester1);
        $this->assertInstall($tester2);
    }

    /**
     * @param CommandTester $tester
     */
    private function assertInstall(CommandTester $tester)
    {
        $this->assertContains('[OK] - CKEditor has been successfully installed...', $tester->getDisplay());
    }
}
