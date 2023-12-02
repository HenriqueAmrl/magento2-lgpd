<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Model\Entity;

/**
 * @api
 */
interface DocumentInterface
{
    public function setData(array $data): void;

    public function addData(string $key, $value): void;

    public function getData(): array;
}
