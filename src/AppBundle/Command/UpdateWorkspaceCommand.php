<?php
namespace AppBundle\Command;

use AppBundle\Service\UpdateData;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateWorkspaceCommand extends Command
{
    private $updateWorkspaceService;
    public function __construct(UpdateData $update)
    {
        parent::__construct();
        $this->updateWorkspaceService = $update;
    }
    protected function configure()
    {
        $this->setName('app:workspace:update')
            ->setDescription('Update all the workspace  registered in the dabase')
            ->setHelp('This command allows you to update your workspaces');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Update started');
        $result = $this->updateWorkspaceService->update();
        if($result)
        {
            $output->writeln('Done');
        }
        else
        {
            $output->writeln('Fail');
        }
    }
}