<?php

namespace Laraditz\TngEwallet\Enums;

enum AccessTokenStatus: string
{
    case Active = 'active';
    case Cancelled = 'cancelled';
    case Expired = 'expired';
}
