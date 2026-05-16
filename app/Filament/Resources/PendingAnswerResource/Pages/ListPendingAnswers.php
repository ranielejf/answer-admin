<?php

namespace App\Filament\Resources\PendingAnswerResource\Pages;

use App\Filament\Resources\PendingAnswerResource;
use Filament\Resources\Pages\ListRecords;

class ListPendingAnswers extends ListRecords
{
    protected static string $resource = PendingAnswerResource::class;
}
