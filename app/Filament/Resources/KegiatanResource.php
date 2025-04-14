<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Kegiatan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\KegiatanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KegiatanResource\RelationManagers;

class KegiatanResource extends Resource
{
    protected static ?string $model = Kegiatan::class;
    protected static ?string $label = 'Data Kegiatan';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Kegiatan';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_kegiatan')
                    ->sortable()
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('divisi_id')
                    ->relationship('divisi', 'nama_divisi')
                    ->searchable()
                    ->required(),
                Forms\Components\DatePicker::make('tanggal_kegiatan')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kegiatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('divisi.nama_divisi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_kegiatan')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'nonaktif' => 'inactive',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //filter based on divisi_id
                Tables\Filters\SelectFilter::make('divisi_id')
                    ->relationship('divisi', 'nama_divisi')
                    ->visible(fn () => in_array(Auth::user()->role, ['ketua', 'sekretaris']))
                    ->placeholder('Semua Divisi')
                    ->label('Divisi'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKegiatans::route('/'),
            'create' => Pages\CreateKegiatan::route('/create'),
            'edit' => Pages\EditKegiatan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // hanya ketua dan sekertaris yang bisa melihat semua divisi
        if (Auth::user()->role === 'ketua' || Auth::user()->role === 'sekretaris') {
            return parent::getEloquentQuery();
        } else {
            // selain itu hanya bisa melihat divisi yang ada di divisi_id
            return parent::getEloquentQuery()
                ->where('id', Auth::user()->divisi_id);
        }
    }
}
