<?php

/*
 * This file is part of the Ivory CKEditor package.
 *
 * (c) Eric GELOEN <geloen.eric@gmail.com>
 *
 * For the full copyright and license information, please read the LICENSE
 * file that was distributed with this source code.
 */

namespace Ivory\CKEditorBundle\Installer;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorInstaller
{
    const RELEASE_BASIC = 'basic';
    const RELEASE_FULL = 'full';
    const RELEASE_STANDARD = 'standard';

    const VERSION_LATEST = 'latest';

    const CLEAR_DROP = 'drop';
    const CLEAR_KEEP = 'keep';
    const CLEAR_SKIP = 'skip';

    const NOTIFY_CLEAR = 'clear';
    const NOTIFY_CLEAR_ARCHIVE = 'clear-archive';
    const NOTIFY_CLEAR_COMPLETE = 'clear-complete';
    const NOTIFY_CLEAR_PROGRESS = 'clear-progress';
    const NOTIFY_CLEAR_QUESTION = 'clear-question';
    const NOTIFY_CLEAR_SIZE = 'clear-size';

    const NOTIFY_DOWNLOAD = 'download';
    const NOTIFY_DOWNLOAD_COMPLETE = 'download-complete';
    const NOTIFY_DOWNLOAD_PROGRESS = 'download-progress';
    const NOTIFY_DOWNLOAD_SIZE = 'download-size';

    const NOTIFY_EXTRACT = 'extract';
    const NOTIFY_EXTRACT_COMPLETE = 'extract-complete';
    const NOTIFY_EXTRACT_PROGRESS = 'extract-progress';
    const NOTIFY_EXTRACT_SIZE = 'extract-size';

    /**
     * @var string
     */
    private static $archive = 'https://github.com/ckeditor/ckeditor-releases/archive/%s/%s.zip';

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $release;

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $clear;

    /**
     * @var string[]
     */
    private $excludes;

    /**
     * @param string|null $path
     * @param string|null $release
     * @param string|null $version
     * @param string|null $clear
     * @param string[]    $excludes
     */
    public function __construct(
        $path = null,
        $release = null,
        $version = null,
        $clear = null,
        array $excludes = ['samples']
    ) {
        $this->path = $path ?: dirname(__DIR__).'/Resources/public';
        $this->release = $release ?: self::RELEASE_FULL;
        $this->version = $version ?: self::VERSION_LATEST;
        $this->clear = $clear ?: self::CLEAR_SKIP;
        $this->excludes = $excludes;
    }

    /**
     * @param mixed[] $options
     *
     * @return bool
     */
    public function install(array $options = [])
    {
        $path = rtrim(isset($options['path']) ? $options['path'] : $this->path, '/');
        $clear = isset($options['clear']) ? $options['clear'] : null;
        $notifier = isset($options['notifier']) ? $options['notifier'] : null;

        if ($this->clear($path, $clear, $notifier) === self::CLEAR_SKIP) {
            return false;
        }

        $release = isset($options['release']) ? $options['release'] : $this->release;
        $version = isset($options['version']) ? $options['version'] : $this->version;
        $excludes = isset($options['excludes']) ? $options['excludes'] : $this->excludes;

        $zip = $this->download($release, $version, $notifier);
        $this->extract($zip, $path, $release, $version, $excludes, $notifier);

        return true;
    }

    /**
     * @param string        $path
     * @param int|null      $clear
     * @param callable|null $notifier
     *
     * @return int
     */
    private function clear($path, $clear = null, callable $notifier = null)
    {
        if (!file_exists($path.'/ckeditor.js')) {
            return self::CLEAR_DROP;
        }

        if ($clear === null) {
            $clear = $this->notify($notifier, self::NOTIFY_CLEAR, $path);
        }

        if ($clear === null) {
            $clear = $this->clear;
        }

        if ($clear === self::CLEAR_DROP) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            $this->notify($notifier, self::NOTIFY_CLEAR_SIZE, iterator_count($files));

            foreach ($files as $file) {
                $filePath = $file->getRealPath();
                $this->notify($notifier, self::NOTIFY_CLEAR_PROGRESS, $filePath);

                if ($dir = $file->isDir()) {
                    $success = @rmdir($filePath);
                } else {
                    $success = @unlink($filePath);
                }

                if (!$success) {
                    throw $this->createException(sprintf(
                        'Unable to remove the %s "%s".',
                        $dir ? 'directory' : 'file',
                        $filePath
                    ));
                }
            }

            $this->notify($notifier, self::NOTIFY_CLEAR_COMPLETE);
        }

