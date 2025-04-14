<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Section;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Password;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $label = 'Data User';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Profil';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('User Detail')->schema([
                    Forms\Components\TextInput::make('name')
                        ->required(),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->rule('regex:/^(?!.*@example\.com$).*$/')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('password')
                        ->password()
                        ->required()
                        ->revealable()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->visible(fn ($livewire) => $livewire instanceof CreateUser)
                        ->rule(Password::default())
                        ->maxLength(255),
                    Forms\Components\Select::make('role')
                        ->options([
                            'admin' => 'Admin',
                            'bendahara' => 'Bendahara',
                            'sekretaris' => 'Sekretaris',
                            'ketua' => 'Ketua',
                            'anggota' => 'Anggota',
                            'external' => 'External',
                            'bsomtq' => 'BSOMTQ',
                            'phkmi' => 'PHKMI',
                        ]),
                    Forms\Components\FileUpload::make('avatar_url')
                        ->image()
                        ->avatar()
                        ->openable()
                        ->resize(50)
                        ->imageCropAspectRatio('1:1')
                        ->imageResizeMode('cover')
                        ->directory('avatars')
                        ->imageEditor(),
                ]),

                Section::make('User New Password')->schema([
                    Forms\Components\TextInput::make('new_password')
                        ->password()
                        ->nullable()
                        ->revealable()
                        ->rule(Password::default())
                        ->maxLength(255),
                    Forms\Components\TextInput::make('new_password_confirmation')
                        ->password()
                        ->revealable()
                        ->same('new_password')
                        ->rule(Password::default())
                        ->requiredWith('new_password')
                        ->maxLength(255),
                ])->visible(fn ($livewire) => $livewire instanceof EditUser),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('divisi.nama_divisi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
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
                // filter user by role
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'bendahara' => 'Bendahara',
                        'sekretaris' => 'Sekretaris',
                        'ketua' => 'Ketua',
                        'anggota' => 'Anggota',
                        'external' => 'External',
                        'bsomtq' => 'BSOMTQ',
                        'phkmi' => 'PHKMI',
                    ]),
                // filter user by divisi
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // hanya ketua dan sekertaris yang bisa melihat semua user
        if (Auth::user()->role === 'ketua' || Auth::user()->role === 'sekretaris') {
            return parent::getEloquentQuery();
        }

        // selain itu hanya bisa melihat user yang ada di divisi nya
        return parent::getEloquentQuery()
            ->where('divisi_id', Auth::user()->divisi_id);
    }
}
