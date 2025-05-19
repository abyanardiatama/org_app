<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Presensi;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;

use App\Filament\Resources\PresensiResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PresensiResource\RelationManagers;
use App\Filament\Resources\PresensiResource\Widgets\PresensiOverview;

class PresensiResource extends Resource
{
    protected static ?string $model = Presensi::class;
    protected static ?string $label = 'Presensi';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Profil';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('kegiatan_id')
                    ->required()
                    ->options(\App\Models\Kegiatan::pluck('nama_kegiatan', 'id')),
                Forms\Components\Select::make('user_id')
                    ->required()
                    ->options(\App\Models\User::pluck('name', 'id'))
                    //afterstateupdate total poin
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => (
                        function () use ($state, $set, $get) {
                            //set total poin from latest data user_id 
                            $latestData = \App\Models\Presensi::where('user_id', $state)->latest()->first();
                            $totalSebelumnya = $latestData?->total_poin ?? 0;
                    
                            $set('total_poin', $totalSebelumnya + $get('poin_peran') + $get('poin_kehadiran'));
                        }
                        )()),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'hadir' => 'Hadir',
                        'tidak hadir' => 'Tidak Hadir',
                    ])
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => (
                        function () use ($state, $set, $get) {
                            $poin_kehadiran = match ($state) {
                                'pending' => 0,
                                'tidak_hadir' => 0,
                                'hadir' => 1,
                                default => 0,
                            };
                    
                            $set('poin_kehadiran', $poin_kehadiran);
                            //set total poin from latest data user_id 
                            $latestData = Presensi::where('user_id', $get('user_id'))->latest()->first();
                            $set('total_poin', $latestData?->total_poin + $get('poin_peran') + $poin_kehadiran);
                        }
                    )())
                    ->required(),
                Forms\Components\Select::make('peran')
                    ->options([
                        'peserta' => 'Peserta',
                        'panitia' => 'Panitia',
                        'ketua divisi' => 'Ketua Divisi',
                        'ketua pelaksana' => 'Ketua Pelaksana',
                    ])
                    ->live()
                    ->afterStateUpdated(fn ($state, callable $set, callable $get) => (
                        function () use ($state, $set, $get) {
                            $poin_peran = match ($state) {
                                'peserta' => 1,
                                'panitia' => 2,
                                'ketua divisi' => 3,
                                'ketua pelaksana' => 4,
                                default => 0,
                            };
                    
                            $set('poin_peran', $poin_peran);
                            //set total poin from latest data
                            $latestData = Presensi::where('user_id', $get('user_id'))->latest()->first();
                            $set('total_poin', $latestData?->total_poin + $poin_peran + $get('poin_kehadiran'));
                        }
                    )())
                    ->required(),
                Forms\Components\TextInput::make('poin_peran')
                    ->required()
                    ->default(0),
                Forms\Components\TextInput::make('poin_kehadiran')
                    ->required()
                    ->default(0),
                Forms\Components\TextInput::make('total_poin')
                    ->required()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kegiatan.nama_kegiatan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'hadir' => 'success',
                        'pending' => 'warning',
                        'tidak hadir' => 'danger',
                    })
                    ->searchable(),
                Tables\Columns\SelectColumn::make('peran')
                    ->options([
                        'peserta' => 'Peserta',
                        'panitia' => 'Panitia',
                        'ketua divisi' => 'Ketua Divisi',
                        'ketua pelaksana' => 'Ketua Pelaksana',
                    ])
                    ->searchable()
                    ->afterStateUpdated(function ($record, $state) {
                        // Update poin_peran sesuai peran yang dipilih
                        $poinPeran = match($state) {
                            'ketua pelaksana' => 4,
                            'ketua divisi' => 3,
                            'panitia' => 2,
                            'peserta' => 1,
                            default => 0,
                        };
        
                        // Update record
                        $record->update([
                            'poin_peran' => $poinPeran,
                            'total_poin' => $record->total_poin - $record->poin_peran + $poinPeran,
                        ]);
                    })
                    ->visible(fn () => in_array(Auth::user()->role, ['ketua', 'sekretaris']))
                    ->sortable(),
                Tables\Columns\TextColumn::make('poin_peran')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('poin_kehadiran')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_poin')
                    ->numeric()
                    ->sortable(),
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
                //filter per kegiatan
                Tables\Filters\SelectFilter::make('kegiatan_id')
                    ->options(fn () => \App\Models\Kegiatan::pluck('nama_kegiatan', 'id')->toArray())
                    ->label('Kegiatan'),
                //filter per user
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->options(fn () => \App\Models\User::pluck('name', 'id')->toArray())
                    ->visible(fn () => in_array(Auth::user()->role, ['ketua', 'sekretaris']))
                    ->default(fn () => Auth::user()->role === 'ketua' || Auth::user()->role === 'sekretaris' ? null : Auth::id()),
            ])
            ->actions([
                Action::make('hadir')
                    ->label('Hadir')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => in_array(Auth::user()->role, ['ketua', 'sekretaris']) && $record->status === 'pending')
                    ->icon('heroicon-o-check-circle')
                    ->action(function ($record) {
                        $poinPeran = $record->poin_peran;
                        $poinKehadiran = 1;

                        // Hitung total poin dengan benar
                        $totalPoinBaru = $record->total_poin + $poinPeran + $poinKehadiran;

                        $record->update([
                            'status' => 'hadir',
                            'poin_kehadiran' => $poinKehadiran,
                            'total_poin' => $totalPoinBaru,
                        ]);

                        Notification::make()
                            ->title('Status Presensi Diubah')
                            ->success()
                            ->send();
                    }),
                Action::make('tidak hadir')
                ->label('Tidak Hadir')
                ->color('inactive')
                ->requiresConfirmation()
                // visible for ketua and sekertaris only
                ->visible(fn ($record) => in_array(Auth::user()->role, ['ketua', 'sekretaris']) && $record->status === 'pending')
                ->icon('heroicon-o-x-circle')
                ->action(function ($record) {
                    $record->update([
                        'status' => 'tidak hadir',
                        'poin_kehadiran' => 0, // Tidak dapat poin kehadiran
                        'total_poin' => $record->total_poin - $record->poin_kehadiran, // Kurangi poin kehadiran sebelumnya
                    ]);

                    Notification::make()
                        ->title('Status Presensi Diubah')
                        ->success()
                        ->send();
                }),
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
            'index' => Pages\ListPresensis::route('/'),
            'create' => Pages\CreatePresensi::route('/create'),
            'edit' => Pages\EditPresensi::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if (in_array($user->role, ['ketua', 'sekretaris'])) {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()->where('user_id', $user->id);
    }

    public static function getWidgets(): array
    {
        return [
            PresensiOverview::class,
        ];
    }


    // public static function getNavigationItems(): array
    // {
    //     //make new navigation item for auth presensi
    //     $menuItems = [];

    //     if (!Auth::check()) {
    //         return $menuItems;
    //     }

    //     // Ambil semua presensi for auth user
    //     $userId = Auth::id();

    //     if(Auth::user()->role == 'ketua' | Auth::user()->role == 'sekretaris') {
    //         // Navigation Item 1: Hanya untuk admin
    //         $menuItems[] = NavigationItem::make('Semua Presensi')
    //             ->icon('heroicon-o-rectangle-stack')
    //             //navigation group
    //             ->group('Profil')
    //             ->url('/admin/presensis');
    //     }
    //     // Navigation Item 2: Hanya untuk presensi user yang sedang login
    //     $menuItems[] = NavigationItem::make('Presensi')
    //         ->icon('heroicon-o-rectangle-stack')
    //         ->group('Profil')
    //         //query to filter presensi by user_id
    //         ->url("/admin/presensis?tableFilters[user_id][value]=$userId&tableFilters[user_id][operator]=equals");
    //         // ->url("/admin/presensis?tableFilters[user_id][value]=$userId");

    //     return $menuItems;
    // }

}
