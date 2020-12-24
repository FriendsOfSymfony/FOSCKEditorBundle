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

namespace FOS\CKEditorBundle\Command;

use FOS\CKEditorBundle\Installer\CKEditorInstaller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
final class CKEditorInstallerCommand extends Command
{
    /**
     * @var CKEditorInstaller
     */
    private $installer;

    public function __construct(CKEditorInstaller $installer)
    {
        parent::__construct();

        $this->installer = $installer;
    }

    protected function configure(): void
    {
        $this
            ->setName('ckeditor:install')
            ->setDescription('Install CKEditor')
            ->addArgument('path', InputArgument::OPTIONAL, 'Where to install CKEditor')
            ->addOption(
                'release',
                null,
                InputOption::VALUE_OPTIONAL,
                'CKEditor release (basic, standard, full or custom)'
            )
            ->addOption(
                'custom-build-id',
                null,
                InputOption::VALUE_OPTIONAL,
                'CKEditor custom build ID'
            )
            ->addOption('tag', null, InputOption::VALUE_OPTIONAL, 'CKEditor tag (x.y.z or latest)')
            ->addOption(
                'clear',
                null,
                InputOption::VALUE_OPTIONAL,
                'How to clear previous CKEditor installation (drop, keep or skip)'
            )
            ->addOption(
                'exclude',
                null,
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'Path to exclude when extracting CKEditor'
            )
            ->addOption(
                'no-progress-bar',
                'nobar',
                InputOption::VALUE_NONE,
                'Hide the progress bars?'
            )
            ->setHelp(
                <<<'EOF'
The <info>%command.name%</info> command install CKEditor in your application:

  <info>php %command.full_name%</info>
  
You can install it at a specific path (absolute):

  <info>php %command.full_name% path</info>
  
You can install a specific release (basic, standard or full):

  <info>php %command.full_name% --release=full</info>
  
You can install a specific version:

  <info>php %command.full_name% --tag=4.7.0</info>

You can install custom build generated on https://ckeditor.com/cke4/builder:

  <info>php %command.full_name% --release=custom --custom-build-id=574a82a0d3e9226d94b0e91d10eaa372</info>

If there is a previous CKEditor installation detected, 
you can control how it should be handled in non-interactive mode:

  <info>php %command.full_name% --clear=drop</info>
  <info>php %command.full_name% --clear=keep</info>
  <info>php %command.full_name% --clear=skip</info>
  
You can exclude path(s) when extracting CKEditor:

  <info>php %command.full_name% --exclude=samples --exclude=adapters</info>
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->title($output);

        $success = $this->installer->install($this->createOptions($input, $output));

        if ($success) {
            $this->success('CKEditor has been successfully installed...', $output);
        } else {
            $this->info('CKEditor installation has been skipped...', $output);
        }

        return 0;
    }

    private function createOptions(InputInterface $input, OutputInterface $output): array
    {
        $options = ['notifier' => $this->createNotifier($input, $output)];

        if ($input->hasArgument('path')) {
            $options['path'] = $input->getArgument('path');
        }

        if ($input->hasOption('release')) {
            $options['release'] = $input->getOption('release');
        }

        if ($input->hasOption('custom-build-id')) {
            $options['custom_build_id'] = $input->getOption('custom-build-id');
        }

        if ($input->hasOption('tag')) {
            $options['version'] = $input->getOption('tag');
        }

        if ($input->hasOption('exclude')) {
            $options['excludes'] = $input->getOption('exclude');
        }

        if ($input->hasOption('clear')) {
            $options['clear'] = $input->getOption('clear');
        }

        return array_filter($options);
    }

    private function createNotifier(InputInterface $input, OutputInterface $output): \Closure
    {
        $barOutput = $input->getOption('no-progress-bar') ? new NullOutput() : $output;

        $clear = new ProgressBar($barOutput);
        $download = new ProgressBar($barOutput);
        $extract = new ProgressBar($barOutput);

        return function ($type, $data) use ($input, $output, $barOutput, $clear, $download, $extract) {
            switch ($type) {
                case CKEditorInstaller::NOTIFY_CLEAR:
                    $result = $this->choice(
                        [
                            sprintf('CKEditor is already installed in "%s"...', $data),
                            '',
                            'What do you want to do?',
                        ],
                        $choices = [
                            CKEditorInstaller::CLEAR_DROP => 'Drop the directory & reinstall CKEditor',
                            CKEditorInstaller::CLEAR_KEEP => 'Keep the directory & reinstall CKEditor by overriding files',
                            CKEditorInstaller::CLEAR_SKIP => 'Skip installation',
                        ],
                        CKEditorInstaller::CLEAR_DROP,
                        $input,
                        $output
                    );

                    if (false !== ($key = array_search($result, $choices, true))) {
                        $result = $key;
                    }

                    if (CKEditorInstaller::CLEAR_DROP === $result) {
                        $this->comment(sprintf('Dropping CKEditor from "%s"', $data), $output);
                    }

                    return $result;

                case CKEditorInstaller::NOTIFY_CLEAR_ARCHIVE:
                    $this->comment(sprintf('Dropping CKEditor ZIP archive "%s"', $data), $output);

                    break;

                case CKEditorInstaller::NOTIFY_CLEAR_COMPLETE:
                    $this->finishProgressBar($clear, $barOutput);

                    break;

                case CKEditorInstaller::NOTIFY_CLEAR_PROGRESS:
                    $clear->advance();

                    break;

                case CKEditorInstaller::NOTIFY_CLEAR_SIZE:
                    $clear->start($data);

                    break;

                case CKEditorInstaller::NOTIFY_DOWNLOAD:
                    $this->comment(sprintf('Downloading CKEditor ZIP archive from "%s"', $data), $output);

                    break;

                case CKEditorInstaller::NOTIFY_DOWNLOAD_COMPLETE:
                    $this->finishProgressBar($download, $barOutput);

                    break;

                case CKEditorInstaller::NOTIFY_DOWNLOAD_PROGRESS:
                    $download->setProgress($data);

                    break;

                case CKEditorInstaller::NOTIFY_DOWNLOAD_SIZE:
                    $download->start($data);

                    break;

                case CKEditorInstaller::NOTIFY_EXTRACT:
                    $this->comment(sprintf('Extracting CKEditor ZIP archive to "%s"', $data), $output);

                    break;

                case CKEditorInstaller::NOTIFY_EXTRACT_COMPLETE:
                    $this->finishProgressBar($extract, $barOutput);

                    break;

                case CKEditorInstaller::NOTIFY_EXTRACT_PROGRESS:
                    $extract->advance();

                    break;

                case CKEditorInstaller::NOTIFY_EXTRACT_SIZE:
                    $extract->start($data);

                    break;
            }
        };
    }

    private function title(OutputInterface $output): void
    {
        $output->writeln(
            [
                '----------------------',
                '| CKEditor Installer |',
                '----------------------',
                '',
            ]
        );
    }

    private function comment(string $message, OutputInterface $output): void
    {
        $output->writeln(' // '.$message);
        $output->writeln('');
    }

    private function success(string $message, OutputInterface $output): void
    {
        $this->block('[OK] - '.$message, $output, 'green', 'black');
    }

    private function info(string $message, OutputInterface $output): void
    {
        $this->block('[INFO] - '.$message, $output, 'yellow', 'black');
    }

    private function block(
        string $message,
        OutputInterface $output,
        string $background = null,
        string $font = null
    ): void {
        $options = [];

        if (null !== $background) {
            $options[] = 'bg='.$background;
        }

        if (null !== $font) {
            $options[] = 'fg='.$font;
        }

        $pattern = ' %s ';

        if (!empty($options)) {
            $pattern = '<'.implode(';', $options).'>'.$pattern.'</>';
        }

        $output->writeln($block = sprintf($pattern, str_repeat(' ', strlen($message))));
        $output->writeln(sprintf($pattern, $message));
        $output->writeln($block);
    }

    /**
     * @param string[] $question
     * @param string[] $choices
     */
    private function choice(
        array $question,
        array $choices,
        string $default,
        InputInterface $input,
        OutputInterface $output
    ): ?string {
        $helper = new QuestionHelper();

        if (is_array($question)) {
            $question = implode("\n", $question);
        }

        $result = $helper->ask(
            $input,
            $output,
            new ChoiceQuestion($question, $choices, $default)
        );

        $output->writeln('');

        return $result;
    }

    private function finishProgressBar(ProgressBar $progress, OutputInterface $output): void
    {
        $progress->finish();
        $output->writeln(['', '']);
    }
}
