<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SuratUndangan;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\SuratUndanganResource\Pages;
use App\Filament\Resources\SuratUndanganResource\RelationManagers;

class SuratUndanganResource extends Resource
{
    protected static ?string $model = SuratUndangan::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Surat Undangan';
    protected static ?int $navigationSort = 11;
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
                    Forms\Components\Select::make('periode')
                        ->label('Periode')
                        ->options(
                            collect(range(2022, Carbon::now()->year))
                                ->mapWithKeys(fn ($year) => [$year => $year])
                                ->toArray()
                        )
                        ->required(),
                    Forms\Components\TextInput::make('jml_lampiran')
                        ->label('Jumlah Lampiran')
                        ->default(0)
                        ->numeric()
                        ->live(),
                    Forms\Components\FileUpload::make('lampiran')
                        ->label('Lampiran')
                        ->directory('surat_undangan')
                        ->acceptedFileTypes(['application/pdf'])
                        ->openable()
                        ->visible(fn ($get) => (int) $get('jml_lampiran') > 0)
                        ->columnSpanFull(),
                ])->columns(2),

                Section::make()->schema([
                    Forms\Components\TextInput::make('kepada')
                        ->label('Kepada')
                        ->required(),
                    Forms\Components\TextInput::make('kegiatan')
                        ->label('Nama Kegiatan')
                        ->required(),
                    Forms\Components\TextInput::make('tempat')
                        ->label('Tempat Kegiatan')
                        ->required(),
                    Forms\Components\Group::make([
                        Forms\Components\DateTimePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai Acara')
                            ->native(false)
                            ->displayFormat('d F Y H:i')
                            ->required(),
                        Forms\Components\DateTimePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai Acara')
                            ->native(false)
                            ->displayFormat('d F Y H:i')
                            ->required(),
                    ])->columns(2),
                ])->columns(2),

                Section::make()->schema([
                    Forms\Components\Select::make('nama_ketua')
                        ->label('Nama Ketua')
                        ->options(function () {
                            return \App\Models\User::where('role', 'ketua')->pluck('name', 'name');
                        })
                        ->live(debounce:100)
                        ->afterStateUpdated(function (callable $set, $state) {
                            if ($state) {
                                $user = \App\Models\User::where('name', $state)->first();
                                if ($user) {
                                    $set('nim_ketua', $user->nim);
                                }
                            }
                        })
                        ->required(),
                    Forms\Components\TextInput::make('nim_ketua')
                        ->label('NIM Ketua')
                        ->readOnly()
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_ketua')
                        ->label('Tanda Tangan Ketua')
                        ->columnSpanFull()
                        ->directory('surat_undangan')
                        ->image()
                        ->visible(fn () => in_array(Auth::user()->role, ['sekretaris', 'ketua'])),
                    Forms\Components\Select::make('nama_sekretaris')
                        ->label('Nama Sekretaris')
                        ->options(function () {
                            return \App\Models\User::where('role', 'sekretaris')->pluck('name', 'name');
                        })
                        ->live(debounce:100)
                        ->afterStateUpdated(function (callable $set, $state) {
                            if ($state) {
                                $user = \App\Models\User::where('name', $state)->first();
                                if ($user) {
                                    $set('nim_sekretaris', $user->nim);
                                }
                            }
                        })
                        ->required(),
                    Forms\Components\TextInput::make('nim_sekretaris')
                        ->label('NIM Sekretaris')
                        ->readOnly()
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_sekretaris')
                        ->label('Tanda Tangan Sekretaris')
                        ->columnSpanFull()
                        ->directory('surat_undangan')
                        ->image()
                        ->visible(fn () => Auth::user()->role == 'sekretaris'),
                    Forms\Components\Select::make('nama_ketupel')
                        ->label('Nama Ketua Pelaksana')
                        ->options(function () {
                            return \App\Models\User::pluck('name', 'name');
                        })
                        ->live(debounce:100)
                        ->afterStateUpdated(function (callable $set, $state) {
                            if ($state) {
                                $user = \App\Models\User::where('name', $state)->first();
                                if ($user) {
                                    $set('nim_ketupel', $user->nim);
                                }
                            }
                        })
                        ->required(),
                    Forms\Components\TextInput::make('nim_ketupel')
                        ->label('NIM Ketua Pelaksana')
                        ->readOnly()
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_ketupel')
                        ->label('Tanda Tangan Ketua Pelaksana')
                        ->columnSpanFull()
                        ->directory('surat_undangan')
                        ->image()
                        ->visible(fn () => in_array(Auth::user()->role, ['sekretaris', 'ketua'])),
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('periode')
                    ->label('Periode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jml_lampiran')
                    ->label('Jumlah Lampiran')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kepada')
                    ->label('Kepada')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kegiatan')
                    ->label('Kegiatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tempat')
                    ->label('Tempat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_ketua')
                    ->label('Ketua')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_sekretaris')
                    ->label('Sekretaris')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_ketupel')
                    ->label('Ketua Pelaksana')
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
                        $record->ttd_ketua != null &&
                        $record->ttd_sekretaris != null &&
                        $record->ttd_ketupel != null
                    )
                    ->action(function ($record) {
                        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(public_path('template/template_surat_undangan.docx'));
                        $templateProcessor->setValue('no_surat', $record->no_surat);
                        $templateProcessor->setValue('tanggal_surat', \Carbon\Carbon::parse($record->tanggal_surat)->translatedFormat('d F Y'));
                        $templateProcessor->setValue('periode', $record->periode);

                        // Set jml_lampiran dan lampiran
                        if ((int)$record->jml_lampiran === 0) {
                            $templateProcessor->setValue('jml_lampiran', '-');
                            $templateProcessor->setValue('lampiran', '-');
                        } else {
                            $templateProcessor->setValue('jml_lampiran', $record->jml_lampiran);
                            $templateProcessor->setValue('lampiran', $record->lampiran);
                        }

                        $templateProcessor->setValue('kepada', $record->kepada);
                        $templateProcessor->setValue('kegiatan', $record->kegiatan);
                        $templateProcessor->setValue('tempat', $record->tempat);
                        $templateProcessor->setValue('tanggal_mulai', \Carbon\Carbon::parse($record->tanggal_mulai)->translatedFormat('l, d F Y'));
                        $templateProcessor->setValue('tanggal_selesai', \Carbon\Carbon::parse($record->tanggal_selesai)->translatedFormat('l, d F Y'));
                        $templateProcessor->setValue('waktu_mulai', \Carbon\Carbon::parse($record->tanggal_mulai)->translatedFormat('H:i'));

                        $templateProcessor->setValue('nama_ketua', $record->nama_ketua);
                        $templateProcessor->setValue('nim_ketua', $record->nim_ketua);
                        if ($record->ttd_ketua) {
                            $templateProcessor->setImageValue('ttd_ketua', public_path('storage/' . $record->ttd_ketua));
                        }
                        $templateProcessor->setValue('nama_sekretaris', $record->nama_sekretaris);
                        $templateProcessor->setValue('nim_sekretaris', $record->nim_sekretaris);
                        if ($record->ttd_sekretaris) {
                            $templateProcessor->setImageValue('ttd_sekretaris', public_path('storage/' . $record->ttd_sekretaris));
                        }
                        $templateProcessor->setValue('nama_ketupel', $record->nama_ketupel);
                        $templateProcessor->setValue('nim_ketupel', $record->nim_ketupel);
                        if ($record->ttd_ketupel) {
                            $templateProcessor->setImageValue('ttd_ketupel', public_path('storage/' . $record->ttd_ketupel));
                        }
                        $cleanNoSurat = str_replace('/', '_', $record->no_surat);
                        $fileName = "Surat Undangan - {$record->kegiatan} - {$cleanNoSurat}";
                        $docxPath = public_path("storage/surat_undangan/{$fileName}.docx");
                        $templateProcessor->saveAs($docxPath);

                        if ((int)$record->jml_lampiran === 0) {
                            // Download hanya docx
                            return response()->download($docxPath, "{$fileName}.docx")->deleteFileAfterSend(true);
                        } else {
                            // Gabungkan docx dan lampiran (PDF) ke dalam satu zip
                            $zipFileName = "{$fileName}.zip";
                            $zipPath = public_path("storage/surat_undangan/{$zipFileName}");
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
                            // Hapus file docx setelah zip dibuat
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
            'index' => Pages\ListSuratUndangans::route('/'),
            'create' => Pages\CreateSuratUndangan::route('/create'),
            'edit' => Pages\EditSuratUndangan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return SuratUndangan::query()
            // ketua can see all surat undangan
            ->when(Auth::user() && !in_array(Auth::user()->role, ['ketua', 'sekretaris']), function ($query) {
                return $query->where('nama_ketupel', Auth::user()->name);
            })
            // ketua see where nama_ketua is same as their name
            ->when((Auth::user()->role == 'ketua'), function ($query) {
                return $query->where('nama_ketua', Auth::user()->name);
            })
            // sekretaris see all surat undangan
            ->when(Auth::user()->role == 'sekretaris', function ($query) {
                return $query;
            });
    }
}
