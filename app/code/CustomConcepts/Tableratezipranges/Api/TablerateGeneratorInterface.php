<?php
declare(strict_types=1);

namespace CustomConcepts\Tableratezipranges\Api;


interface TablerateGeneratorInterface
{
    public function getRates() : array;
}
