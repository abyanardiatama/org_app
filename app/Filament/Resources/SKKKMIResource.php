<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SKKKMI;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\SKKKMIResource\Pages;

class SKKKMIResource extends Resource
{
    protected static ?string $model = SKKKMI::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'SK KKMI';
    protected static ?int $navigationSort = 15;
    protected static ?string $navigationGroup = 'Arsip Surat';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\TextInput::make('no_surat')
                        ->label('No Surat')
                        ->required(),
                    Forms\Components\DatePicker::make('tanggal_surat')
                        ->label('Tanggal Surat')
                        ->native(false)
                        ->displayFormat('d F Y')
                        ->required(),
                    Forms\Components\TextInput::make('periode')
                        ->label('Periode')
                        ->required(),
                    Forms\Components\TextInput::make('jml_lampiran')
                        ->label('Jumlah Lampiran')
                        ->default(0)
                        ->numeric()
                        ->live(),
                    Forms\Components\FileUpload::make('lampiran')
                        ->label('Lampiran')
                        ->directory('skkkmi')
                        ->acceptedFileTypes(['application/pdf'])
                        ->openable()
                        ->visible(fn ($get) => (int) $get('jml_lampiran') > 0)
                        ->columnSpanFull(),
                ])->columns(2),

                Section::make()->schema([
                    Forms\Components\TextInput::make('nama_kkmi')
                        ->label('Nama KKMI')
                        ->required(),
                    Forms\Components\TextInput::make('fakultas')
                        ->label('Fakultas')
                        ->required(),
                ])->columns(2),

                Section::make()->schema([
                    Forms\Components\TextInput::make('nama_pembina')
                        ->label('Nama Pembina')
                        ->required(),
                    Forms\Components\TextInput::make('nip_pembina')
                        ->label('NIP Pembina')
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_pembina')
                        ->label('Tanda Tangan Pembina')
                        ->columnSpanFull()
                        ->directory('skkkmi')
                        ->image(),
                ])->columns(2),
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
                    ->date('d-m-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('periode')
                    ->label('Periode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jml_lampiran')
                    ->label('Jumlah Lampiran')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_kkmi')
                    ->label('Nama KKMI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('fakultas')
                    ->label('Fakultas')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_pembina')
                    ->label('Nama Pembina')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nip_pembina')
                    ->label('NIP Pembina')
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
                \Filament\Tables\Actions\Action::make('Download')
                    ->label('Download')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down')
                    ->visible(fn ($record) =>
                        $record->ttd_pembina != null
                    )
                    ->action(function ($record) {
                        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(public_path('template/template_skkkmi.docx'));
                        $templateProcessor->setValue('no_surat', $record->no_surat);
                        $templateProcessor->setValue('tanggal_surat', \Carbon\Carbon::parse($record->tanggal_surat)->translatedFormat('d F Y'));
                        $templateProcessor->setValue('periode', $record->periode);

                        if ((int)$record->jml_lampiran === 0) {
                            $templateProcessor->setValue('jml_lampiran', '-');
                            $templateProcessor->setValue('lampiran', '-');
                        } else {
                            $templateProcessor->setValue('jml_lampiran', $record->jml_lampiran);
                            $templateProcessor->setValue('lampiran', $record->lampiran);
                        }

                        $templateProcessor->setValue('nama_kkmi', strtoupper($record->nama_kkmi));
                        $templateProcessor->setValue('fakultas', strtoupper($record->fakultas));
                        $templateProcessor->setValue('fakultas_kecil', $record->fakultas);

                        $templateProcessor->setValue('nama_pembina', $record->nama_pembina);
                        $templateProcessor->setValue('nip_pembina', $record->nip_pembina);
                        if ($record->ttd_pembina) {
                            $templateProcessor->setImageValue('ttd_pembina', public_path('storage/' . $record->ttd_pembina));
                        }

                        $cleanNoSurat = str_replace('/', '_', $record->no_surat);
                        $fileName = "SK KKMI - {$record->nama_kkmi} - {$cleanNoSurat}";
                        $docxPath = public_path("storage/skkkmi/{$fileName}.docx");
                        $templateProcessor->saveAs($docxPath);

                        if ((int)$record->jml_lampiran === 0) {
                            return response()->download($docxPath, "{$fileName}.docx")->deleteFileAfterSend(true);
                        } else {
                            $zipFileName = "{$fileName}.zip";
                            $zipPath = public_path("storage/skkkmi/{$zipFileName}");
                            $zip = new \ZipArchive();
                            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                                $zip->addFile($docxPath, "{$fileName}.docx");
                                if ($record->lampiran) {
                                    $lampiranPath = public_path('storage/' . $record->lampiran);
                                    if (file_exists($lampiranPath)) {
                                        $zip->addFile($lampiranPath, basename($lampiranPath));
                                    }
                                }
                                $zip->close();
                            }
                            @unlink($docxPath);
                            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
                        }
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
            'index' => Pages\ListSKKKMIS::route('/'),
            'create' => Pages\CreateSKKKMI::route('/create'),
            'edit' => Pages\EditSKKKMI::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return SKKKMI::query()
            ->when(Auth::user() && !in_array(Auth::user()->role, ['ketua', 'sekretaris']), function ($query) {
                return $query->where('nama_pembina', Auth::user()->name);
            })
            ->when((Auth::user()->role == 'ketua'), function ($query) {
                return $query;
            })
            ->when(Auth::user()->role == 'sekretaris', function ($query) {
                return $query;
            });
    }
}
