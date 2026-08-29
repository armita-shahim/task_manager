<?php

namespace App\enums;

enum Status: string
{
    case notStarted = 'not_started';
    case inProgress = 'in_progress';
    case done = 'done';
}
