<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SuratPermohonan;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\ActionGroup;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SuratPermohonanResource\Pages;
use App\Filament\Resources\SuratPermohonanResource\RelationManagers;

class SuratPermohonanResource extends Resource
{
    protected static ?string $model = SuratPermohonan::class;
    static ?string $label = 'Surat Permohonan';
    protected static ?int $navigationSort = 8;
    protected static ?string $navigationGroup = 'Arsip Surat';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Surat')->schema([
                    Forms\Components\TextInput::make('no_surat')
                        ->label('No Surat')
                        ->columnSpan(1)
                        ->required(),
                    Forms\Components\DatePicker::make('tanggal_surat')
                        ->label('Tanggal Surat')
                        ->columnSpan(1)
                        ->required(),
                    Forms\Components\TextInput::make('jml_lampiran')
                        ->label('Jumlah Lampiran')
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('perihal')
                        ->columnSpan(1)
                        ->required(),
                    Forms\Components\TextInput::make('tujuan_surat')
                        ->label('Tujuan Surat')
                        ->columnSpan(1)
                        ->required(),
                ])->columns(2),

                Section::make('Detail Kegiatan')->schema([
                    Forms\Components\TextInput::make('keperluan')
                        ->label('Keperluan')
                        ->required(),
                    Forms\Components\TextInput::make('penyelenggara')
                        ->label('Penyelenggara')
                        ->required(),
                    Forms\Components\DateTimePicker::make('tanggal_mulai')
                        ->label('Tanggal Mulai')
                        ->required(),
                    Forms\Components\DateTimePicker::make('tanggal_selesai')
                        ->label('Tanggal Selesai')
                        ->required(),
                    Forms\Components\TextInput::make('tempat')
                        ->label('Tempat')
                        ->required(),
                ])->columns(2),

                Section::make('Tanda Tangan dan Pembina')->schema([
                    Forms\Components\Select::make('ketua_kmi')
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
                        ->directory('surat_permohonan')
                        ->image()
                        ->columnSpanFull()
                        ->visible(fn () => in_array(Auth::user()->role, ['ketua', 'sekretaris'])),
                    Forms\Components\TextInput::make('pembina_kmi')
                        ->label('Pembina KMI')
                        ->required(),
                    Forms\Components\TextInput::make('nip_pembina_kmi')
                        ->label('NIP Pembina KMI')
                        ->required(),
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
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jml_lampiran')
                    ->label('Jumlah Lampiran')
                    ->searchable(),
                Tables\Columns\TextColumn::make('perihal')
                    ->label('Perihal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tujuan_surat')
                    ->label('Tujuan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('keperluan')
                    ->label('Keperluan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('penyelenggara')
                    ->label('Penyelenggara')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('tanggal_mulai')
                //     ->dateTime()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('tanggal_selesai')
                //     ->dateTime()
                //     ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_acara')
                    ->label('Tanggal Acara')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(fn ($record) => $record->tanggal_mulai) // ambil nilai dasar supaya kolom bisa diproses
                    ->formatStateUsing(fn ($state, $record) =>
                        \Carbon\Carbon::parse($record->tanggal_mulai)->toDateString() === \Carbon\Carbon::parse($record->tanggal_selesai)->toDateString()
                            ? \Carbon\Carbon::parse($record->tanggal_mulai)->translatedFormat('d F Y, H:i') . ' - ' . \Carbon\Carbon::parse($record->tanggal_selesai)->translatedFormat('H:i')
                            : \Carbon\Carbon::parse($record->tanggal_mulai)->translatedFormat('d F Y, H:i') . ' - ' . \Carbon\Carbon::parse($record->tanggal_selesai)->translatedFormat('d F Y, H:i')
                    ),
                Tables\Columns\TextColumn::make('tempat')
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
                    $templateProcessor = new TemplateProcessor(public_path('template/template_surat_permohonan.docx'));

                    $templateProcessor->setValue('no_surat', $record->no_surat);
                    $templateProcessor->setValue('tanggal_surat', Carbon::parse($record->tanggal_surat)->translatedFormat('d F Y'));
                    $templateProcessor->setValue('jml_lampiran', $record->jml_lampiran);
                    $templateProcessor->setValue('perihal', $record->perihal);
                    $templateProcessor->setValue('tujuan_surat', $record->tujuan_surat);
                    $templateProcessor->setValue('keperluan', $record->keperluan);
                    $templateProcessor->setValue('penyelenggara', $record->penyelenggara);
                    $templateProcessor->setValue('tanggal_mulai', Carbon::parse($record->tanggal_mulai)->translatedFormat('d F Y'));
                    $templateProcessor->setValue('waktu_mulai', Carbon::parse($record->tanggal_mulai)->translatedFormat('H:i'));
                    $templateProcessor->setValue('tanggal_selesai', Carbon::parse($record->tanggal_selesai)->translatedFormat('d F Y'));
                    $templateProcessor->setValue('waktu_selesai', Carbon::parse($record->tanggal_selesai)->translatedFormat('H:i'));
                    $templateProcessor->setValue('tempat', $record->tempat);
                    $templateProcessor->setValue('ketua_kmi', $record->ketua_kmi);
                    $templateProcessor->setValue('nim_ketua_kmi', $record->nim_ketua_kmi);
                    $templateProcessor->setValue('pembina_kmi', $record->pembina_kmi);
                    $templateProcessor->setValue('nip_pembina_kmi', $record->nip_pembina_kmi);
                    $templateProcessor->setImageValue('ttd_ketua_kmi', public_path('storage/' . $record->ttd_ketua_kmi));
                    // $templateProcessor->setImageValue('ttd_pembina_kmi', public_path('storage/surat_permohonan/' . $record->ttd_pembina_kmi));
                
                    $cleanNoSurat = str_replace('/', '_', $record->no_surat);
                    $fileName = "Surat Permohonan {$record->keperluan} - {$cleanNoSurat}";
                    $docxPath = public_path("storage/surat_permohonan/{$fileName}.docx");
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
            'index' => Pages\ListSuratPermohonans::route('/'),
            'create' => Pages\CreateSuratPermohonan::route('/create'),
            'edit' => Pages\EditSuratPermohonan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        //ketua show data where ttd_sekretaris_kmi is not null
        return SuratPermohonan::query()
        ->when(Auth::user()->role === 'ketua', function (Builder $query) {
            return $query
                //where 'ketua_kmi' is the same as Auth::user()->name
                ->where('ketua_kmi', Auth::user()->name);
        });
    }
}
