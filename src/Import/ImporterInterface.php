<?php

namespace Aa\AkeneoImport\Import;

interface ImporterInterface
{
    public function import(iterable $commands);
}
