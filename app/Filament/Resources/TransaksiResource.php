<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Transaksi;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Group;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TransaksiResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransaksiResource\RelationManagers;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;

class TransaksiResource extends Resource
{
    protected static ?string $model = Transaksi::class;
    protected static ?string $label = 'Transaksi';
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('transaksi.user_id') // Ganti disabled dengan readOnly
                    ->label('User')
                    // jika bendahara, maka placeholder adalah nama user berdasarkan user_id
                    ->placeholder(fn () => Auth::user()->role === 'bendahara'
                        ? Transaksi::find(request()->route('record'))->user->name
                        : Auth::user()->name)
                    // ->default(fn () => Auth::user()->name)
                    ->disabled(),
                Forms\Components\Hidden::make('user_id')
                    ->default(fn () => Auth::id())
                    ->required(),
                Forms\Components\TextInput::make('nominal')
                    ->required()
                    ->disabled(fn () => Auth::user()->role === 'admin' || Auth::user()->role === 'bendahara')
                    ->mask(RawJs::make("
                        input => {
                            let value = input.replace(/[^0-9]/g, ''); // Remove non-numeric characters
                            return value.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Add '.' as thousands separator
                        }
                    "))
                    ->stripCharacters('.')
                    ->prefix('Rp')
                    ->default(5000),
                Forms\Components\Select::make('status')
                    ->options(fn () => in_array(Auth::user()->role, ['ketua', 'sekretaris'])
                        ? [
                            'pending' => 'Pending',
                            'lunas' => 'Lunas',
                            'belum lunas' => 'Belum Lunas',
                        ]
                        : [
                            'pending' => 'Pending',
                        ])
                    ->default('pending')
                    ->required(),
                Forms\Components\FileUpload::make('bukti_pembayaran')
                    ->image()
                    ->openable()
                    ->resize(30)
                    ->imageEditor()
                    ->directory('bukti_pembayaran')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nominal')
                    // ->numeric()
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\IconColumn::make('bukti_pembayaran')
                    ->icon(fn (Transaksi $record) => $record->bukti_pembayaran ? 'heroicon-o-camera' : '')
                    ->tooltip(fn (Transaksi $record) => $record->bukti_pembayaran)
                    ->url(fn (Transaksi $record) => $record->bukti_pembayaran)
                    ->openUrlInNewTab()
                    ->alignment(Alignment::Center)
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucwords($state))
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'lunas' => 'success',
                        'belum lunas' => 'danger',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //filter per user
                Tables\Filters\SelectFilter::make('user_id')
                    ->options(fn () => \App\Models\User::pluck('name', 'id')->toArray())
                    ->visible(fn () => in_array(Auth::user()->role, ['ketua', 'sekretaris', 'bendahara']))
                    ->label('User'),
                //filter per status
                Tables\Filters\SelectFilter::make('status')
                ->options([
                    'pending' => 'Pending',
                    'lunas' => 'Lunas',
                    'belum lunas' => 'Belum Lunas',
                ])
                ->label('Status'),
                //filter per divisi
                Tables\Filters\SelectFilter::make('divisi_id')
                    ->relationship('user.divisi', 'nama_divisi')
                    ->visible(fn () => in_array(Auth::user()->role, ['ketua', 'sekretaris', 'bendahara']))
                    ->placeholder('Semua Divisi')
                    ->label('Divisi'),
                //filter per created_at
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->placeholder(fn ($state): string => 'Dec 18, ' . now()->subYear()->format('Y')),
                        Forms\Components\DatePicker::make('created_until')
                            ->placeholder(fn ($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Order from ' . Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Order until ' . Carbon::parse($data['created_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Action::make('lunas')
                    ->label('Lunas')
                    ->color('success')
                    ->visible(fn () => in_array(Auth::user()->role, ['bendahara', 'ketua','sekretaris']))
                    ->requiresConfirmation()
                    ->icon('heroicon-o-check-circle')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'lunas',
                        ]);

                        Notification::make()
                            ->title('Status Kas Diubah')
                            ->success()
                            ->send();
                    }),
                Action::make('belum lunas')
                ->label('Belum Lunas')
                ->color('inactive')
                ->visible(fn () => in_array(Auth::user()->role, ['bendahara', 'ketua','sekretaris']))
                ->requiresConfirmation()
                ->icon('heroicon-o-x-circle')
                ->action(function ($record) {
                    $record->update([
                        'status' => 'belum lunas',
                    ]);

                    Notification::make()
                        ->title('Status Kas Diubah')
                        ->success()
                        ->send();
                }),
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListTransaksis::route('/'),
            'create' => Pages\CreateTransaksi::route('/create'),
            'edit' => Pages\EditTransaksi::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if (in_array($user->role, ['ketua', 'sekretaris', 'bendahara'])) {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()->where('user_id', $user->id);
    }
}
