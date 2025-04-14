<?php

namespace App\Filament\Resources\RABResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\RABResource;
use Filament\Resources\Pages\EditRecord;

class EditRAB extends EditRecord
{
    protected static string $resource = RABResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    public function mutateFormDataBeforeSave(array $data): array
    {
        //if the user that edit is admin or bendahara, change the status to 'sudah diproses'
        if(Auth::user()->role == 'admin' || Auth::user()->role == 'bendahara') {
            $data['status'] = 'sudah diproses';
        }
        return $data;
    }
}
