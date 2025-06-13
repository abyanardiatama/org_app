<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PerlombaanResource\Pages;
use App\Models\Perlombaan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\Action;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class PerlombaanResource extends Resource
{
    protected static ?string $model = Perlombaan::class;
    protected static ?string $label = 'Perlombaan';
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Prestasi';
    protected static ?int $navigationSort = 21;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Perlombaan')
                    ->schema([
                        Forms\Components\TextInput::make('nama_kejuaraan')
                            ->label('Nama Kejuaraan')
                            ->required(),
                        Forms\Components\TextInput::make('jenis_prestasi')
                            ->label('Jenis Prestasi')
                            ->required(),
                        Forms\Components\TextInput::make('tingkat_prestasi')
                            ->label('Tingkat Prestasi')
                            ->required(),
                        Forms\Components\TextInput::make('penyelenggara')
                            ->label('Penyelenggara'),
                        Forms\Components\TextInput::make('lokasi_penyelenggara')
                            ->label('Lokasi Penyelenggara'),
                        Forms\Components\DatePicker::make('tanggal_mulai')
                            ->label('Tanggal Mulai')
                            ->required(),
                        Forms\Components\DatePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai'),
                        Forms\Components\TextInput::make('kategori_tanding')
                            ->label('Kategori Perlombaan'),
                        Forms\Components\TextInput::make('url_kegiatan')
                            ->label('URL Kegiatan'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_kejuaraan')->label('Nama Kejuaraan')->searchable(),
                Tables\Columns\TextColumn::make('jenis_prestasi')->label('Jenis Prestasi')->searchable(),
                Tables\Columns\TextColumn::make('tingkat_prestasi')->label('Tingkat')->searchable(),
                Tables\Columns\TextColumn::make('penyelenggara')->label('Penyelenggara')->searchable(),
                Tables\Columns\TextColumn::make('lokasi_penyelenggara')->label('Lokasi')->searchable(),
                Tables\Columns\TextColumn::make('tanggal_mulai')->label('Tgl Mulai')->date()->sortable(),
                Tables\Columns\TextColumn::make('tanggal_selesai')->label('Tgl Selesai')->date()->sortable(),
                Tables\Columns\TextColumn::make('kategori_tanding')->label('Kategori Tanding')->searchable(),
                // Kolom selalu tampil, hanya link yang aktif jika ada url
                Tables\Columns\TextColumn::make('url_kegiatan')
                    ->label('URL Kegiatan')
                    ->url(fn ($record) => $record->url_kegiatan)
                    ->openUrlInNewTab(),
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
                        ->label('Download Peserta')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->visible(fn ($record) => $record->mahasiswaBerprestasi()->exists())
                        ->action(function ($record) {
                            $templatePath = public_path('template/template_prestasi.xlsx');
                            $spreadsheet = IOFactory::load($templatePath);
                            $sheet = $spreadsheet->getActiveSheet();

                            $pesertas = $record->mahasiswaBerprestasi; // relasi hasMany
                            $row = 7; // Data mulai dari baris 7
                            $no = 1;

                            $files = [];
                            foreach ($pesertas as $peserta) {
                                $sheet->setCellValue("A{$row}", $no++);
                                $sheet->setCellValue("B{$row}", $peserta->nim ?? '');
                                $sheet->setCellValue("C{$row}", $peserta->nama ?? '');
                                $sheet->setCellValue("D{$row}", $peserta->prodi ?? '');
                                $sheet->setCellValue("E{$row}", $peserta->fakultas ?? '');
                                $sheet->setCellValue("F{$row}", $peserta->jenis_prestasi ?? '');
                                $sheet->setCellValue("G{$row}", $peserta->tingkat_prestasi ?? '');
                                $sheet->setCellValue("H{$row}", $peserta->nama_kejuaraan ?? '');
                                $sheet->setCellValue("I{$row}", $peserta->penyelenggara ?? '');
                                $sheet->setCellValue("J{$row}", $peserta->lokasi_penyelenggara ?? '');
                                $sheet->setCellValue("K{$row}", $peserta->jumlah_pt_peserta ?? '');
                                $sheet->setCellValue("L{$row}", $peserta->jumlah_peserta_lomba ?? '');
                                $sheet->setCellValue("M{$row}", $peserta->tanggal_mulai ?? '');
                                $sheet->setCellValue("N{$row}", $peserta->tanggal_selesai ?? '');
                                $sheet->setCellValue("O{$row}", $peserta->peringkat ?? '');
                                $sheet->setCellValue("P{$row}", $peserta->tunggal_beregu ?? '');
                                $sheet->setCellValue("Q{$row}", $peserta->kategori_tanding ?? '');
                                $sheet->setCellValue("R{$row}", ''); // KELAS_TANDING
                                $sheet->setCellValue("S{$row}", $peserta->dosen_pembimbing ?? '');
                                $sheet->setCellValue("T{$row}", $peserta->nidn ?? '');
                                $sheet->setCellValue("U{$row}", $peserta->nip ?? '');

                                // File fields
                                $foto = $peserta->foto_penerima_prestasi ? Storage::disk('public')->path($peserta->foto_penerima_prestasi) : '';
                                $sertifikat = $peserta->sertifikat_prestasi ? Storage::disk('public')->path($peserta->sertifikat_prestasi) : '';
                                $surat = $peserta->surat_tugas ? Storage::disk('public')->path($peserta->surat_tugas) : '';

                                $sheet->setCellValue("V{$row}", $foto ? basename($foto) : '');
                                $sheet->setCellValue("W{$row}", $sertifikat ? basename($sertifikat) : '');
                                $sheet->setCellValue("X{$row}", $surat ? basename($surat) : '');

                                $sheet->setCellValue("Y{$row}", $peserta->url_kegiatan ?? '');
                                $sheet->setCellValue("Z{$row}", $peserta->nomor_telepon ?? '');
                                $sheet->setCellValue("AA{$row}", $peserta->nomor_wa ?? '');

                                // Kumpulkan file untuk zip
                                if ($foto && file_exists($foto)) $files['foto'][] = $foto;
                                if ($sertifikat && file_exists($sertifikat)) $files['sertifikat'][] = $sertifikat;
                                if ($surat && file_exists($surat)) $files['surat_tugas'][] = $surat;

                                $row++;
                            }

                            // Simpan spreadsheet ke temporary file
                            $namaFile = "{$record->nama_kejuaraan}_peserta_" . now()->format('YmdHis') . ".xlsx";
                            $namaFile = str_replace([' ', '/'], '_', $namaFile);
                            $tempExcel = storage_path("app/tmp/{$namaFile}");
                            if (!is_dir(storage_path('app/tmp'))) {
                                mkdir(storage_path('app/tmp'), 0777, true);
                            }
                            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                            $writer->save($tempExcel);

                            // Siapkan file untuk zip
                            $zipName = "{$record->nama_kejuaraan}_peserta_" . now()->format('YmdHis') . ".zip";
                            $zipName = str_replace([' ', '/'], '_', $zipName);
                            $zipPath = storage_path("app/tmp/{$zipName}");
                            $zip = new ZipArchive();
                            $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
                            $zip->addFile($tempExcel, $namaFile);

                            foreach (['foto', 'sertifikat', 'surat_tugas'] as $type) {
                                if (!empty($files[$type])) {
                                    foreach ($files[$type] as $file) {
                                        $zip->addFile($file, "{$type}/" . basename($file));
                                    }
                                }
                            }
                            $zip->close();

                            // Hapus file excel temp setelah zip
                            @unlink($tempExcel);

                            // Download response
                            return response()->download($zipPath)->deleteFileAfterSend(true);
                        })
                        ->color('success')
                        ->requiresConfirmation()
                        ->tooltip('Download seluruh peserta & dokumen lomba'),
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
            'index' => Pages\ListPerlombaans::route('/'),
            'create' => Pages\CreatePerlombaan::route('/create'),
            'edit' => Pages\EditPerlombaan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // Semua user bisa melihat data perlombaan
        return parent::getEloquentQuery();
    }
}
