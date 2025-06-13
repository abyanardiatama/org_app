<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Kegiatan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\KegiatanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KegiatanResource\RelationManagers;

class KegiatanResource extends Resource
{
    protected static ?string $model = Kegiatan::class;
    protected static ?string $label = 'Proker';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Program Kerja';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\TextInput::make('nama_kegiatan')
                            ->required(),
                        Forms\Components\Textarea::make('deskripsi_kegiatan')
                            ->label('Deskripsi Kegiatan')
                            ->rows(8)
                            ->required(),
                    ])
                    ->columns(1),
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Select::make('divisi_id')
                            ->relationship('divisi', 'nama_divisi')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'aktif' => 'Aktif',
                                'nonaktif' => 'Nonaktif',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('total_biaya')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->mask(\Filament\Support\RawJs::make("
                                input => {
                                    let value = input.replace(/[^0-9]/g, '');
                                    return value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                }
                            "))
                            ->minValue(0)
                            ->dehydrateStateUsing(fn ($state) => str_replace('.', '', $state))
                            ->prefix('Rp')
                            ->helperText('Total biaya terkumpul dari kegiatan ini.'),
                        Forms\Components\TextInput::make('target_biaya')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->mask(\Filament\Support\RawJs::make("
                                input => {
                                    let value = input.replace(/[^0-9]/g, '');
                                    return value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                }
                            "))
                            ->minValue(0)
                            ->dehydrateStateUsing(fn ($state) => str_replace('.', '', $state))
                            ->prefix('Rp')
                            ->helperText('Target biaya yang ingin dicapai dari kegiatan ini.'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kegiatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deskripsi_kegiatan')
                    ->label('Deskripsi')
                    ->limit(80)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('divisi.nama_divisi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'nonaktif' => 'inactive',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('total_biaya')
                    ->money('idr')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('target_biaya')
                    ->money('idr')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('divisi_id')
                    ->relationship('divisi', 'nama_divisi')
                    ->visible(fn () => in_array(Auth::user()->role, ['ketua', 'sekretaris']))
                    ->placeholder('Semua Divisi')
                    ->label('Divisi'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
                Tables\Actions\Action::make('aktifkan')
                    ->label('Aktifkan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => in_array(Auth::user()->role, ['ketua', 'sekretaris']) && $record->status !== 'aktif')
                    ->action(function ($record) {
                        $record->status = 'aktif';
                        $record->save();
                    }),
                Tables\Actions\Action::make('nonaktifkan')
                    ->label('Nonaktifkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => in_array(Auth::user()->role, ['ketua', 'sekretaris']) && $record->status !== 'nonaktif')
                    ->action(function ($record) {
                        $record->status = 'nonaktif';
                        $record->save();
                    }),
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
            // selain itu hanya bisa melihat proker divisi sendiri
            return parent::getEloquentQuery()
                ->where('divisi_id', Auth::user()->divisi_id);
        }
    }
}
