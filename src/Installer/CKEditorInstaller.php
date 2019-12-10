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

namespace FOS\CKEditorBundle\Installer;

use FOS\CKEditorBundle\Exception\BadProxyUrlException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
final class CKEditorInstaller
{
    const RELEASE_BASIC = 'basic';

    const RELEASE_FULL = 'full';

    const RELEASE_STANDARD = 'standard';

    const RELEASE_CUSTOM = 'custom';

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
    private static $customBuildArchive = 'https://ckeditor.com/cke4/builder/download/%s';

    /**
     * @var OptionsResolver
     */
    private $resolver;

    public function __construct(array $options = [])
    {
        $this->resolver = (new OptionsResolver())
            ->setDefaults(array_merge([
                'clear' => null,
                'excludes' => ['samples'],
                'notifier' => null,
                'path' => dirname(__DIR__).'/Resources/public',
                'release' => self::RELEASE_FULL,
                'custom_build_id' => null,
                'version' => self::VERSION_LATEST,
            ], $options))
            ->setAllowedTypes('excludes', 'array')
            ->setAllowedTypes('notifier', ['null', 'callable'])
            ->setAllowedTypes('path', 'string')
            ->setAllowedTypes('custom_build_id', ['null', 'string'])
            ->setAllowedTypes('version', 'string')
            ->setAllowedValues('clear', [self::CLEAR_DROP, self::CLEAR_KEEP, self::CLEAR_SKIP, null])
            ->setAllowedValues('release', [self::RELEASE_BASIC, self::RELEASE_FULL, self::RELEASE_STANDARD, self::RELEASE_CUSTOM])
            ->setNormalizer('path', function (Options $options, $path) {
                return rtrim($path, '/');
            });
    }

    public function install(array $options = []): bool
    {
        $options = $this->resolver->resolve($options);

        if (self::CLEAR_SKIP === $this->clear($options)) {
            return false;
        }

        $this->extract($this->download($options), $options);

        return true;
    }

    private function clear(array $options): string
    {
        if (!file_exists($options['path'].'/ckeditor.js')) {
            return self::CLEAR_DROP;
        }

        if (null === $options['clear'] && null !== $options['notifier']) {
            $options['clear'] = $this->notify($options['notifier'], self::NOTIFY_CLEAR, $options['path']);
        }

        if (null === $options['clear']) {
            $options['clear'] = self::CLEAR_SKIP;
        }

        if (self::CLEAR_DROP === $options['clear']) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($options['path'], \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );

            $this->notify($options['notifier'], self::NOTIFY_CLEAR_SIZE, iterator_count($files));

            foreach ($files as $file) {
                $filePath = $file->getRealPath();
                $this->notify($options['notifier'], self::NOTIFY_CLEAR_PROGRESS, $filePath);

                if ($dir = $file->isDir()) {
                    $success = @rmdir($filePath);
                } else {
                    $success = @unlink($filePath);
                }

                if (!$success) {
                    throw $this->createException(sprintf('Unable to remove the %s "%s".', $dir ? 'directory' : 'file', $filePath));
                }
            }

            $this->notify($options['notifier'], self::NOTIFY_CLEAR_COMPLETE);
        }

