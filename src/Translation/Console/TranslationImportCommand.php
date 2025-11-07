<?php

declare(strict_types=1);

namespace Megio\Translation\Console;

use Doctrine\ORM\Exception\ORMException;
use Megio\Helper\Path;
use Megio\Translation\Facade\TranslationImportFacade;
use Megio\Translation\Service\TranslationService;
use Nette\Neon\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

#[AsCommand(
    name: 'translation:import',
    description: 'Import translations from .neon files to database',
)]
class TranslationImportCommand extends Command
{
    public function __construct(
        private readonly TranslationImportFacade $translationImportFacade,
        private readonly TranslationService $translationService,
    ) {
        parent::__construct();
    }

    /**
     * @throws Exception
     * @throws ORMException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $result = $this->translationImportFacade->importFromDirectory(Path::localeDir());
        $this->translationService->invalidateCache();

        $output->writeln(
            sprintf(
                'Formatted: <info>%s imported, %s updated, %s marked as deleted</info>',
                $result['imported'],
                $result['updated'],
                $result['deleted'],
            ),
        );

        return Command::SUCCESS;
    }
}
