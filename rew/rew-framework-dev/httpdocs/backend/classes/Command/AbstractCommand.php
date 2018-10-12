<?php

namespace REW\Backend\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * @package REW\Backend\Command
 */
abstract class AbstractCommand extends Command
{

    /**
     * @param string $name
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param NULL|string default
     * @param bool $hidden
     * @return string
     */
    protected function getInputArgument($name, InputInterface $input, OutputInterface $output, $default = null, $hidden = false)
    {
        $definition = $this->getDefinition();
        $arg = $definition->getArgument($name);
        if ($value = $input->getArgument($name)) {
            $default = $value;
        }
        $display = $hidden ? str_repeat('*', strlen($default)) : $default;
        $display = is_array($display) ? implode(',', $display) : $display;
        $display = $display ? sprintf('[%s] ', $display) : '';
        $question = new Question(sprintf('<info>%s:</info> %s', $arg->getDescription(), $display), false);
        // Hide input
        if ($hidden) {
            $question->setHidden(true);
            $question->setHiddenFallback(false);
        }
        // Confirm/prompt cli argument
        $helper = $this->getHelper('question');
        return $helper->ask($input, $output, $question) ?: $default;
    }
    /**
     * @param string $name
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param NULL|string default
     * @param bool $hidden
     * @return string
     */
    protected function getInputOption($name, InputInterface $input, OutputInterface $output, $default = null, $hidden = false)
    {
        $definition = $this->getDefinition();
        $opt = $definition->getOption($name);
        if ($value = $input->getOption($name)) {
            $default = $value;
        }
        $display = $hidden ? str_repeat('*', strlen($default)) : $default;
        $display = is_array($display) ? implode(',', $display) : $display;
        $display = $display ? sprintf('[%s] ', $display) : '';
        $question = new Question(sprintf('<info>%s:</info> %s', $opt->getDescription(), $display), false);
        // Hide input
        if ($hidden) {
            $question->setHidden(true);
            $question->setHiddenFallback(false);
        }
        // Confirm/prompt cli option
        $helper = $this->getHelper('question');
        return $helper->ask($input, $output, $question) ?: $default;
    }

    /**
     * @param string $command
     * @param OutputInterface $output
     * @param int $error
     * @throws \Exception
     * @return array
     */
    protected function executeCommand($command, OutputInterface $output, &$error = 0)
    {
        $output->writeLn(sprintf('<comment>[%s]: %s</comment>', date('Y-m-d H:i:s'), $command));
        $data = [];
        exec($command, $data, $error);
        if (!empty($error)) {
            throw new \Exception('Failed to execute command.');
        }
        if ($output->isVeryVerbose()) {
            $output->writeLn(
                $data
            );
        }
        return $data;
    }
}
