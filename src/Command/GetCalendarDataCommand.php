<?php
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\CalendarFileFetcher;

#[AsCommand(name: 'api:get-calendar-data')]
class GetCalendarDataCommand extends Command
{

    public function __construct(private CalendarFileFetcher $calendarFileFetcher)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Get parsed data from calendar file.')
            ->setHelp('This command allows you to get parsed data from calendar file...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $calendarData = $this->calendarFileFetcher->getCalendarData();
        foreach ($calendarData as $index => $data) {
            $output->writeln([
                "---",
                "id: {$data['id']}",
                "start: {$data['start']}",
                "end: {$data['end']}",
                "summary: {$data['summary']}",
            ]);
        }

        return Command::SUCCESS;
    }
}
