<?php

namespace App\Enums;

use ArchTech\Enums\Values;

enum FileExtension: string
{
    use Values;

    case JPG = 'jpg';
    case JPEG = 'jpeg';
    case PNG = 'png';
    case PDF = 'pdf';
    case DOC = 'doc';
    case DOCX = 'docx';
    case MP3 = 'mp3';
    case MP4 = 'mp4';
    case WAV = 'wav';
    case OGG = 'ogg';
    case TXT = 'txt';
    case CSV = 'csv';
    case OTHER = 'other';
}
