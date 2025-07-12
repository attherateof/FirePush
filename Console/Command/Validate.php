<?php
namespace MageStack\FirePush\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use MageStack\FirePush\Api\MessageServiceInterface;
use MageStack\FirePush\Api\MessageInterface;

class Validate extends Command
{
    public function __construct(
        private readonly MessageServiceInterface $messageService,
        private readonly MessageInterface $message
    ) {
        parent::__construct();
    }
    protected function configure()
    {
        $this->setName('firebase:message:validate')
            ->setDescription('Validate firebase message connection.');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info> Validating Cloud messaging connection ... </info>');

        $message = $this->message->setTitle('Validate')->setBody('Sample message to test connection.');

        if ($this->messageService->send($message, 'validate')) {
            $output->writeln('<info> Connection successful ... </info>');
        } else {
            $output->writeln('<error> Connection failed ... </error>');
        }

        return Command::SUCCESS;
    }
}
