<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\SuratTugas;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\ActionGroup;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SuratTugasResource\Pages;
use App\Filament\Resources\SuratTugasResource\RelationManagers;

class SuratTugasResource extends Resource
{
    protected static ?string $model = SuratTugas::class;
    protected static ?string $label = 'Surat Tugas';
    protected static ?int $navigationSort = 9;
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
                    Forms\Components\TextInput::make('tempat')
                        ->label('Tempat')
                        ->required(),
                    Forms\Components\DatePicker::make('tanggal_surat')
                        ->label('Tanggal Surat')
                        ->required(),
                ])->columns(2),
                Section::make()->schema([
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
                                    $set('jurusan_ketua_kmi', $user->prodi);
                                    $set('jabatan_ketua_kmi', $user->amanah);
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
                        ->required(),
                    Forms\Components\TextInput::make('jabatan_ketua_kmi')
                        ->label('Jabatan')
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_ketua_kmi')
                        ->label('Tanda Tangan ')
                        ->directory('surat_tugas')
                        ->image()
                        ->columnSpanFull(),
                ])->columns(2),
                Section::make()->schema([
                    Forms\Components\TextInput::make('kepada')
                        ->label('Kepada')
                        ->required(),
                    Forms\Components\TextInput::make('nim_kepada')
                        ->label('NIM')
                        ->required(),
                    Forms\Components\TextInput::make('jurusan_kepada')
                        ->label('Jurusan')
                        ->required(),
                    Forms\Components\TextInput::make('jabatan_kepada')
                        ->label('Jabatan')
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_kepada')
                        ->label('Tanda Tangan ')
                        ->directory('surat_tugas')
                        ->image()
                        ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('kepada')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ketua_kmi')
                    ->label('Ketua KMI')
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
                    $templateProcessor = new TemplateProcessor(public_path('template/template_surat_tugas.docx'));

                    $templateProcessor->setValue('no_surat', $record->no_surat);
                    $templateProcessor->setValue('tempat', $record->tempat);
                    $templateProcessor->setValue('tanggal_surat', Carbon::parse($record->tanggal_surat)->translatedFormat('d F Y'));

                    $templateProcessor->setValue('kepada', $record->kepada);
                    $templateProcessor->setValue('nim_kepada', $record->nim_kepada);
                    $templateProcessor->setValue('jurusan_kepada', $record->jurusan_kepada);
                    $templateProcessor->setValue('jabatan_kepada', $record->jabatan_kepada);

                    $templateProcessor->setValue('ketua_kmi', $record->ketua_kmi);
                    $templateProcessor->setValue('nim_ketua', $record->nim_ketua_kmi);
                    $templateProcessor->setValue('jurusan_ketua_kmi', $record->jurusan_ketua_kmi);
                    $templateProcessor->setValue('jabatan_ketua_kmi', $record->jabatan_ketua_kmi);
                    $templateProcessor->setImageValue('ttd_ketua_kmi', public_path('storage/' . $record->ttd_ketua_kmi));
                    $templateProcessor->setImageValue('ttd_kepada', public_path('storage/' . $record->ttd_kepada));
                
                    
                
                    $cleanNoSurat = str_replace('/', '_', $record->no_surat);
                    $fileName = "Surat Tugas - {$cleanNoSurat}"; 
                    $docxPath = public_path("storage/surat_tugas/{$fileName}.docx");
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
            'index' => Pages\ListSuratTugas::route('/'),
            'create' => Pages\CreateSuratTugas::route('/create'),
            'edit' => Pages\EditSuratTugas::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return SuratTugas::query()
            // ketua can see all surat keterangan aktif
            ->when(Auth::user() && !in_array(Auth::user()->role, ['ketua', 'sekretaris']), function (Builder $query) {
                return $query->where('kepada', Auth::user()->name);
            })
            // ketua see where nama_ketua_kmi is same as their name
            ->when((Auth::user()->role == 'ketua'), function (Builder $query) {
                return $query->where('ketua_kmi', Auth::user()->name);
            })
            //sekretaris see all surat keterangan aktif
            ->when(Auth::user()->role == 'sekretaris', function (Builder $query) {
                return $query->withoutGlobalScopes([SoftDeletingScope::class]);
            });
    }
}
