<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Divisi;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Resources\DivisiResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DivisiResource\RelationManagers;

class DivisiResource extends Resource
{
    protected static ?string $model = Divisi::class;
    protected static ?string $navigationGroup = 'Profil';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_divisi')
                    ->required(),
                Forms\Components\FileUpload::make('image')
                    ->label('Gambar')
                    ->image()
                    ->preserveFilenames()
                    ->directory('divisi')
                    ->columnSpan(2)
                    ->required(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
               TextEntry::make('nama_divisi')
                ->label('Nama Divisi')
                ->weight(FontWeight::Bold)
                ->color('primary'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\Layout\Stack::make([
                Tables\Columns\ImageColumn::make('image')
                    ->height('100%')
                    ->width('100%'),
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('nama_divisi')
                        ->weight(FontWeight::Bold),
                ]),
            ])->space(5),
            //jumlah kegiatan
            Tables\Columns\TextColumn::make('kegiatan_count')
                ->label('Jumlah Kegiatan')
                ->weight(FontWeight::Thin)
                ->getStateUsing(fn ($record) => $record->kegiatan()->count() . ' Proker' . ' | '. $record->users()->count() . ' Anggota'),
        ])
        // ->reorderable('sort')
        ->filters([
            //
        ])
        ->contentGrid([
            'md' => 2,
            'xl' => 3,
        ])
        ->actions([
            //make action button redirect to /admin/users with query string divisi_id
            Tables\Actions\Action::make('Lihat Anggota')
                ->label('Anggota')
                ->visible(fn () => in_array(Auth::user()->role, ['ketua', 'sekretaris']))
                ->icon('heroicon-o-user-group')
                ->url(fn ($record) => "/admin/users?tableFilters[divisi_id][value]={$record->id}&tableFilters[divisi_id][operator]=equals"),
            //lihat kegiatan
            Tables\Actions\Action::make('Lihat Proker')
            ->label('Proker')
                ->url(fn ($record) => "/admin/kegiatans?tableFilters[divisi_id][value]={$record->id}&tableFilters[divisi_id][operator]=equals")
                ->visible(fn () => in_array(Auth::user()->role, ['ketua', 'sekretaris']))
                ->icon('heroicon-o-eye'),
            ActionGroup::make([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]),

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
            'index' => Pages\ListDivisis::route('/'),
            'create' => Pages\CreateDivisi::route('/create'),
            'edit' => Pages\EditDivisi::route('/{record}/edit'),
        ];
    }

    //canCreate
    public static function canCreate(): bool
    {
        return true;
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
