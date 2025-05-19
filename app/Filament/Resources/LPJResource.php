<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\LPJ;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\LPJResource\Pages;
use App\Filament\Resources\LPJResource\RelationManagers;

class LPJResource extends Resource
{
    protected static ?string $model = LPJ::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'LPJ';
    protected static ?int $navigationSort = 13;
    protected static ?string $navigationGroup = 'Arsip Surat';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\TextInput::make('nama_proker')
                        ->label('Nama Proker')
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
                        ->directory('lpj')
                        ->acceptedFileTypes(['application/pdf'])
                        ->openable()
                        ->visible(fn ($get) => (int) $get('jml_lampiran') > 0)
                        ->columnSpanFull(),
                    Forms\Components\DatePicker::make('tanggal_surat')
                        ->label('Tanggal Surat')
                        ->native(false)
                        ->displayFormat('d F Y')
                        ->required(),
                ])->columns(2),

                Section::make()->schema([
                    Forms\Components\TextInput::make('nama_kegiatan')
                        ->label('Nama Kegiatan')
                        ->required(),
                ])->columns(2),

                Section::make()->schema([
                    Forms\Components\TextInput::make('nama_kabag_kemahasiswaan')
                        ->label('Nama Kabag Kemahasiswaan')
                        ->required(),
                    Forms\Components\TextInput::make('nip_kabag_kemahasiswaan')
                        ->label('NIP Kabag Kemahasiswaan')
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_kabag_kemahasiswaan')
                        ->label('Tanda Tangan Kabag Kemahasiswaan')
                        ->columnSpanFull()
                        ->directory('lpj')
                        ->image(),
                    Forms\Components\Select::make('nama_ketua_panitia')
                        ->label('Nama Ketua Panitia')
                        ->options(function () {
                            return \App\Models\User::pluck('name', 'name');
                        })
                        ->live(debounce:100)
                        ->afterStateUpdated(function (callable $set, $state) {
                            if ($state) {
                                $user = \App\Models\User::where('name', $state)->first();
                                if ($user) {
                                    $set('nim_ketua_panitia', $user->nim);
                                }
                            }
                        })
                        ->required(),
                    Forms\Components\TextInput::make('nim_ketua_panitia')
                        ->label('NIM Ketua Panitia')
                        ->readOnly()
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_ketua_panitia')
                        ->label('Tanda Tangan Ketua Panitia')
                        ->columnSpanFull()
                        ->directory('lpj')
                        ->image(),
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
                        ->directory('lpj')
                        ->image(),
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
                        ->directory('lpj')
                        ->image(),
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
                        ->directory('lpj')
                        ->image(),
                    Forms\Components\TextInput::make('nama_pembina')
                        ->label('Nama Pembina')
                        ->required(),
                    Forms\Components\TextInput::make('nip_pembina')
                        ->label('NIP Pembina')
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_pembina')
                        ->label('Tanda Tangan Pembina')
                        ->columnSpanFull()
                        ->directory('lpj')
                        ->image(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_proker')
                    ->label('Nama Proker')
                    ->searchable(),
                Tables\Columns\TextColumn::make('periode')
                    ->label('Periode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jml_lampiran')
                    ->label('Jumlah Lampiran')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_surat')
                    ->label('Tanggal Surat')
                    ->date('d-m-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_kegiatan')
                    ->label('Nama Kegiatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_kabag_kemahasiswaan')
                    ->label('Kabag Kemahasiswaan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_ketua_panitia')
                    ->label('Ketua Panitia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_ketupel')
                    ->label('Ketua Pelaksana')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_sekretaris')
                    ->label('Sekretaris')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_ketua')
                    ->label('Ketua')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_pembina')
                    ->label('Pembina')
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
                        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(public_path('template/template_lpj.docx'));
                        $templateProcessor->setValue('tanggal_surat', \Carbon\Carbon::parse($record->tanggal_surat)->translatedFormat('d F Y'));
                        $templateProcessor->setValue('periode', $record->periode);

                        if ((int)$record->jml_lampiran === 0) {
                            $templateProcessor->setValue('jml_lampiran', '-');
                            $templateProcessor->setValue('lampiran', '-');
                        } else {
                            $templateProcessor->setValue('jml_lampiran', $record->jml_lampiran);
                            $templateProcessor->setValue('lampiran', $record->lampiran);
                        }

                        $templateProcessor->setValue('nama_proker', $record->nama_proker);
                        $templateProcessor->setValue('nama_kegiatan', $record->nama_kegiatan);

                        $templateProcessor->setValue('nama_kabag_kemahasiswaan', $record->nama_kabag_kemahasiswaan);
                        $templateProcessor->setValue('nip_kabag_kemahasiswaan', $record->nip_kabag_kemahasiswaan);
                        if ($record->ttd_kabag_kemahasiswaan) {
                            $templateProcessor->setImageValue('ttd_kabag_kemahasiswaan', public_path('storage/' . $record->ttd_kabag_kemahasiswaan));
                        }

                        $templateProcessor->setValue('nama_ketua_panitia', $record->nama_ketua_panitia);
                        $templateProcessor->setValue('nim_ketua_panitia', $record->nim_ketua_panitia);
                        if ($record->ttd_ketua_panitia) {
                            $templateProcessor->setImageValue('ttd_ketua_panitia', public_path('storage/' . $record->ttd_ketua_panitia));
                        }

                        $templateProcessor->setValue('nama_ketupel', $record->nama_ketupel);
                        $templateProcessor->setValue('nim_ketupel', $record->nim_ketupel);
                        if ($record->ttd_ketupel) {
                            $templateProcessor->setImageValue('ttd_ketupel', public_path('storage/' . $record->ttd_ketupel));
                        }

                        $templateProcessor->setValue('nama_sekretaris', $record->nama_sekretaris);
                        $templateProcessor->setValue('nim_sekretaris', $record->nim_sekretaris);
                        if ($record->ttd_sekretaris) {
                            $templateProcessor->setImageValue('ttd_sekretaris', public_path('storage/' . $record->ttd_sekretaris));
                        }

                        $templateProcessor->setValue('nama_ketua', $record->nama_ketua);
                        $templateProcessor->setValue('nim_ketua', $record->nim_ketua);
                        if ($record->ttd_ketua) {
                            $templateProcessor->setImageValue('ttd_ketua', public_path('storage/' . $record->ttd_ketua));
                        }

                        $templateProcessor->setValue('nama_pembina', $record->nama_pembina);
                        $templateProcessor->setValue('nip_pembina', $record->nip_pembina);
                        if ($record->ttd_pembina) {
                            $templateProcessor->setImageValue('ttd_pembina', public_path('storage/' . $record->ttd_pembina));
                        }

                        $cleanNamaProker = str_replace('/', '_', $record->nama_proker);
                        $fileName = "LPJ - {$record->nama_kegiatan} - {$cleanNamaProker}";
                        $docxPath = public_path("storage/lpj/{$fileName}.docx");
                        $templateProcessor->saveAs($docxPath);

                        if ((int)$record->jml_lampiran === 0) {
                            return response()->download($docxPath, "{$fileName}.docx")->deleteFileAfterSend(true);
                        } else {
                            $zipFileName = "{$fileName}.zip";
                            $zipPath = public_path("storage/lpj/{$zipFileName}");
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
            'index' => Pages\ListLPJS::route('/'),
            'create' => Pages\CreateLPJ::route('/create'),
            'edit' => Pages\EditLPJ::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return LPJ::query()
            ->when(Auth::user() && !in_array(Auth::user()->role, ['ketua', 'sekretaris']), function ($query) {
                return $query->where('nama_ketua_panitia', Auth::user()->name)
                            ->orWhere('nama_ketupel', Auth::user()->name);
            })
            ->when((Auth::user()->role == 'ketua'), function ($query) {
                return $query->where('nama_ketua', Auth::user()->name);
            })
            ->when(Auth::user()->role == 'sekretaris', function ($query) {
                return $query;
            });
    }
}
