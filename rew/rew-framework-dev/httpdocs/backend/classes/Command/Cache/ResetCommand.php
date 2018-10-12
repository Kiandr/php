<?php

namespace REW\Backend\Command\Cache;

use REW\Backend\Command\AbstractCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ResetCommand
 * @package REW\Backend\Command\Cache
 */
class ResetCommand extends AbstractCommand {

    /**
     * Configure "cache:reset" command
     * @return void
     */
    protected function configure () {
        $this->setName('cache:reset')
            ->setDescription('Clear local filesystem cache and memcache server.')
        ;
    }

    /**
     * Execute "cache:reset" command
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute (InputInterface $input, OutputInterface $output) {

        // Write to console
        $output->writeln([
            '===============',
            'Resetting Cache',
            '===============',
        ]);

        // Delete local filesystem cache
        $output->writeLn('<info>Deleting filesystem cache: </info>');
        $command = 'find ~/app/httpdocs/inc/cache -type f -not -name ".*" -delete -print';
        $return = $this->executeCommand($command, $output);
        $output->writeLn($return);

        // Flush local memcache server
        $output->writeLn('<info>Flushing memcache server: </info>');
        $command = 'echo "flush_all" | nc localhost 11211';
        $return = $this->executeCommand($command, $output);
        $output->writeLn($return);

    }

}