        return $clear;
    }

    /**
     * @param string        $release
     * @param string        $version
     * @param callable|null $notifier
     *
     * @return string
     */
    private function download($release, $version, callable $notifier = null)
    {
        $url = sprintf(self::$archive, $release, $version);
        $this->notify($notifier, self::NOTIFY_DOWNLOAD, $url);

        $zip = @file_get_contents($url, false, $this->createStreamContext($notifier));

        if ($zip === false) {
            throw $this->createException(sprintf('Unable to download CKEditor ZIP archive from "%s".', $url));
        }

        $path = tempnam(sys_get_temp_dir(), sprintf('ckeditor-%s-%s-', $release, $version)).'.zip';

        if (!@file_put_contents($path, $zip)) {
            throw $this->createException(sprintf('Unable to write CKEditor ZIP archive to "%s".', $path));
        }

        $this->notify($notifier, self::NOTIFY_DOWNLOAD_COMPLETE, $path);

        return $path;
    }

    /**
     * @param callable|null $notifier
     *
     * @return resource
     */
    private function createStreamContext(callable $notifier = null)
    {
        return stream_context_create([], [
            'notification' => function (
                $code,
                $severity,
                $message,
                $messageCode,
                $transferred,
                $size
            ) use ($notifier) {
                if ($notifier === null) {
                    return;
                }

                switch ($code) {
                    case STREAM_NOTIFY_FILE_SIZE_IS:
                        $this->notify($notifier, self::NOTIFY_DOWNLOAD_SIZE, $size);
                        break;

                    case STREAM_NOTIFY_PROGRESS:
                        $this->notify($notifier, self::NOTIFY_DOWNLOAD_PROGRESS, $transferred);
                        break;
                }
            },
        ]);
    }

    /**
     * @param string        $origin
     * @param string        $destination
     * @param string        $release
     * @param string        $version
     * @param string[]      $excludes
     * @param callable|null $notifier
     */
    private function extract($origin, $destination, $release, $version, array $excludes, callable $notifier = null)
    {
        $this->notify($notifier, self::NOTIFY_EXTRACT, $destination);

        $zip = new \ZipArchive();
        $zip->open($origin);

        $this->notify($notifier, self::NOTIFY_EXTRACT_SIZE, $zip->numFiles);

        $offset = 20 + strlen($release) + strlen($version);

        for ($i = 0; $i < $zip->numFiles; ++$i) {
            $this->extractFile(
                $file = $zip->getNameIndex($i),
                substr($file, $offset),
                $origin,
                $destination,
                $excludes,
                $notifier
            );
        }

        $zip->close();

        $this->notify($notifier, self::NOTIFY_EXTRACT_COMPLETE);
        $this->notify($notifier, self::NOTIFY_CLEAR_ARCHIVE, $origin);

        if (!@unlink($origin)) {
            throw $this->createException(sprintf('Unable to remove the CKEditor ZIP archive "%s".', $origin));
        }
    }

    /**
     * @param string        $file
     * @param string        $rewrite
     * @param string        $origin
     * @param string        $destination
     * @param string[]      $excludes
     * @param callable|null $notifier
     */
    private function extractFile($file, $rewrite, $origin, $destination, array $excludes, callable $notifier = null)
    {
        $this->notify($notifier, self::NOTIFY_EXTRACT_PROGRESS, $rewrite);

        $from = 'zip://'.$origin.'#'.$file;
        $to = $destination.'/'.$rewrite;

        foreach ($excludes as $exclude) {
            if (strpos($rewrite, $exclude) === 0) {
                return;
            }
        }

        if (substr($from, -1) === '/') {
            if (!is_dir($to) && !@mkdir($to)) {
                throw $this->createException(sprintf('Unable to create the directory "%s".', $to));
            }

            return;
        }

        if (!@copy($from, $to)) {
            throw $this->createException(sprintf('Unable to extract the file "%s" to "%s".', $file, $to));
        }
    }

    /**
     * @param callable|null $notifier
     * @param string        $type
     * @param mixed         $data
     *
     * @return mixed
     */
    private function notify(callable $notifier = null, $type, $data = null)
    {
        if ($notifier !== null) {
            return $notifier($type, $data);
        }
    }

    /**
     * @param string $message
     *
     * @return \RuntimeException
     */
    private function createException($message)
    {
        $error = error_get_last();

        if (isset($error['message'])) {
            $message .= sprintf(' (%s)', $error['message']);
        }

        return new \RuntimeException($message);
    }
}
