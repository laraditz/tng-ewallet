<?php

namespace Laraditz\TngEwallet\Enums;

enum ResultStatus: string
{
    case Success = 'S';
    case Failed = 'F';
    case Unknown = 'U';
    case Accepted = 'A';
}
