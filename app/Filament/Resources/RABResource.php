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
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;

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
                Section::make('Data RAB')
                    ->schema([
                        Forms\Components\Select::make('divisi')
                            ->disabled(fn () => Auth::user()->role === 'admin' || Auth::user()->role === 'bendahara')
                            ->options(
                                \App\Models\Divisi::pluck('nama_divisi', 'id')
                            )
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                // Reset nama_kegiatan when divisi changes
                                $set('nama_kegiatan', null);
                            })
                            ->required(),
                        Forms\Components\Select::make('nama_kegiatan')
                            ->disabled(fn () => Auth::user()->role === 'admin' || Auth::user()->role === 'bendahara')
                            //get kegiatan from divisi selected
                            ->options(function (callable $get) {
                                $divisiId = $get('divisi');
                                if (!$divisiId) {
                                    return [];
                                }
                                return \App\Models\Kegiatan::where('divisi_id', $divisiId)
                                    ->pluck('nama_kegiatan', 'nama_kegiatan'); // gunakan 'name' untuk nama kegiatan
                            })
                            ->required(),
                        Forms\Components\DatePicker::make('tanggal_kegiatan')
                            ->disabled(fn () => Auth::user()->role === 'admin' || Auth::user()->role === 'bendahara')
                            ->required()
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->placeholder('Pilih Tanggal Kegiatan'),
                        Forms\Components\TextInput::make('jumlah')
                            ->numeric()
                            ->prefix('Rp')
                            ->inputMode('decimal')
                            ->required()
                            ->disabled() // agar tidak bisa diubah manual
                            ->dehydrated(true),
                        Forms\Components\Select::make('status')
                            ->options(fn () => Auth::user()->role === 'phkmi' || Auth::user()->role === 'bendahara'
                                ? [
                                    'sudah diproses' => 'Sudah Diproses',
                                    'belum diproses' => 'Belum Diproses',
                                ]
                                : [
                                    'belum diproses' => 'Belum Diproses',
                                ]
                            )
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Detail RAB Item')
                    ->schema([
                        TableRepeater::make('rab_items')
                            ->relationship('rabItems')
                            ->label('RAB Item')
                            ->schema([
                                Forms\Components\TextInput::make('keterangan')
                                    ->label('Keterangan')
                                    ->required(),
                                Forms\Components\TextInput::make('qty')
                                    ->label('Qty')
                                    ->numeric()
                                    ->required()
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $harga = $get('harga_satuan') ?? 0;
                                        $set('jumlah', $state * $harga);
                                        // Hitung total jumlah dari semua item
                                        $items = $get('../../rab_items') ?? [];
                                        $total = collect($items)->sum('jumlah');
                                        $set('../../jumlah', $total);
                                    }),
                                Forms\Components\TextInput::make('harga_satuan')
                                    ->label('Harga Satuan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $qty = $get('qty') ?? 0;
                                        $harga = $get('harga_satuan') ?? 0;
                                        $set('jumlah', $qty * $harga);
                                        // Hitung total jumlah dari semua item
                                        $items = $get('../../rab_items') ?? [];
                                        $total = collect($items)->sum('jumlah');
                                        $set('../../jumlah', $total);
                                    }),
                                Forms\Components\TextInput::make('jumlah')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->disabled()
                                    ->dehydrated(true),
                            ])
                            ->addActionLabel('Tambah Item')
                            ->defaultItems(1)
                            ->afterStateUpdated(function ($state, $set, $get) {
                                // Hitung total jumlah dari semua item saat repeater diubah (tambah/hapus)
                                $items = $get('rab_items') ?? [];
                                $total = collect($items)->sum('jumlah');
                                $set('jumlah', $total);
                            }),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('divisi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_anggota'
                    //show name based id
                    )->label('Nama Anggota')
                    ->formatStateUsing(fn ($state, RAB $record) => $record->user->name ?? 'Tidak Diketahui')
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
                Tables\Actions\Action::make('sudah_diproses')
                    ->label('Sudah Diproses')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'sudah diproses')
                    ->action(function ($record) {
                        $record->status = 'sudah diproses';
                        $record->save();
                    }),
                Tables\Actions\Action::make('belum_diproses')
                    ->label('Belum Diproses')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status !== 'belum diproses')
                    ->action(function ($record) {
                        $record->status = 'belum diproses';
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
            'index' => Pages\ListRABS::route('/'),
            'create' => Pages\CreateRAB::route('/create'),
            'edit' => Pages\EditRAB::route('/{record}/edit'),
        ];
    }
}
