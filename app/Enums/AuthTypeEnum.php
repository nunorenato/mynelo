<?php

namespace App\Enums;

Enum AuthTypeEnum
{
    case None;
    case BearerToken;
    case oAuth;

    case Key;
}
