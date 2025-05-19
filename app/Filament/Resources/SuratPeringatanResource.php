<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SuratPeringatan;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\ActionGroup;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SuratPeringatanResource\Pages;
use App\Filament\Resources\SuratPeringatanResource\RelationManagers;
use Carbon\Carbon;

class SuratPeringatanResource extends Resource
{
    protected static ?string $model = SuratPeringatan::class;
    static ?string $label = 'Surat Peringatan';
    protected static ?int $navigationSort = 8;
    protected static ?string $navigationGroup = 'Arsip Surat';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('no_surat')
                    ->label('No Surat')
                    ->required(),
                Forms\Components\DatePicker::make('tanggal_surat')
                    ->label('Tanggal Surat')
                    ->required(),
                Forms\Components\Select::make('perihal')
                    ->label('Perihal')
                    ->options([
                        'Surat Peringatan 1' => 'Surat Peringatan 1',
                        'Surat Peringatan 2' => 'Surat Peringatan 2',
                        'Surat Peringatan 3' => 'Surat Peringatan 3',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('jml_lampiran')
                    ->label('Jumlah Lampiran')
                    ->required(),
                Forms\Components\Select::make('penerima')
                    ->label('Penerima')
                    ->live(debounce: 1000)
                    ->afterStateUpdated(function (callable $set, $state) {
                        $user = \App\Models\User::where('name', $state)->first();
                        if ($user) {
                            $set('nim_penerima', $user->nim);
                        } else {
                            $set('nim_penerima', null);
                        }
                    })
                    ->searchable()
                    ->options(
                        \App\Models\User::where('role', '!=', 'external')
                            ->pluck('name', 'name')
                            ->toArray()
                    )
                    ->required(),
                Forms\Components\TextInput::make('nim_penerima')
                    ->label('NIM Penerima')
                    ->readOnly()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_surat')
                    ->label('No Surat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_surat')
                    ->label('Tanggal Surat')
                    ->date('d F Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('perihal')
                    ->label('Perihal')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Surat Peringatan 1' => 'success',
                        'Surat Peringatan 2' => 'warning',
                        'Surat Peringatan 3' => 'danger',
                        default => 'primary',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('jml_lampiran')
                    ->label('Jumlah Lampiran')
                    ->searchable(),
                Tables\Columns\TextColumn::make('penerima')
                    ->label('Penerima')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nim_penerima')
                    ->searchable()
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
                //
            ])
            ->actions([
                Action::make('Download')
                    ->label('Download')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down')
                    ->action(function ($record) {
                        // Use template
                        $template = $record->gender === 'L' ? 'template_sp_l.docx' : 'template_sp_p.docx';
                        $templateProcessor = new TemplateProcessor(public_path('template/' . $template));
                        $templateProcessor->setValue('no_surat', $record->no_surat);
                        $templateProcessor->setValue('tanggal_surat', Carbon::parse($record->tanggal_surat)->translatedFormat('d F Y'));
                        $templateProcessor->setValue('perihal', $record->perihal);
                        $templateProcessor->setValue('penerima', $record->penerima);
                        $templateProcessor->setValue('jml_lampiran', $record->jml_lampiran);

                        // Ensure the directory exists
                        $directory = public_path('storage/surat_peringatan');
                        if (!is_dir($directory)) {
                            mkdir($directory, 0755, true);
                        }

                        // Save to public/storage/surat_peringatan
                        $fileName = 'SP_' . str_replace('/', '_', $record->no_surat) . '.docx';
                        $filePath = "{$directory}/{$fileName}";
                        $templateProcessor->saveAs($filePath);

                        // Download file
                        return response()->download($filePath)->deleteFileAfterSend(true);
                    }),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuratPeringatans::route('/'),
            'create' => Pages\CreateSuratPeringatan::route('/create'),
            'edit' => Pages\EditSuratPeringatan::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->role === 'sekretaris';
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        $query = parent::getEloquentQuery();

        if (in_array($user->role, ['sekretaris', 'ketua'])) {
            return $query; // Sekretaris and Ketua can view all records
        } else {
            // Other users can only view records where they are the recipient
            return $query->where('nim_penerima', $user->nim)
                         ->orWhere('penerima', $user->name);
        }
    }
}
