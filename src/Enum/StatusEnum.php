<?php

namespace App\Enum;

enum StatusEnum: string
{
    case DONE = 'DONE';
    case ERROR = 'ERROR';
    case NEW = 'NEW';
    case IN_PROGRESS = 'IN_PROGRESS';

}
