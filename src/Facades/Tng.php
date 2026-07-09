<?php

namespace Laraditz\TngEwallet\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Laraditz\TngEwallet\Services\AuthorizationService authorization()
 * @method static \Laraditz\TngEwallet\Services\UserService user()
 * @method static \Laraditz\TngEwallet\Services\PaymentService payment()
 * @method static \Laraditz\TngEwallet\Services\RefundService refund()
 * @method static \Laraditz\TngEwallet\Services\MessageService message()
 * @method static \Laraditz\TngEwallet\Client\TngClient client()
 *
 * @see \Laraditz\TngEwallet\TngEwallet
 */
class Tng extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'tng-ewallet';
    }
}
