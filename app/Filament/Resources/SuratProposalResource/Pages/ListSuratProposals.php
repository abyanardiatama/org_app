<?php

namespace App\Filament\Resources\SuratProposalResource\Pages;

use App\Filament\Resources\SuratProposalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuratProposals extends ListRecords
{
    protected static string $resource = SuratProposalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
