<?php

namespace App\Enum;

enum GameStatus: string
{
    case Souhaite = 'souhaite';
    case AJouer = 'a_jouer';
    case EnCours = 'en_cours';
    case EnPause = 'en_pause';
    case Termine = 'termine';
    case Platine = 'platine';
    case Abandonne = 'abandonne';
}