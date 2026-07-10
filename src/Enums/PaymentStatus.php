<?php

namespace Laraditz\TngEwallet\Enums;

enum PaymentStatus: string
{
    case Created = 'created';
    case Accepted = 'accepted';
    case Success = 'success';
    case Failed = 'failed';
    case Unknown = 'unknown';
}
