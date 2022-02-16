<?php

namespace CustomConcepts\Wics\Console\Command;

use CustomConcepts\Wics\Service\Synchronizer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;

class SynchronizeItemCommand extends Command
{
    /**
     * @var Synchronizer
     */
    private $synchronizer;

    /**
     * @var State
     */
    private $state;

    /**
     * @param Synchronizer $synchronizer
     * @param State $state
     */
    public function __construct(Synchronizer $synchronizer, State $state)
    {
        $this->synchronizer = $synchronizer;
        $this->state = $state;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cc_wics:synchronize')
            ->setDescription('Synchronize WICS stock items command');

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Synchronization is started</info>');
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        $this->synchronizer->synchronize();
        $output->writeln('<info>Synchronization was finished</info>');
    }
}
