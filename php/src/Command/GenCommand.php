<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Hyperf\ROCGenerator\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperfCommand;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

#[Command]
class GenCommand extends HyperfCommand
{
    protected bool $coroutine = false;

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('gen:roc');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Generate ROC classes.');
        $this->addArgument('protobuf', InputArgument::REQUIRED, 'The protobuf file');
        $this->addOption('output', 'o', InputOption::VALUE_OPTIONAL, 'The output dir.');
        $this->addOption('path', 'i', InputOption::VALUE_OPTIONAL, 'The proto path. (dirname(protobuf file)).');
    }

    public function handle()
    {
        $protobuf = $this->input->getArgument('protobuf');
        $output = $this->input->getOption('output') ?: getcwd();
        $path = $this->input->getOption('path') ?: dirname($protobuf);

        $process = new Process([
            'protoc',
            '--plugin=protoc-gen-roc=' . str_replace('phar://', '', MAIN_PATH),
            '--proto_path=' . $path,
            '--roc_out=' . $output,
            $protobuf,
        ]);

        $process->run(function ($type, $buffer) {
            if (! $this->output->isVerbose() || ! $buffer) {
                return;
            }

            $this->output->writeln($buffer);
        });

        $return = $process->getExitCode();
        $result = $process->getOutput();

        if ($return === 0) {
            $this->output->writeln('<info>PHP classes successfully generate.</info>');

            return $return;
        }

        $this->output->writeln('<error>protoc exited with an error (' . $return . ') when executed with: </error>');
        $this->output->writeln('');
        $this->output->writeln('  ' . $process->getCommandLine());
        $this->output->writeln('');
        $this->output->writeln($result);
        $this->output->writeln('');
        $this->output->writeln($process->getErrorOutput());
        $this->output->writeln('');

        return $return;
    }
}
