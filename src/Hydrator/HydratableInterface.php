<?php

namespace AlphaSoft\DataModel\Hydrator;

interface HydratableInterface
{
    /**
     * @param array<string,mixed> $data
     */
    public function hydrate(array $data): void;
}
