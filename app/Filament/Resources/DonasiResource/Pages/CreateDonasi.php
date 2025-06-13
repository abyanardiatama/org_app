<?php

namespace App\Filament\Resources\DonasiResource\Pages;

use App\Filament\Resources\DonasiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateDonasi extends CreateRecord
{
    protected static string $resource = DonasiResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        // Set the user_id to the authenticated user's ID
        $data['user_id'] = Auth::id();
        return $data;
    }
}
