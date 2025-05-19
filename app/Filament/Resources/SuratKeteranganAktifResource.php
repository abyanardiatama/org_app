<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use App\Models\SuratKeteranganAktif;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\ActionGroup;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SuratKeteranganAktifResource\Pages;
use App\Filament\Resources\SuratKeteranganAktifResource\RelationManagers;

class SuratKeteranganAktifResource extends Resource
{
    protected static ?string $model = SuratKeteranganAktif::class;
    protected static ?string $label = 'Surat Keterangan Aktif';
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
                                    $set('jurusan_ketua_kmi', $user->prodi);
                                }
                            }
                        })
                        ->required(),
                    Forms\Components\TextInput::make('nim_ketua_kmi')
                        ->label('NIM')
                        ->readOnly()
                        ->required(),
                    Forms\Components\TextInput::make('jurusan_ketua_kmi')
                        ->label('Jurusan')
                        ->readOnly()
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_ketua_kmi')
                        ->label('Tanda Tangan Ketua KMI')
                        ->columnSpanFull()
                        ->directory('surat_keterangan_aktif')
                        ->image(),
                ])->columns(2),
                
                Section::make()->schema([
                    Forms\Components\Select::make('kepada')
                        ->label('Kepada')
                        ->options(function () {
                            return \App\Models\User::where('role', '!=', 'external')->pluck('name', 'name');
                        })
                        ->live(debounce:100)
                        ->afterStateUpdated(function (callable $set, $state) {
                            if ($state) {
                                $user = \App\Models\User::where('name', $state)->first();
                                $amanah = $user->amanah;
                                $divisi = $user->divisi->nama_divisi;
                                if ($user) {
                                    $set('nim_kepada', $user->nim);
                                    $set('jurusan_kepada', $user->prodi);
                                    $set('jabatan_kepada', $amanah . ' ' . $divisi);
                                }
                            }
                        })
                        ->required(),
                    Forms\Components\TextInput::make('nim_kepada')
                        ->label('NIM')
                        ->readOnly()
                        ->required(),
                    Forms\Components\TextInput::make('jurusan_kepada')
                        ->label('Jurusan')
                        ->readOnly()
                        ->required(),
                    Forms\Components\TextInput::make('jabatan_kepada')
                        ->label('Jabatan')
                        ->required(),
                ])->columns(2),

                Forms\Components\TextInput::make('pembina_kmi')
                    ->label('Pembina KMI')
                    ->required(),
                Forms\Components\TextInput::make('nip_pembina_kmi')
                    ->label('NIM Pembina KMI')
                    ->required(),
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_ketua_kmi')
                    ->label('Nama Ketua KMI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kepada')
                    ->label('Kepada')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jabatan_kepada')
                    ->label('Jabatan')
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
                    $templateProcessor = new TemplateProcessor(public_path('template/template_surat_keterangan_aktif.docx'));

                    $templateProcessor->setValue('no_surat', $record->no_surat);
                    $templateProcessor->setValue('tanggal_surat', Carbon::parse($record->tanggal_surat)->translatedFormat('d F Y'));
                    $templateProcessor->setValue('periode', $record->periode);

                    $templateProcessor->setValue('kepada', $record->kepada);
                    $templateProcessor->setValue('nim_kepada', $record->nim_kepada);
                    $templateProcessor->setValue('jurusan_kepada', $record->jurusan_kepada);
                    $templateProcessor->setValue('jabatan_kepada', $record->jabatan_kepada);

                    $templateProcessor->setValue('nama_ketua_kmi', $record->nama_ketua_kmi);
                    $templateProcessor->setValue('nim_ketua_kmi', $record->nim_ketua_kmi);
                    $templateProcessor->setValue('jurusan_ketua_kmi', $record->jurusan_ketua_kmi);
                    $templateProcessor->setImageValue('ttd_ketua_kmi', public_path('storage/' . $record->ttd_ketua_kmi));
                
                    $templateProcessor->setValue('pembina_kmi', $record->pembina_kmi);
                    $templateProcessor->setValue('nip_pembina', $record->nip_pembina_kmi);
                    
                
                    $cleanNoSurat = str_replace('/', '_', $record->no_surat);
                    $fileName = "Surat Keterangan Aktif - {$record->kepada} - {$cleanNoSurat}"; 
                    $docxPath = public_path("storage/surat_keterangan_aktif/{$fileName}.docx");
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
            'index' => Pages\ListSuratKeteranganAktifs::route('/'),
            'create' => Pages\CreateSuratKeteranganAktif::route('/create'),
            'edit' => Pages\EditSuratKeteranganAktif::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return SuratKeteranganAktif::query()
            // ketua can see all surat keterangan aktif
            ->when(Auth::user() && !in_array(Auth::user()->role, ['ketua', 'sekretaris']), function (Builder $query) {
                return $query->where('kepada', Auth::user()->name);
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
