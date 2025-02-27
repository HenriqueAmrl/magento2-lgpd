<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace HenriqueAmrl\Lgpd\Service\Anonymize\Anonymizer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;
use HenriqueAmrl\Lgpd\Service\Anonymize\AnonymizerInterface;

final class AlphaUpper implements AnonymizerInterface
{
    private const DEFAULT_LENGTH = 5;

    /**
     * @var Random
     */
    private Random $mathRandom;

    private int $length;

    public function __construct(
        Random $mathRandom,
        int $length = self::DEFAULT_LENGTH
    ) {
        $this->mathRandom = $mathRandom;
        $this->length = $length;
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function anonymize($value): ?string
    {
        return $value ? $this->mathRandom->getRandomString($this->length, Random::CHARS_UPPERS) : null;
    }
}
