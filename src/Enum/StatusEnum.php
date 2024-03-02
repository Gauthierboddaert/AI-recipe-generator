<?php

namespace App\Enum;

enum StatusEnum: string
{

    case DONE = 'DONE';
    /** This state is use when ingredient are set and the recipe is ready for cook */
    case NEW = 'NEW';
    /** This state is use when ingredient return by openAI but the ingredient name doesn't exist */
    case TO_REVIEW = 'TO_REVIEW';
    /** This state is use when ingredient are set by openAI but need an second check fo be sur the ingredient set or missed */
    case NEED_VALIDATION = 'NEED_VALIDATION';

}
