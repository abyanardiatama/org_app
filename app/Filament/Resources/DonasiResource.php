<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DonasiResource\Pages;
use App\Models\Donasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Support\RawJs;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Support\Facades\Auth;

class DonasiResource extends Resource
{
    protected static ?string $model = Donasi::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $label = 'Donasi';
    protected static ?int $navigationSort = 14;
    protected static ?string $navigationGroup = 'Laporan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\TextInput::make('user_id')
                        ->label('Nama Donatur')
                        ->required(),
                    Forms\Components\Select::make('kegiatan_id')
                        ->label('Nama Kegiatan')
                        // hanya tampilkan kegiatan dengan target_biaya > 0
                        ->options(
                            \App\Models\Kegiatan::where('target_biaya', '>', 0)
                                ->pluck('nama_kegiatan', 'id')
                        )
                        ->native(false)
                        ->required(),
                    Forms\Components\TextInput::make('jumlah_donasi')
                        ->label('Jumlah Donasi')
                        ->required()
                        //add separator
                        ->prefix('Rp ')
                        ->mask(RawJs::make("
                            input => {
                                let value = input.replace(/[^0-9]/g, ''); // Remove non-numeric characters
                                return value.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Add '.' as thousands separator
                            }
                        "))
                        ->minValue(0)
                        ->numeric()
                        ->dehydrateStateUsing(fn ($state) => str_replace('.', '', $state)),
                    Forms\Components\Select::make('metode_pembayaran')
                        ->label('Metode Pembayaran')
                        ->options([
                            'transfer' => 'Transfer',
                            'cash' => 'Cash',
                            'e-wallet' => 'E-Wallet',
                        ])
                        ->required(),
                    Forms\Components\FileUpload::make('bukti_pembayaran')
                        ->label('Bukti Pembayaran')
                        ->image()
                        ->required()
                        ->directory('donasi_bukti_pembayaran')
                        ->preserveFilenames()
                        ->maxSize(1024) // 1MB
                        ->acceptedFileTypes(['image/*', 'application/pdf'])
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Donatur')
                    //show as **** if user is external
                    ->formatStateUsing(fn ($state) => Auth::user()->role === 'external' ? str_repeat('*', mb_strlen($state)) : $state)
                    ->searchable(),
                Tables\Columns\TextColumn::make('kegiatan.nama_kegiatan')
                    ->label('Nama Kegiatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah_donasi')
                    ->label('Jumlah Donasi')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('metode_pembayaran')
                    ->label('Metode Pembayaran')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ], position: ActionsPosition::BeforeColumns)
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

    public function getHeaderWidgetsColumns(): int|string|array
    {
        return 4;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDonasis::route('/'),
            'create' => Pages\CreateDonasi::route('/create'),
            'edit' => Pages\EditDonasi::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        // dd(Auth::user()->id);
        return Donasi::query()
            //for user selain eksternal get all donasi based on divisi_id
            // for user eksternal get all donasi from all divisi
            ->when(Auth::user()->role === 'external', function ($query) {
                // dd(Auth::user()->id, Auth::user()->role);
                //sort by biggest jumlah_donasi
                return $query->orderBy('jumlah_donasi', 'desc');

            })
            ->when(Auth::user()->role !== 'external', function ($query) {
                // return $query->where('divisi_id', Auth::user()->divisi_id);
                return $query;
            });
            
        }
     public static function canCreate(): bool
     {
        if(Auth::check() && Auth::user()->role == "external"){
            return true;
        }
        return false;
     }
}
