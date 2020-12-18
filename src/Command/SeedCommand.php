<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @method Application getApplication()
 */
class SeedCommand extends Command
{

    protected static $defaultName = 'app:seed';
    private EntityManagerInterface $em;

    public function __construct(
        string $name = null,
        EntityManagerInterface $em
    )
    {
        parent::__construct($name);
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('hautelook:fixtures:load');
        $return = $command->run($input, $output);

        if (0 !== $return) {
            return $return;
        }

        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

        return 0;
    }

}
