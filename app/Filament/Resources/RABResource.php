<?php

namespace App\Filament\Resources;

use App\Models\RAB;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RABResource\Pages;
use Filament\Forms\Components\TextInput\Mask;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use App\Filament\Resources\RABResource\RelationManagers;

class RABResource extends Resource
{
    protected static ?string $model = RAB::class;
    protected static ?string $label = 'RAB';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('divisi')
                    //cant be edited for admin user or bendahara
                    ->disabled(fn () => Auth::user()->role === 'admin' || Auth::user()->role === 'bendahara')
                    ->required(),
                Forms\Components\TextInput::make('nama_anggota')
                    ->disabled(fn () => Auth::user()->role === 'admin' || Auth::user()->role === 'bendahara')
                    ->required(),
                Forms\Components\TextInput::make('tanggal_kegiatan')
                    ->disabled(fn () => Auth::user()->role === 'admin' || Auth::user()->role === 'bendahara')
                    ->required(),
                Forms\Components\TextInput::make('jumlah')
                    ->numeric()
                    ->prefix('Rp') 
                    // ->mask(RawJs::make('$money($input)'))
                    ->mask(RawJs::make("
                        input => {
                            let value = input.replace(/[^0-9]/g, ''); // Remove non-numeric characters
                            return value.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Add '.' as thousands separator
                        }
                    "))
                    ->stripCharacters('.')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options(fn () => Auth::user()->role === 'admin' || Auth::user()->role === 'bendahara'
                    ? [
                        'sudah diproses' => 'Sudah Diproses',
                        'belum diproses' => 'Belum Diproses',
                    ]
                    : [
                        'belum diproses' => 'Belum Diproses',
                    ]
                    )
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('divisi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_anggota')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_kegiatan')
                    ->searchable()
                    ->date(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->searchable()
                    //rupiah
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucwords($state))
                    ->color(fn (RAB $record) => $record->status === 'sudah diproses' ? 'success' : 'danger'),
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
                //
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
            'index' => Pages\ListRABS::route('/'),
            'create' => Pages\CreateRAB::route('/create'),
            'edit' => Pages\EditRAB::route('/{record}/edit'),
        ];
    }
}