        return $options['clear'];
    }

    private function download(array $options): string
    {
        $url = $this->getDownloadUrl($options);
        $this->notify($options['notifier'], self::NOTIFY_DOWNLOAD, $url);

        $zip = @file_get_contents($url, false, $this->createStreamContext($options['notifier']));

        if (false === $zip) {
            throw $this->createException(sprintf('Unable to download CKEditor ZIP archive from "%s".', $url));
        }

        $path = (string) tempnam(sys_get_temp_dir(), 'ckeditor-'.$options['release'].'-'.$options['version'].'.zip');

        if (!@file_put_contents($path, $zip)) {
            throw $this->createException(sprintf('Unable to write CKEditor ZIP archive to "%s".', $path));
        }

        $this->notify($options['notifier'], self::NOTIFY_DOWNLOAD_COMPLETE, $path);

        return $path;
    }

    private function getDownloadUrl(array $options): string
    {
        if (self::RELEASE_CUSTOM !== $options['release']) {
            return sprintf(self::$archive, $options['release'], $options['version']);
        }

        if (null === $options['custom_build_id']) {
            throw $this->createException('Unable to download CKEditor ZIP archive. Custom build ID is not specified.');
        }

        if (self::VERSION_LATEST !== $options['version']) {
            throw $this->createException('Unable to download CKEditor ZIP archive. Specifying version for custom build is not supported.');
        }

        return sprintf(self::$customBuildArchive, $options['custom_build_id']);
    }

    /**
     * @return resource
     */
    private function createStreamContext(callable $notifier = null)
    {
        $context = [];
        $proxy = getenv('https_proxy') ?: getenv('http_proxy');

        if ($proxy) {
            $proxyUrl = parse_url($proxy);

            if (false === $proxyUrl || !isset($proxyUrl['host']) || !isset($proxyUrl['port'])) {
                throw BadProxyUrlException::fromEnvUrl($proxy);
            }

            $context['http'] = [
                'proxy' => 'tcp://'.$proxyUrl['host'].':'.$proxyUrl['port'],
                'request_fulluri' => (bool) getenv('https_proxy_request_fulluri') ?:
                    getenv('http_proxy_request_fulluri'),
            ];
        }

        return stream_context_create($context, [
            'notification' => function (
                $code,
                $severity,
                $message,
                $messageCode,
                $transferred,
                $size
            ) use ($notifier) {
                if (null === $notifier) {
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

    private function extract(string $path, array $options): void
    {
        $this->notify($options['notifier'], self::NOTIFY_EXTRACT, $options['path']);

        $zip = new \ZipArchive();
        $zip->open($path);

        $this->notify($options['notifier'], self::NOTIFY_EXTRACT_SIZE, $zip->numFiles);

        if (self::RELEASE_CUSTOM === $options['release']) {
            $offset = 9;
        } else {
            $offset = 20 + strlen($options['release']) + strlen($options['version']);
        }

        for ($i = 0; $i < $zip->numFiles; ++$i) {
            $filename = $zip->getNameIndex($i);
            $isDirectory = ('/' === substr($filename, -1, 1));

            if (!$isDirectory) {
                $this->extractFile($filename, substr($filename, $offset), $path, $options);
            }
        }

        $zip->close();

        $this->notify($options['notifier'], self::NOTIFY_EXTRACT_COMPLETE);
        $this->notify($options['notifier'], self::NOTIFY_CLEAR_ARCHIVE, $path);

        if (!@unlink($path)) {
            throw $this->createException(sprintf('Unable to remove the CKEditor ZIP archive "%s".', $path));
        }
    }

    private function extractFile(string $file, string $rewrite, string $origin, array $options): void
    {
        $this->notify($options['notifier'], self::NOTIFY_EXTRACT_PROGRESS, $rewrite);

        $from = 'zip://'.$origin.'#'.$file;
        $to = $options['path'].'/'.$rewrite;

        foreach ($options['excludes'] as $exclude) {
            if (0 === strpos(ltrim($rewrite, '\\/'), $exclude)) {
                return;
            }
        }

        $targetDirectory = dirname($to);
        if (!is_dir($targetDirectory) && !@mkdir($targetDirectory, 0777, true)) {
            throw $this->createException(sprintf('Unable to create the directory "%s".', $targetDirectory));
        }

        if (!@copy($from, $to)) {
            throw $this->createException(sprintf('Unable to extract the file "%s" to "%s".', $file, $to));
        }
    }

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    private function notify(callable $notifier = null, string $type, $data = null)
    {
        if (null !== $notifier) {
            return $notifier($type, $data);
        }
    }

    private function createException(string $message): \RuntimeException
    {
        $error = error_get_last();

        if (isset($error['message'])) {
            $message .= sprintf(' (%s)', $error['message']);
        }

        return new \RuntimeException($message);
    }
}
