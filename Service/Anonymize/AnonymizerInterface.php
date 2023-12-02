<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Service\Anonymize;

/**
 * @api
 */
interface AnonymizerInterface
{
    /**
     * Anonymize the value
     *
     * @param mixed $value
     * @return mixed
     */
    public function anonymize($value);
}
