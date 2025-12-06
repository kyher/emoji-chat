<?php

namespace App\Enum;

enum WorkspaceUserRole: string
{
    case Administrator = 'administrator';
    case Member = 'member';
}
