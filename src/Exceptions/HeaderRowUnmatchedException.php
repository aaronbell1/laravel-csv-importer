<?php

namespace Aaronbell1\LaravelCsvImporter\Exceptions;

use Exception;

class HeaderRowUnmatchedException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct("CSV headers and rows did not match up.");
    }
}
