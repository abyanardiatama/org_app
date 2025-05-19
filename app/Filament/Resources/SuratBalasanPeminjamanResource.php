<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SuratBalasanPeminjaman;
use App\Models\SuratPeminjaman;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\SuratBalasanPeminjamanResource\Pages;

class SuratBalasanPeminjamanResource extends Resource
{
    protected static ?string $model = SuratBalasanPeminjaman::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Surat Balasan Peminjaman';
    protected static ?int $navigationSort = 14;
    protected static ?string $navigationGroup = 'Arsip Surat';

    public static function form(Form $form): Form
    {
        $isEdit = request()->routeIs('filament.admin.resources.surat-balasan-peminjamen.edit');
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\TextInput::make('no_surat')
                        ->label('No Surat Balasan')
                        ->required(),
                    Forms\Components\DatePicker::make('tanggal_surat')
                        ->label('Tanggal Surat Balasan')
                        ->native(false)
                        ->displayFormat('d F Y')
                        ->required(),
                    Forms\Components\TextInput::make('jml_lampiran')
                        ->label('Jumlah Lampiran')
                        ->default(0)
                        ->numeric()
                        ->live(),
                    Forms\Components\FileUpload::make('lampiran')
                        ->label('Lampiran')
                        ->directory('surat_balasan_peminjaman')
                        ->acceptedFileTypes(['application/pdf'])
                        ->openable()
                        ->visible(fn ($get) => (int) $get('jml_lampiran') > 0)
                        ->columnSpanFull(),
                    Forms\Components\Select::make('surat_peminjaman_id')
                        ->label('No Surat Peminjaman')
                        ->options(function () {
                            return \App\Models\SuratPeminjaman::pluck('no_surat', 'id');
                        })
                        ->searchable()
                        ->required()
                        ->disabled($isEdit),
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
                        // ->disabled($isEdit),
                    Forms\Components\TextInput::make('nim_ketua')
                        ->label('NIM Ketua')
                        ->readOnly()
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_ketua')
                        ->label('Tanda Tangan Ketua')
                        ->columnSpanFull()
                        ->directory('surat_balasan_peminjaman')
                        ->image(),
                        // ->disabled($isEdit),
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
                        // ->disabled($isEdit),
                    Forms\Components\TextInput::make('nim_sekretaris')
                        ->label('NIM Sekretaris')
                        ->readOnly()
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_sekretaris')
                        ->label('Tanda Tangan Sekretaris')
                        ->columnSpanFull()
                        ->directory('surat_balasan_peminjaman')
                        ->image(),
                        // ->disabled($isEdit),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_surat')
                    ->label('No Surat Balasan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_surat')
                    ->label('Tanggal Surat Balasan')
                    ->date('d-m-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jml_lampiran')
                    ->label('Jumlah Lampiran')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('suratpeminjaman.no_surat')
                    ->label('No Surat Peminjaman')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_ketua')
                    ->label('Ketua')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_sekretaris')
                    ->label('Sekretaris')
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
                        $record->ttd_sekretaris != null
                    )
                    ->action(function ($record) {
                        $suratPeminjaman = $record->suratPeminjaman;
                        $barangPinjaman = $suratPeminjaman?->barangPeminjaman ?? collect();
                        $tempatPinjaman = $suratPeminjaman?->tempatPeminjaman ?? collect();

                        $templateProcessor = null;
                        $barang_tempat = '';
                        if ($barangPinjaman->count() > 0) {
                            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(public_path('template/template_surat_balasan_peminjaman.docx'));
                            $barang_tempat = $barangPinjaman->pluck('nama_barang')->implode(', ');
                        } else {
                            $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(public_path('template/template_surat_balasan_peminjaman.docx'));
                            $barang_tempat = $tempatPinjaman->pluck('nama_tempat')->implode(', ');
                        }

                        $no_surat_peminjaman = $suratPeminjaman?->no_surat ?? '-';
                        $tanggal_surat_peminjaman = $suratPeminjaman?->tanggal_surat ? Carbon::parse($suratPeminjaman->tanggal_surat)->translatedFormat('d F Y') : '-';
                        $kegiatan = $suratPeminjaman?->kegiatan ?? '-';
                        $tanggal_mulai = $suratPeminjaman?->tanggal_mulai ? Carbon::parse($suratPeminjaman->tanggal_mulai)->translatedFormat('l, d F Y') : '-';
                        $tanggal_selesai = $suratPeminjaman?->tanggal_selesai ? Carbon::parse($suratPeminjaman->tanggal_selesai)->translatedFormat('l, d F Y') : '-';

                        $templateProcessor->setValue('no_surat', $record->no_surat);
                        $templateProcessor->setValue('tanggal_surat', Carbon::parse($record->tanggal_surat)->translatedFormat('d F Y'));
                        $templateProcessor->setValue('jml_lampiran', (int)$record->jml_lampiran === 0 ? '-' : $record->jml_lampiran);
                        $templateProcessor->setValue('lampiran', (int)$record->jml_lampiran === 0 ? '-' : $record->lampiran);

                        $templateProcessor->setValue('no_surat_peminjaman', $no_surat_peminjaman);
                        $templateProcessor->setValue('tanggal_surat_peminjaman', $tanggal_surat_peminjaman);
                        $templateProcessor->setValue('barang_tempat', mb_strtolower($barang_tempat));
                        $templateProcessor->setValue('kegiatan', $kegiatan);
                        $templateProcessor->setValue('tanggal_mulai', $tanggal_mulai);
                        $templateProcessor->setValue('tanggal_selesai', $tanggal_selesai);

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

                        $cleanNoSurat = str_replace('/', '_', $record->no_surat);
                        $cleanNoSuratPeminjaman = str_replace('/', '_', $no_surat_peminjaman);
                        $fileName = "Surat Balasan Peminjaman - {$cleanNoSurat} - {$cleanNoSuratPeminjaman}";
                        $docxPath = public_path("storage/surat_balasan_peminjaman/{$fileName}.docx");
                        $templateProcessor->saveAs($docxPath);

                        if ((int)$record->jml_lampiran === 0) {
                            return response()->download($docxPath, "{$fileName}.docx")->deleteFileAfterSend(true);
                        } else {
                            $zipFileName = "{$fileName}.zip";
                            $zipPath = public_path("storage/surat_balasan_peminjaman/{$zipFileName}");
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
            'index' => Pages\ListSuratBalasanPeminjamen::route('/'),
            'create' => Pages\CreateSuratBalasanPeminjaman::route('/create'),
            'edit' => Pages\EditSuratBalasanPeminjaman::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return SuratBalasanPeminjaman::query()
            ->when(Auth::user() && !in_array(Auth::user()->role, ['ketua', 'sekretaris']), function ($query) {
                return $query->where('nama_ketua', Auth::user()->name)
                            ->orWhere('nama_sekretaris', Auth::user()->name);
            })
            ->when((Auth::user()->role == 'ketua'), function ($query) {
                return $query->where('nama_ketua', Auth::user()->name);
            })
            ->when(Auth::user()->role == 'sekretaris', function ($query) {
                return $query;
            });
    }
}
