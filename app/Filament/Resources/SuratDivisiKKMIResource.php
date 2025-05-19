<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SuratDivisiKKMI;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\ActionGroup;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SuratDivisiKKMIResource\Pages;

class SuratDivisiKKMIResource extends Resource
{
    protected static ?string $model = SuratDivisiKKMI::class;
    protected static ?string $label = 'Surat Divisi KKMI';
    protected static ?int $navigationSort = 11;
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
                    Forms\Components\TextInput::make('periode')
                        ->label('Periode')
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
                    Forms\Components\DatePicker::make('tanggal_mulai')
                        ->label('Tanggal Mulai Acara')
                        ->native(false)
                        ->displayFormat('d F Y H:i')
                        ->required(),
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
                        ->directory('surat_divisi_kkmi')
                        ->image()
                        ->visible(fn () => in_array(Auth::user()->role, ['sekretaris', 'ketua'])),
                    Forms\Components\TextInput::make('nama_ketupel_kmi')
                        ->label('Nama Ketua Pelaksana KMI')
                        ->required(),
                    Forms\Components\TextInput::make('nim_ketupel_kmi')
                        ->label('NIM Ketua Pelaksana KMI')
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_ketupel_kmi')
                        ->label('Tanda Tangan Ketua Pelaksana KMI')
                        ->columnSpanFull()
                        ->directory('surat_divisi_kkmi')
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
                Tables\Actions\Action::make('Download')
                    ->label('Download')
                    ->color('success')
                    ->visible(fn ($record) => $record->ttd_ketua_kmi != null && $record->ttd_ketupel_kmi != null)
                    ->icon('heroicon-o-arrow-down')
                    ->action(function ($record) {
                        $templateProcessor = new TemplateProcessor(public_path('template/template_surat_divisi_kkmi.docx'));

                        $templateProcessor->setValue('no_surat', $record->no_surat);
                        $templateProcessor->setValue('tgl_surat', Carbon::parse($record->tanggal_surat)->translatedFormat('d F Y'));
                        $templateProcessor->setValue('periode', $record->periode);
                        $templateProcessor->setValue('kepada', $record->kepada);
                        $templateProcessor->setValue('kegiatan', $record->kegiatan);
                        $templateProcessor->setValue('tempat', $record->tempat);
                        $templateProcessor->setValue('tanggal_mulai', Carbon::parse($record->tanggal_mulai)->translatedFormat('d F Y'));
                        $templateProcessor->setValue('waktu_mulai', Carbon::parse($record->tanggal_mulai)->translatedFormat('H:i'));

                        $templateProcessor->setValue('nama_ketua_kmi', $record->nama_ketua_kmi);
                        $templateProcessor->setValue('nim_ketua_kmi', $record->nim_ketua_kmi);
                        $templateProcessor->setImageValue('ttd_ketua_kmi', public_path('storage/' . $record->ttd_ketua_kmi));
                        $templateProcessor->setValue('nama_ketupel', $record->nama_ketupel_kmi);
                        $templateProcessor->setValue('nim_ketupel', $record->nim_ketupel_kmi);
                        $templateProcessor->setImageValue('ttd_ketupel', public_path('storage/' . $record->ttd_ketupel_kmi));

                        $cleanNoSurat = str_replace('/', '_', $record->no_surat);
                        $fileName = "Surat Divisi KKMI - {$record->kegiatan} - {$cleanNoSurat}";
                        $docxPath = public_path("storage/surat_divisi_kkmi/{$fileName}.docx");

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
            'index' => Pages\ListSuratDivisiKKMIS::route('/'),
            'create' => Pages\CreateSuratDivisiKKMI::route('/create'),
            'edit' => Pages\EditSuratDivisiKKMI::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return SuratDivisiKKMI::query()
            ->when(Auth::user() && !in_array(Auth::user()->role, ['ketua', 'sekretaris']), function (Builder $query) {
                return $query->where('nama_ketupel_kmi', Auth::user()->name);
            })
            ->when((Auth::user()->role == 'ketua'), function (Builder $query) {
                return $query->where('nama_ketua_kmi', Auth::user()->name);
            })
            ->when(Auth::user()->role == 'sekretaris', function (Builder $query) {
                return $query->withoutGlobalScopes([SoftDeletingScope::class]);
            });
    }
}
