<?php

declare(strict_types=1);

namespace LaravelTable\Core\Enums;

use LaravelTable\Core\Support\EnumUtilities;

enum FilterOperator: string
{
    use EnumUtilities;

    case EQ = '=';
    case NEQ = '!=';
    case GT = '>';
    case GTE = '>=';
    case LT = '<';
    case LTE = '<=';
    case LIKE = 'like';
    case IN = 'in';
    case NOT_IN = 'not_in';
    case BETWEEN = 'between';

}
