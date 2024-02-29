<?php

namespace App\Service\Importer\Contract;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.importer')]
interface ImporterInterface
{
    public function import(): void;

    public function supports(string $type): bool;
}