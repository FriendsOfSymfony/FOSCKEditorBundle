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

namespace FOS\CKEditorBundle\Tests\Command;

use FOS\CKEditorBundle\Command\CKEditorInstallerCommand;
use FOS\CKEditorBundle\Installer\CKEditorInstaller;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorInstallerCommandTest extends TestCase
{
    /**
     * @var Application
     */
    private $application;

    protected function setUp(): void
    {
        $this->application = new Application();
        $this->application->addCommands([new CKEditorInstallerCommand(new CKEditorInstaller())]);

        $this->tearDown();
    }

    protected function tearDown(): void
    {
        if (file_exists($path = __DIR__.'/../../Resources/public')) {
            exec('rm -rf '.$path);
        }
    }

    /**
     * @group installation
     */
    public function testInstall(): void
    {
        $command = $this->application->find('ckeditor:install');

        $tester = new CommandTester($command);
        $tester->execute(['command' => $command->getName()]);

        $this->assertInstall($tester);
    }

    /**
     * @group installation
     */
    public function testReinstall(): void
    {
        $command = $this->application->find('ckeditor:install');

        $tester1 = new CommandTester($command);
        $tester1->execute($input = ['command' => $command->getName()]);

        $tester2 = new CommandTester($command);
        $tester2->setInputs([CKEditorInstaller::CLEAR_DROP]);
        $tester2->execute($input);

        $this->assertInstall($tester1);
        $this->assertInstall($tester2);
    }

    private function assertInstall(CommandTester $tester): void
    {
        $this->assertContains('[OK] - CKEditor has been successfully installed...', $tester->getDisplay());
    }
}
