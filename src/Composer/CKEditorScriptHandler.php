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

namespace FOS\CKEditorBundle\Composer;

use Composer\Script\CommandEvent;
use Composer\Script\Event;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorScriptHandler extends ScriptHandler
{
    /**
     * @param CommandEvent|Event $event
     */
    public static function install($event)
    {
        static::executeCommand(
            $event,
            static::getConsoleDir($event, 'Install CKEditor'),
            static::createCommand($event)
        );
    }

    /**
     * @param CommandEvent|Event $event
     *
     * @return string
     */
    protected static function createCommand($event)
    {
        $extra = $event->getComposer()->getPackage()->getExtra();
        $command = 'ckeditor:install';

        if (isset($extra['ckeditor-path'])) {
            $command .= ' '.$extra['ckeditor-path'];
        }

        if (isset($extra['ckeditor-release'])) {
            $command .= ' --release='.$extra['ckeditor-release'];
        }

        if (isset($extra['ckeditor-tag'])) {
            $command .= ' --tag='.$extra['ckeditor-tag'];
        }

        if (isset($extra['ckeditor-clear'])) {
            $command .= ' --clear='.$extra['ckeditor-clear'];
        }

        if (isset($extra['ckeditor-exclude'])) {
            foreach ($extra['ckeditor-exclude'] as $exclude) {
                $command .= ' --exclude='.$exclude;
            }
        }

        return $command;
    }
}
