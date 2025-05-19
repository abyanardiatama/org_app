<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Filament\Forms;
use App\Models\Spkb;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use PhpOffice\PhpWord\IOFactory;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\SpkbResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SpkbResource\RelationManagers;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

class SpkbResource extends Resource
{
    protected static ?string $model = Spkb::class;
    protected static ?string $label = 'SPKB';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationGroup = 'Arsip Surat';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    Forms\Components\TextInput::make('no_surat')
                        ->label('Nomor Surat')
                        ->required(),
                    Forms\Components\DatePicker::make('tanggal_surat')
                        ->label('Tanggal Surat')
                        ->native(false)    
                        ->displayFormat('d F Y')
                        ->required(),
                    Forms\Components\TextInput::make('jml_lampiran')
                        ->label('Jumlah Lampiran')
                        ->numeric()
                        ->minValue(0)
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
                    Forms\Components\TextInput::make('ketua_kmi')
                        ->label('Ketua KMI')
                        ->required(),
                    Forms\Components\TextInput::make('nim_ketua_kmi')
                        ->label('NIM Ketua KMI')
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_ketua_kmi')
                        ->label('Tanda Tangan Ketua KMI')
                        ->columnSpanFull()
                        ->image()
                        ->directory('ttd_spkb')
                        ->imageResizeMode('cover')
                        ->imageResizeTargetWidth('250')
                        ->imageResizeTargetHeight('100')
                        //visible if ttd sekretaris is not null and for user ketua
                        ->visible(fn (?Spkb $record) => $record !== null && Auth::user()->role === 'ketua' && $record->ttd_sekretaris_kmi !== null)
                        ->required(),
                ])->columns(2),
                Section::make()->schema([
                    Forms\Components\TextInput::make('sekretaris_kmi')
                        ->label('Sekretaris KMI')
                        ->required(),
                    Forms\Components\TextInput::make('nim_sekretaris_kmi')
                        ->label('NIM Sekretaris KMI')
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_sekretaris_kmi')
                        ->label('Tanda Tangan Sekretaris KMI')
                        ->columnSpanFull()
                        ->image()
                        ->directory('ttd_spkb')
                        ->visible(fn () => Auth::user()->role === 'sekretaris')
                        ->required(),
                ])->columns(2),
                Section::make()->schema([
                    Forms\Components\TextInput::make('kabag_binwa')
                        ->label('Kabag Binwa')
                        ->required(),
                    Forms\Components\TextInput::make('nip_kabag_binwa')
                        ->label('NIP Kabag Binwa')
                        ->required(),
                    Forms\Components\TextInput::make('pembina_kmi')
                        ->label('Pembina KMI')
                        ->required(),
                    Forms\Components\TextInput::make('nip_pembina_kmi')
                        ->label('NIP Pembina KMI')
                        ->required(),
                    Forms\Components\FileUpload::make('susunan_pengurus')
                        ->acceptedFileTypes(['application/pdf'])
                        ->directory('spkb')
                        ->openable()
                        ->label('Susunan Pengurus')
                        ->required(),
                ])->columns(2),
                
                
                
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Data SPKB sedang dalam proses')
            ->columns([
                Tables\Columns\TextColumn::make('no_surat')
                    ->label('Nomor Surat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_surat')
                    ->label('Tanggal Surat')
                    ->date('d-m-Y')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jml_lampiran')
                    ->label('Jumlah Lampiran')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('ketua_kmi')
                    ->label('Ketua KMI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nim_ketua_kmi')
                    ->label('NIM Ketua KMI')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('sekretaris_kmi')
                    ->label('Sekretaris KMI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nim_sekretaris_kmi')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('NIM Sekretaris KMI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kabag_binwa')
                    ->label('Kabag Binwa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nip_kabag_binwa')
                    ->label('NIP Kabag Binwa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pembina_kmi')
                    ->label('Pembina KMI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nip_pembina_kmi')
                    ->label('NIP Pembina KMI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('periode')
                    ->label('Periode')
                    ->searchable(),
                Tables\Columns\IconColumn::make('susunan_pengurus')
                    ->icon('heroicon-o-document')
                    ->tooltip(fn (spkb $record) => $record->bukti_pembayaran)
                    ->url(fn (spkb $record) => $record->bukti_pembayaran)
                    ->openUrlInNewTab()
                    ->alignment(Alignment::Center)
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
                Action::make('download')
                    ->label('Download')
                    ->color('success')
                    //visible if ttd_ketua_kmi, ttd_sekretaris_kmi is not null
                    ->visible(fn (spkb $record) => $record->ttd_ketua_kmi != null && $record->ttd_sekretaris_kmi != null)
                    ->icon('heroicon-o-arrow-down')
                    ->action(function ($record) {
                        $templateProcessor = new TemplateProcessor(public_path('template/template-spkb.docx'));
                    
                        $templateProcessor->setValue('no_surat', $record->no_surat);
                        $templateProcessor->setValue('periode', $record->periode);  
                        $templateProcessor->setValue('tanggal', Carbon::parse($record->tanggal_surat)->translatedFormat('j F Y'));
                        $templateProcessor->setValue('jml_lampiran', $record->jml_lampiran);
                        $templateProcessor->setValue('ketua_kmi', $record->ketua_kmi);
                        $templateProcessor->setValue('nim_ketua', $record->nim_ketua_kmi);
                        $templateProcessor->setImageValue('ttd_ketua_kmi', public_path('storage/' . $record->ttd_ketua_kmi));
                        $templateProcessor->setValue('sekretaris_kmi', $record->sekretaris_kmi);
                        $templateProcessor->setValue('nim_sekre', $record->nim_sekretaris_kmi);
                        $templateProcessor->setImageValue('ttd_sekretaris_kmi', public_path('storage/' . $record->ttd_sekretaris_kmi));
                        $templateProcessor->setValue('kabag_binwa', $record->kabag_binwa);
                        $templateProcessor->setValue('nip_kabag_binwa', $record->nip_kabag_binwa);
                        $templateProcessor->setValue('pembina_kmi', $record->pembina_kmi);
                        $templateProcessor->setValue('nip_pembina_kmi', $record->nip_pembina_kmi);
                    
                        // Simpan ke DOCX
                        $cleanNoSurat = str_replace('/', '_', $record->no_surat);
                        $fileName = 'SPKB_' . $cleanNoSurat;
                        $docxPath = public_path("storage/spkb/{$fileName}.docx");
                    
                        $templateProcessor->saveAs($docxPath);
                        
                        // Get $record->susunan_pengurus
                        $pdfPath = public_path('storage/' . $record->susunan_pengurus);

                        // Download both files as a zip
                        $zip = new \ZipArchive();
                        $zipFileName = 'SPKB_' . $cleanNoSurat . '.zip';
                        $zipFilePath = public_path("storage/spkb/{$zipFileName}");
                        if ($zip->open($zipFilePath, \ZipArchive::CREATE) === true) {
                            $zip->addFile($docxPath, "{$fileName}.docx");
                            $zip->addFile($pdfPath, "{$fileName}.pdf");
                            $zip->close();
                        }
                        // Delete the temporary files
                        unlink($docxPath);
                        unlink($pdfPath);
                        // Return the zip file for download
                        return response()->download($zipFilePath)->deleteFileAfterSend(true);
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
            'index' => Pages\ListSpkbs::route('/'),
            'create' => Pages\CreateSpkb::route('/create'),
            'edit' => Pages\EditSpkb::route('/{record}/edit'),
        ];
    }

    //can create
    public static function canCreate(): bool
    {
        return Auth::user()->role === 'sekretaris';
    }

    public static function getEloquentQuery(): Builder
    {
        //ketua show data where ttd_sekretaris_kmi is not null
        return Spkb::query()
        ->when(Auth::user()->role === 'ketua', function (Builder $query) {
            return $query->whereNotNull('ttd_sekretaris_kmi');
        })
        ->when(Auth::user()->role === 'sekretaris', function (Builder $query) {
            //show data where ttd_ketua_kmi is null and ttd_sekretaris_kmi is null
            return $query->whereNull('ttd_ketua_kmi')
                ->whereNull('ttd_sekretaris_kmi');
        });
    }
}
