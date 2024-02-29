<?php

namespace App\Service\Importer;


use App\Service\Importer\Contract\ImporterInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class ImporterManager
{

    public function __construct(
        #[TaggedIterator('app.importer')]
        private readonly iterable $handlers
    )
    {
    }

    public function import(string $type): void
    {
        /** @var ImporterInterface $handler */
        foreach ($this->handlers as $handler) {
            if ($handler->supports($type)) {
                $handler->import();
            }
        }
    }

}