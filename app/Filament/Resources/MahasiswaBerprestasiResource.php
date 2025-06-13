<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MahasiswaBerprestasiResource\Pages;
use App\Models\MahasiswaBerprestasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\Action;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Database\Eloquent\Builder;

class MahasiswaBerprestasiResource extends Resource
{
    protected static ?string $model = MahasiswaBerprestasi::class;
    protected static ?string $label = 'Mahasiswa Berprestasi';
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Prestasi';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Section Mahasiswa
                Forms\Components\Section::make('Data Mahasiswa')
                    ->schema([
                        Forms\Components\TextInput::make('nim')
                            ->label('NIM')
                            ->required(),
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Mahasiswa')
                            ->required(),
                        Forms\Components\TextInput::make('prodi')
                            ->label('Program Studi')
                            ->required(),
                        Forms\Components\TextInput::make('fakultas')
                            ->label('Fakultas')
                            ->required(),
                        Forms\Components\TextInput::make('nomor_telepon')->label('Nomor Telepon')->tel(),
                        Forms\Components\TextInput::make('nomor_wa')->label('Nomor WA'),
                    ])
                    ->columns(2),

                // Section Prestasi
                Forms\Components\Section::make('Data Prestasi')
                    ->schema([
                        Forms\Components\TextInput::make('jenis_prestasi')
                            ->label('Jenis Prestasi')
                            ->required(),
                        Forms\Components\TextInput::make('tingkat_prestasi')
                            ->label('Tingkat Prestasi')
                            ->required(),
                        Forms\Components\TextInput::make('nama_kejuaraan')
                            ->label('Nama Kejuaraan')
                            ->required(),
                        Forms\Components\Select::make('perlombaan_id')
                            ->label('Perlombaan')
                            //required
                            ->required()
                            ->relationship('perlombaan', 'nama_kejuaraan')
                            ->searchable(),
                        Forms\Components\TextInput::make('penyelenggara')->label('Penyelenggara'),
                        Forms\Components\TextInput::make('lokasi_penyelenggara')->label('Lokasi Penyelenggara'),
                        Forms\Components\TextInput::make('jumlah_pt_peserta')->label('Jumlah Perguruan Tinggi Peserta'),
                        Forms\Components\TextInput::make('jumlah_peserta_lomba')->label('Jumlah Peserta Lomba'),
                        Forms\Components\DatePicker::make('tanggal_mulai')->label('Tanggal Mulai')->required(),
                        Forms\Components\DatePicker::make('tanggal_selesai')->label('Tanggal Selesai'),
                        Forms\Components\TextInput::make('peringkat')->label('Peringkat'),
                        Forms\Components\Select::make('tunggal_beregu')->label('Tunggal/Beregu')->options([
                            'tunggal' => 'Tunggal',
                            'beregu' => 'Beregu',
                        ]),
                        Forms\Components\TextInput::make('kategori_tanding')->label('Kategori Perlombaan'),
                    ])
                    ->columns(3),

                // Section Dosen Pembimbing
                Forms\Components\Section::make('Dosen Pembimbing')
                    ->schema([
                        Forms\Components\TextInput::make('dosen_pembimbing')->label('Dosen Pembimbing'),
                        Forms\Components\TextInput::make('nidn')->label('NIDN'),
                        Forms\Components\TextInput::make('nip')->label('NIP'),
                    ])
                    ->columns(2),

