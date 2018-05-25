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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class CKEditorInstallerCommand extends Command
{
    /**
     * @var CKEditorInstaller
     */
    private $installer;

    /**
     * @param CKEditorInstaller|null $installer
     */
    public function __construct(CKEditorInstaller $installer = null)
    {
        parent::__construct();

        $this->installer = $installer ?: new CKEditorInstaller();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ckeditor:install')
            ->setDescription('Install CKEditor')
            ->addArgument('path', InputArgument::OPTIONAL, 'Where to install CKEditor')
            ->addOption(
                'release',
                null,
                InputOption::VALUE_OPTIONAL,
                'CKEditor release (basic, standard or full)'
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

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->title($output);

        $success = $this->installer->install($this->createOptions($input, $output));

        if ($success) {
            $this->success('CKEditor has been successfully installed...', $output);
        } else {
            $this->info('CKEditor installation has been skipped...', $output);
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return mixed[]
     */
    private function createOptions(InputInterface $input, OutputInterface $output)
    {
        $options = ['notifier' => $this->createNotifier($input, $output)];

        if ($input->hasArgument('path')) {
            $options['path'] = $input->getArgument('path');
        }

        if ($input->hasOption('release')) {
            $options['release'] = $input->getOption('release');
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

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return \Closure
     */
    private function createNotifier(InputInterface $input, OutputInterface $output)
    {
        $clear = new ProgressBar($output);
        $download = new ProgressBar($output);
        $extract = new ProgressBar($output);

        return function ($type, $data) use ($input, $output, $clear, $download, $extract) {
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
                    $this->finishProgressBar($clear, $output);

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
                    $this->finishProgressBar($download, $output);

                    break;

                case CKEditorInstaller::NOTIFY_DOWNLOAD_PROGRESS:
                    $download->advance($data);

                    break;

                case CKEditorInstaller::NOTIFY_DOWNLOAD_SIZE:
                    $download->start($data);

                    break;

                case CKEditorInstaller::NOTIFY_EXTRACT:
                    $this->comment(sprintf('Extracting CKEditor ZIP archive to "%s"', $data), $output);

                    break;

                case CKEditorInstaller::NOTIFY_EXTRACT_COMPLETE:
                    $this->finishProgressBar($extract, $output);

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

    /**
     * @param OutputInterface $output
     */
    private function title(OutputInterface $output)
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

    /**
     * @param string|string[] $message
     * @param OutputInterface $output
     */
    private function comment($message, OutputInterface $output)
    {
        $output->writeln(' // '.$message);
        $output->writeln('');
    }

    /**
     * @param string          $message
     * @param OutputInterface $output
     */
    private function success($message, OutputInterface $output)
    {
        $this->block('[OK] - '.$message, $output, 'green', 'black');
    }

    /**
     * @param string          $message
     * @param OutputInterface $output
     */
    private function info($message, OutputInterface $output)
    {
        $this->block('[INFO] - '.$message, $output, 'yellow', 'black');
    }

    /**
     * @param string          $message
     * @param OutputInterface $output
     * @param string          $background
     * @param string          $font
     */
    private function block($message, OutputInterface $output, $background = null, $font = null)
    {
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
     * @param string|string[] $question
     * @param string[]        $choices
     * @param string          $default
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return string|null
     */
    private function choice($question, array $choices, $default, InputInterface $input, OutputInterface $output)
    {
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

    /**
     * @param ProgressBar     $progress
     * @param OutputInterface $output
     */
    private function finishProgressBar($progress, OutputInterface $output)
    {
        $progress->finish();
        $output->writeln(['', '']);
    }
}
