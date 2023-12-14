<?php declare(strict_types=1);

enum ClientTitleEnum: string
{
    case MISTER = 'mr';
    case MISSUS = 'mrs';
    case DOCTOR = 'doctor';
    case PROFESSOR = 'professor';
    case CUSTOM = 'custom';
}
