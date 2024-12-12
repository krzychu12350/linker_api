<?php

namespace App\Enums;

use ArchTech\Enums\Values;

enum FileType: string
{
    use Values;

    case IMAGE = 'image';
    case AUDIO = 'audio';
    case VIDEO = 'video';
    case DOCUMENT = 'document';
    case OTHER = 'other';
}
