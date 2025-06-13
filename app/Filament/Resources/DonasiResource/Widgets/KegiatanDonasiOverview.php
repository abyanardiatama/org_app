<?php

namespace App\Filament\Resources\DonasiResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use App\Models\Donasi;
use App\Models\Kegiatan;

class KegiatanDonasiOverview extends BaseWidget
{
    protected static ?int $maxItems = 2; // Atur column span agar bisa align 1 baris
     protected int | string | array $columnSpan = 2;
    public function table(Table $table): Table
    {
        return $table
            ->description('Kegiatan yang Didukung')
            ->emptyStateHeading('Kamu belum berdonasi')
            ->emptyStateDescription('Ayo berdonasi untuk kegiatan yang kamu dukung!')
            ->query(
                Kegiatan::query()
                    ->whereIn('id', function ($query) {
                        $query->select('kegiatan_id')
                            ->from('donasis')
                            ->where('user_id', Auth::id());
                    })
                    ->limit(static::$maxItems))
            ->columns([
                Tables\Columns\TextColumn::make('nama_kegiatan')
                    ->label('Nama Kegiatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('target_biaya')
                    ->label('Target Donasi')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.')),
            ],2);
    }
    protected function getColumns(): int
    {
        return 1;
    }
}
