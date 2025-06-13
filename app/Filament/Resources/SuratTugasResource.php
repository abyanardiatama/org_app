<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\SuratTugas;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\SuratTugasResource\Pages;

class SuratTugasResource extends Resource
{
    protected static ?string $model = SuratTugas::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Surat Tugas';
    protected static ?int $navigationSort = 16;
    protected static ?string $navigationGroup = 'Arsip Surat';

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
                ])->columns(2),

                Section::make()->schema([
                    Forms\Components\TextInput::make('tempat')
                        ->label('Tempat')
                        ->required(),
                ])->columns(2),

                Section::make()->schema([
                    Forms\Components\Select::make('ketua_kmi')
                        ->label('Nama Ketua KMI')
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
                        ->label('NIM Ketua KMI')
                        ->readOnly()
                        ->required(),
                    Forms\Components\TextInput::make('jurusan_ketua_kmi')
                        ->label('Jurusan Ketua KMI')
                        ->required(),
                    Forms\Components\TextInput::make('jabatan_ketua_kmi')
                        ->label('Jabatan Ketua KMI')
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_ketua_kmi')
                        ->label('Tanda Tangan Ketua KMI')
                        ->directory('surat_tugas')
                        ->image()
                        ->columnSpanFull(),
                ])->columns(2),

                Section::make()->schema([
                    Forms\Components\Select::make('kepada')
                        ->label('Nama Kepada')
                        ->options(function () {
                            return \App\Models\User::pluck('name', 'name');
                        })
                        ->live(debounce:100)
                        ->afterStateUpdated(function (callable $set, $state) {
                            if ($state) {
                                $user = \App\Models\User::where('name', $state)->first();
                                if ($user) {
                                    $set('nim_kepada', $user->nim);
                                    $set('jurusan_kepada', $user->prodi);
                                    $set('jabatan_kepada', $user->amanah);
                                }
                            }
                        })
                        ->required(),
                    Forms\Components\TextInput::make('nim_kepada')
                        ->label('NIM Kepada')
                        ->readOnly()
                        ->required(),
                    Forms\Components\TextInput::make('jurusan_kepada')
                        ->label('Jurusan Kepada')
                        ->required(),
                    Forms\Components\TextInput::make('jabatan_kepada')
                        ->label('Jabatan Kepada')
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_kepada')
                        ->label('Tanda Tangan Kepada')
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('tempat')
                    ->label('Tempat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ketua_kmi')
                    ->label('Ketua KMI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kepada')
                    ->label('Kepada')
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
                        $record->ttd_ketua_kmi != null && $record->ttd_kepada != null
                    )
                    ->action(function ($record) {
                        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(public_path('template/template_surat_tugas.docx'));

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

                        if ($record->ttd_ketua_kmi) {
                            $templateProcessor->setImageValue('ttd_ketua_kmi', public_path('storage/' . $record->ttd_ketua_kmi));
                        }
                        if ($record->ttd_kepada) {
                            $templateProcessor->setImageValue('ttd_kepada', public_path('storage/' . $record->ttd_kepada));
                        }

                        $cleanNoSurat = str_replace('/', '_', $record->no_surat);
                        $fileName = "Surat Tugas - {$cleanNoSurat}";
                        $docxPath = public_path("storage/surat_tugas/{$fileName}.docx");
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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return SuratTugas::query()
            ->when(Auth::user() && !in_array(Auth::user()->role, ['ketua', 'sekretaris']), function ($query) {
                return $query->where('kepada', Auth::user()->name);
            })
            ->when((Auth::user()->role == 'ketua'), function ($query) {
                return $query->where('ketua_kmi', Auth::user()->name);
            })
            ->when(Auth::user()->role == 'sekretaris', function ($query) {
                return $query;
            });
    }
}
