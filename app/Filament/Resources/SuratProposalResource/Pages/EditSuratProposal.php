<?php

namespace App\Filament\Resources\SuratProposalResource\Pages;

use App\Filament\Resources\SuratProposalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuratProposal extends EditRecord
{
    protected static string $resource = SuratProposalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
