<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SuratPeminjaman;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Actions\ActionGroup;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SuratPeminjamanResource\Pages;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use App\Filament\Resources\SuratPeminjamanResource\RelationManagers;
use App\Models\BarangPeminjaman;
use App\Models\TempatPeminjaman;

class SuratPeminjamanResource extends Resource
{
    protected static ?string $model = SuratPeminjaman::class;
    protected static ?string $label = 'Surat Peminjaman';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationGroup = 'Arsip Surat';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                    Forms\Components\Select::make('nama_ketua_kmi')
                        ->label('Ketua KMI')
                        ->options(function () {
                            return \App\Models\User::where('role', 'ketua')->pluck('name', 'name');
                        })
                        ->live(debounce:100)
                        ->afterStateUpdated(function (callable $set, $state) {
                            if ($state) {
                                $user = \App\Models\User::where('name', $state)->first();
                                if ($user) {
                                    $set('nim_ketua_kmi', $user->nim);
                                }
                            }
                        })
                        ->required(),
                    Forms\Components\TextInput::make('nim_ketua_kmi')
                        ->label('NIM Ketua KMI')
                        ->readOnly()
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_ketua_kmi')
                        ->label('Tanda Tangan Ketua KMI')
                        ->columnSpanFull()
                        ->directory('surat_peminjaman')
                        ->image()
                        ->visible(fn () => in_array(Auth::user()->role, ['sekretaris', 'ketua'])),
                    Forms\Components\Select::make('nama_sekretaris_kmi')
                        ->label('Nama Sekretaris KMI')
                        ->options(function () {
                            return \App\Models\User::where('role', 'sekretaris')->pluck('name', 'name');
                        })
                        ->live(debounce:100)
                        ->afterStateUpdated(function (callable $set, $state) {
                            if ($state) {
                                $user = \App\Models\User::where('name', $state)->first();
                                if ($user) {
                                    $set('nim_sekretaris_kmi', $user->nim);
                                }
                            }
                        })
                        ->required(),
                    Forms\Components\TextInput::make('nim_sekretaris_kmi')
                        ->label('NIM Sekretaris KMI')
                        ->readOnly()
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_sekretaris_kmi')
                        ->label('Tanda Tangan Sekretaris KMI')
                        ->columnSpanFull()
                        ->directory('surat_peminjaman')
                        ->image()
                        ->visible(fn () => Auth::user()->role == 'sekretaris'),
                    Forms\Components\Select::make('nama_ketupel_kmi')
                        ->label('Nama Ketua Pelaksana KMI')
                        ->options(function () {
                            //user in random order first
                            return \App\Models\User::inRandomOrder()->first()->pluck('name', 'name');
                        })
                        ->live(debounce:100)
                        ->afterStateUpdated(function (callable $set, $state) {
                            if ($state) {
                                $user = \App\Models\User::where('name', $state)->first();
                                if ($user) {
                                    $set('nim_ketupel_kmi', $user->nim);
                                }
                            }
                        })
                        ->required(),
                    Forms\Components\TextInput::make('nim_ketupel_kmi')
                        ->label('NIM Ketua Pelaksana KMI')
                        ->readOnly()
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_ketupel_kmi')
                        ->label('Tanda Tangan Ketua Pelaksana KMI')
                        ->columnSpanFull()
                        ->directory('surat_peminjaman')
                        ->image()
                        ->visible(fn () => in_array(Auth::user()->role, ['sekretaris', 'ketua'])),
                ])->columns(2),

                Section::make()->schema([
                    Forms\Components\Select::make('jenis_peminjaman')
                        ->label('Jenis Peminjaman')
                        ->options([
                            'barang' => 'Barang Peminjaman',
                            'tempat' => 'Tempat Peminjaman',
                        ])
                        ->live()
                        ->dehydrated(false)
                        ->default(function ($record) {
                            if ($record) {
                                if ($record->barangPeminjaman && $record->barangPeminjaman->count() > 0) {
                                    return 'barang';
                                }
                                if ($record->tempatPeminjaman && $record->tempatPeminjaman->count() > 0) {
                                    return 'tempat';
                                }
                            }
                            return null;
                        }),
                    TableRepeater::make('barang_peminjaman')
                        ->relationship('barangPeminjaman')
                        ->schema([
                            Forms\Components\TextInput::make('nama_barang')
                                ->label('Nama Barang')
                                ->placeholder('Nama Barang (Contoh: Laptop)')
                                ->required(),
                            Forms\Components\TextInput::make('jumlah_barang')
                                ->label('Jumlah Barang')
                                ->placeholder('Jumlah Barang (Contoh: 2 Buah)')
                                ->required(),
                        ])
                        ->visible(fn ($get) => $get('jenis_peminjaman') === 'barang'),
                    TableRepeater::make('tempat_peminjaman')
                        ->relationship('tempatPeminjaman')
                        ->schema([
                            Forms\Components\TextInput::make('nama_tempat')
                                ->label('Nama Tempat')
                                ->placeholder('Nama Tempat (Contoh: Gedung Serbaguna)')
                                ->required(),
                        ])
                        ->visible(fn ($get) => $get('jenis_peminjaman') === 'tempat'),
                ]),

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
                    ->date('d-m-Y')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_ketua_kmi')
                    ->label('Ketua KMI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_sekretaris_kmi')
                    ->label('Sekretaris KMI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_ketupel_kmi')
                    ->label('Ketua Pelaksana KMI')
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
                Action::make('Download')
                ->label('Download')
                ->color('success')
                //visible if ttd_ketua_lama and ttd_ketua_baru is not null
                ->visible(fn ($record) => $record->ttd_ketua_kmi != null)
                ->icon('heroicon-o-arrow-down')
                ->action(function ($record) {
                    
                    $barangPinjaman = $record->barangPeminjaman;
                    $tempatPinjaman = $record->tempatPeminjaman;
                    if ($barangPinjaman->count() > 0) {
                        $templateProcessor = new TemplateProcessor(public_path('template/template_surat_peminjaman.docx'));
                    }
                    else {
                        $templateProcessor = new TemplateProcessor(public_path('template/template_surat_peminjaman_tempat.docx'));
                    }

                    $templateProcessor->setValue('no_surat', $record->no_surat);
                    $templateProcessor->setValue('tgl_surat', Carbon::parse($record->tanggal_surat)->translatedFormat('d F Y'));
                    $templateProcessor->setValue('periode', $record->periode);

                    $templateProcessor->setValue('kepada', $record->kepada);
                    $templateProcessor->setValue('kegiatan', $record->kegiatan);
                    $templateProcessor->setValue('tempat', $record->tempat);
                    $templateProcessor->setValue('tanggal_mulai', Carbon::parse($record->tanggal_mulai)->translatedFormat('d F Y'));
                    $templateProcessor->setValue('tanggal_selesai', Carbon::parse($record->tanggal_selesai)->translatedFormat('d F Y'));
                    $templateProcessor->setValue('waktu_mulai', Carbon::parse($record->tanggal_mulai)->translatedFormat('H:i'));

                    $templateProcessor->setValue('nama_ketua_kmi', $record->nama_ketua_kmi);
                    $templateProcessor->setValue('nim_ketua_kmi', $record->nim_ketua_kmi);
                    $templateProcessor->setImageValue('ttd_ketua_kmi', public_path('storage/' . $record->ttd_ketua_kmi));
                    
                    $templateProcessor->setValue('nama_sekretaris', $record->nama_sekretaris_kmi);
                    $templateProcessor->setValue('nim_sekretaris', $record->nim_sekretaris_kmi);
                    $templateProcessor->setImageValue('ttd_sekretaris', public_path('storage/' . $record->ttd_sekretaris_kmi));
                    
                    $templateProcessor->setValue('nama_ketupel', $record->nama_ketupel_kmi);
                    $templateProcessor->setValue('nim_ketupel', $record->nim_ketupel_kmi);
                    $templateProcessor->setImageValue('ttd_ketupel', public_path('storage/' . $record->ttd_ketupel_kmi));
                    
                    if($barangPinjaman->count() > 0) {
                        // Clone baris sesuai jumlah data
                        $templateProcessor->cloneRow('nama_barang', count($barangPinjaman));

                        // Isi nilai ke setiap baris yang dikloning
                        foreach ($barangPinjaman as $index => $barang) {
                            $i = $index + 1; // harus mulai dari 1
                            $templateProcessor->setValue("n#$i", $i);
                            $templateProcessor->setValue("nama_barang#$i", $barang->nama_barang);
                            $templateProcessor->setValue("jumlah_barang#$i", $barang->jumlah_barang);
                        }
                    }
                    else {
                        // Clone baris sesuai jumlah data
                        $templateProcessor->cloneRow('nama_tempat', count($tempatPinjaman));

                        // Isi nilai ke setiap baris yang dikloning
                        foreach ($tempatPinjaman as $index => $tempat) {
                            $i = $index + 1; // harus mulai dari 1
                            $templateProcessor->setValue("n#$i", $i);
                            $templateProcessor->setValue("nama_tempat#$i", $tempat->nama_tempat);
                        }
                    }
                    
                    $cleanNoSurat = str_replace('/', '_', $record->no_surat);
                    $fileName = "Surat Peminjaman - {$record->kegiatan} - {$cleanNoSurat}"; 
                    $docxPath = public_path("storage/surat_peminjaman/{$fileName}.docx");
                    // Save the document as a .docx file
                    $templateProcessor->saveAs($docxPath);
                    return response()->download($docxPath, "{$fileName}.docx")->deleteFileAfterSend(true);   
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
            'index' => Pages\ListSuratPeminjamen::route('/'),
            'create' => Pages\CreateSuratPeminjaman::route('/create'),
            'edit' => Pages\EditSuratPeminjaman::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return SuratPeminjaman::query()
            // ketua can see all surat keterangan aktif
            ->when(Auth::user() && !in_array(Auth::user()->role, ['ketua', 'sekretaris']), function (Builder $query) {
                return $query->where('nama_ketupel_kmi', Auth::user()->name);
            })
            // ketua see where nama_ketua_kmi is same as their name
            ->when((Auth::user()->role == 'ketua'), function (Builder $query) {
                return $query->where('nama_ketua_kmi', Auth::user()->name);
            })
            //sekretaris see all surat keterangan aktif
            ->when(Auth::user()->role == 'sekretaris', function (Builder $query) {
                return $query->withoutGlobalScopes([SoftDeletingScope::class]);
            });
    }
}
