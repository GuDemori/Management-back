<?php

namespace App\Enums;

enum OrderStatus: string
{
    case EmEspera   = 'Em espera';
    case Preparando = 'Preparando';
    case ACaminho   = 'À caminho';
    case Entregue   = 'Entregue';
    case Pago       = 'Pago';
    case Cancelado  = 'Cancelado';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Entregue, self::Cancelado, self::Pago]);
    }
}