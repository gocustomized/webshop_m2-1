<?php

namespace CustomConcepts\Wics\Console\Command;

use CustomConcepts\Wics\Model\SyncManagement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearOldSyncCommand extends Command
{
    const NAME_ARGUMENT = 'number_of_records';

    /**
     * @var SyncManagement
     */
    private $syncManager;

    /**
     * @param SyncManagement $syncManager
     */
    public function __construct(SyncManagement $syncManager)
    {
        $this->syncManager = $syncManager;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cc_wics:clear_old_syncs')
            ->setDescription('Cleared old sync records from DB command')
            ->setDefinition([
                new InputArgument(
                    self::NAME_ARGUMENT,
                    InputArgument::OPTIONAL,
                    'Number of Sync records to remove'
                )
            ]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Clearing is started</info>');
        $this->syncManager->clean($input->getArgument(self::NAME_ARGUMENT));
        $output->writeln('<info>Clearing was finished</info>');
    }
}
