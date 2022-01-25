<?php

namespace AlphaSoft\DataModel\Hydrator;

/**
 * @package	Data Model
 * @author	F.Michel <fm.rejeb@alpha-soft.fr>
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://www.alpha-soft.fr
 */
interface HydratableInterface
{
    /**
     * @param array<string,mixed> $data
     */
    public function hydrate(array $data): void;
}
