<?php
namespace Kreets\SuzoriSms\Facade;

use Illuminate\Support\Facades\Facade;

class SuzoriSms extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'suzorisms';
    }
}