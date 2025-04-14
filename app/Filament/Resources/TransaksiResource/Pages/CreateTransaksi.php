<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\TransaksiResource;

class CreateTransaksi extends CreateRecord
{
    protected static string $resource = TransaksiResource::class;

    // public function mutateFormDataBeforeCreate(array $data): array
    // {
    //     // Pastikan user_id selalu diisi dengan ID pengguna yang sedang login
        
    //     $data['user_id'] = Auth::id();

    //     return $data;
    // }
}
