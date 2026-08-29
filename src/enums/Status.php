<?php

namespace App\enums;

enum Status: string
{
    case notStarted = 'notStarted';
    case inProgress = 'inProgress';
    case done = 'done';
}