                // Section Dokumentasi
                Forms\Components\Section::make('Dokumentasi')
                    ->schema([
                        Forms\Components\FileUpload::make('foto_penerima_prestasi')
                            ->label('Foto Penerima Prestasi')
                            ->image()
                            ->directory('mahasiswa_berprestasi/foto')
                            ->imagePreviewHeight('100')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/*']),
                        Forms\Components\FileUpload::make('sertifikat_prestasi')
                            ->label('Sertifikat Prestasi')
                            ->directory('mahasiswa_berprestasi/sertifikat')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/*', 'application/pdf']),
                        Forms\Components\FileUpload::make('surat_tugas')
                            ->label('Surat Tugas')
                            ->directory('mahasiswa_berprestasi/surat_tugas')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/*', 'application/pdf']),
                        Forms\Components\TextInput::make('url_kegiatan')->label('URL Kegiatan'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nim')->label('NIM')->searchable(),
                Tables\Columns\TextColumn::make('nama')->label('Nama')->searchable(),
                Tables\Columns\TextColumn::make('prodi')->label('Prodi')->searchable(),
                Tables\Columns\TextColumn::make('fakultas')->label('Fakultas')->searchable(),
                Tables\Columns\TextColumn::make('jenis_prestasi')->label('Jenis Prestasi')->searchable(),
                Tables\Columns\TextColumn::make('tingkat_prestasi')->label('Tingkat')->searchable(),
                Tables\Columns\TextColumn::make('nama_kejuaraan')->label('Kejuaraan')->searchable(),
                Tables\Columns\TextColumn::make('peringkat')->label('Peringkat')->sortable(),
                Tables\Columns\TextColumn::make('tanggal_mulai')->label('Tgl Mulai')->date()->sortable(),
                Tables\Columns\TextColumn::make('tanggal_selesai')->label('Tgl Selesai')->date()->sortable(),
                Tables\Columns\TextColumn::make('perlombaan.nama_kejuaraan')->label('Perlombaan')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Dibuat')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->label('Diperbarui')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Tambahkan filter jika diperlukan
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Action::make('download')
                        ->label('Download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($record) {
                            // Path template
                            $templatePath = public_path('template/template_prestasi.xlsx');
                            $spreadsheet = IOFactory::load($templatePath);
                            $sheet = $spreadsheet->getActiveSheet();

                            // Isi data ke template
                            $row = 7; // Asumsi row 1 adalah header
                            $sheet->setCellValue("A{$row}", 1); // NO
                            $sheet->setCellValue("B{$row}", $record->nim ?? '');
                            $sheet->setCellValue("C{$row}", $record->nama ?? '');
                            $sheet->setCellValue("D{$row}", $record->prodi ?? '');
                            $sheet->setCellValue("E{$row}", $record->fakultas ?? '');
                            $sheet->setCellValue("F{$row}", $record->jenis_prestasi ?? '');
                            $sheet->setCellValue("G{$row}", $record->tingkat_prestasi ?? '');
                            $sheet->setCellValue("H{$row}", $record->nama_kejuaraan ?? '');
                            $sheet->setCellValue("I{$row}", $record->penyelenggara ?? '');
                            $sheet->setCellValue("J{$row}", $record->lokasi_penyelenggara ?? '');
                            $sheet->setCellValue("K{$row}", $record->jumlah_pt_peserta ?? '');
                            $sheet->setCellValue("L{$row}", $record->jumlah_peserta_lomba ?? '');
                            $sheet->setCellValue("M{$row}", $record->tanggal_mulai ?? '');
                            $sheet->setCellValue("N{$row}", $record->tanggal_selesai ?? '');
                            $sheet->setCellValue("O{$row}", $record->peringkat ?? '');
                            $sheet->setCellValue("P{$row}", $record->tunggal_beregu ?? '');
                            $sheet->setCellValue("Q{$row}", $record->kategori_tanding ?? '');
                            $sheet->setCellValue("R{$row}", ''); // KELAS_TANDING (tidak ada di form)
                            $sheet->setCellValue("S{$row}", $record->dosen_pembimbing ?? '');
                            $sheet->setCellValue("T{$row}", $record->nidn ?? '');
                            $sheet->setCellValue("U{$row}", $record->nip ?? '');

                            // File fields
                            $foto = $record->foto_penerima_prestasi ? Storage::disk('public')->path($record->foto_penerima_prestasi) : '';
                            $sertifikat = $record->sertifikat_prestasi ? Storage::disk('public')->path($record->sertifikat_prestasi) : '';
                            $surat = $record->surat_tugas ? Storage::disk('public')->path($record->surat_tugas) : '';

                            $sheet->setCellValue("V{$row}", $foto ? basename($foto) : '');
                            $sheet->setCellValue("W{$row}", $sertifikat ? basename($sertifikat) : '');
                            $sheet->setCellValue("X{$row}", $surat ? basename($surat) : '');

                            $sheet->setCellValue("Y{$row}", $record->url_kegiatan ?? '');
                            $sheet->setCellValue("Z{$row}", $record->nomor_telepon ?? '');
                            $sheet->setCellValue("AA{$row}", $record->nomor_wa ?? '');

                            // Simpan spreadsheet ke temporary file
                            $filename = "{$record->nim}_{$record->nama}_{$record->prodi}_{$record->perlombaan?->nama_kejuaraan}_{$record->tanggal_mulai}.xlsx";
                            $filename = str_replace([' ', '/'], '_', $filename);
                            $tempExcel = storage_path("app/tmp/{$filename}");
                            if (!is_dir(storage_path('app/tmp'))) {
                                mkdir(storage_path('app/tmp'), 0777, true);
                            }
                            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                            $writer->save($tempExcel);

                            // Siapkan file untuk zip
                            $zipName = "{$record->nim}_{$record->nama}_{$record->prodi}_{$record->perlombaan?->nama_kejuaraan}_{$record->tanggal_mulai}.zip";
                            $zipName = str_replace([' ', '/'], '_', $zipName);
                            $zipPath = storage_path("app/tmp/{$zipName}");
                            $zip = new ZipArchive();
                            $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
                            $zip->addFile($tempExcel, $filename);

                            if ($foto && file_exists($foto)) {
                                $zip->addFile($foto, 'foto/' . basename($foto));
                            }
                            if ($sertifikat && file_exists($sertifikat)) {
                                $zip->addFile($sertifikat, 'sertifikat/' . basename($sertifikat));
                            }
                            if ($surat && file_exists($surat)) {
                                $zip->addFile($surat, 'surat_tugas/' . basename($surat));
                            }
                            $zip->close();

                            // Hapus file excel temp setelah zip
                            @unlink($tempExcel);

                            // Download response
                            return response()->download($zipPath)->deleteFileAfterSend(true);
                        })
                        ->color('success')
                        ->requiresConfirmation()
                        ->tooltip('Download data dan dokumen prestasi'),
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
            'index' => Pages\ListMahasiswaBerprestasis::route('/'),
            'create' => Pages\CreateMahasiswaBerprestasi::route('/create'),
            'edit' => Pages\EditMahasiswaBerprestasi::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // Semua user bisa melihat data, bisa tambahkan filter sesuai kebutuhan
        return parent::getEloquentQuery();
    }
}
