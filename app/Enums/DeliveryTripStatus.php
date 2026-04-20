<?php

namespace App\Enums;

enum DeliveryTripStatus: string
{
    case Draft = 'draft';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::InProgress => 'En route',
            self::Completed => 'Retourné',
            self::Closed => 'Clôturé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::InProgress => 'blue',
            self::Completed => 'amber',
            self::Closed => 'green',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Draft => 'heroicon-o-pencil-square',
            self::InProgress => 'heroicon-o-truck',
            self::Completed => 'heroicon-o-arrow-uturn-left',
            self::Closed => 'heroicon-o-check-badge',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Draft => $next === self::InProgress,
            self::InProgress => $next === self::Completed,
            self::Completed => $next === self::Closed,
            self::Closed => false,
        };
    }
}
