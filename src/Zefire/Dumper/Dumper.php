<?php

namespace Zefire\Dumper;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Zefire\Dumper\HtmlDumper;

class Dumper
{
    /**
     * Dumps a value for debug purposes.
     *
     * @return void
     */
    public function dump($value)
    {
        $dumper = 'cli' === PHP_SAPI ? new CliDumper : new HtmlDumper;
        $dumper->dump((new VarCloner)->cloneVar($value));
    }
}