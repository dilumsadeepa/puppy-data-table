<?php

namespace Dilum\PuppyDataTable\Facades;

use Illuminate\Support\Facades\Facade;

class PuppyDataTable extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Dilum\PuppyDataTable\DataTable::class;
    }
}
