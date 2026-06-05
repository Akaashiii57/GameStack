<?php

namespace App\Enum;

enum GameMode: string 
{
    case Solo = 'solo';
    case Multi = 'multi';
    case Coop = 'coop';
    case SoloMulti = 'solo_multi';
}