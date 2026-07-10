<?php

namespace Laraditz\TngEwallet\Enums;

enum RefundStatus: string
{
    case Processing = 'PROCESSING';
    case Success = 'SUCCESS';
    case Fail = 'FAIL';
}
