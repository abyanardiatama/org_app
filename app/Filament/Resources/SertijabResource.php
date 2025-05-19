<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Sertijab;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\ActionGroup;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\SertijabResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SertijabResource\RelationManagers;
use Filament\Forms\Components\Section;

class SertijabResource extends Resource
{
    protected static ?string $model = Sertijab::class;
    static ?string $label = 'SERTIJAB';
    protected static ?int $navigationSort = 7;
    protected static ?string $navigationGroup = 'Arsip Surat';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Surat')->schema([
                    Forms\Components\DatePicker::make('tanggal_surat')
                        ->label('Tanggal Surat')
                        ->columnSpanFull()
                        ->required(),
                    Forms\Components\Select::make('periode_lama')
                        ->label('Periode Lama')
                        ->options(function () {
                            $years = [];
                            for ($i = 2020; $i <= date('Y'); $i++) {
                                $years[$i] = $i;
                            }
                            return $years;
                        })
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('periode_baru')
                        ->label('Periode Baru')
                        ->options(function () {
                            $years = [];
                            for ($i = 2020; $i <= date('Y'); $i++) {
                                $years[$i] = $i;
                            }
                            return $years;
                        })
                        ->required(),
                ])->columns(2),

                Section::make('Ketua Lama')->schema([
                    Forms\Components\Select::make('ketua_lama')
                        ->label('Ketua Lama')
                        ->options(function () {
                            return \App\Models\User::where('role', 'ketua')->pluck('name', 'name');
                        })
                        ->live(debounce:100)
                        ->afterStateUpdated(function (callable $set, $state) {
                            if ($state) {
                                $user = \App\Models\User::where('name', $state)->first();
                                if ($user) {
                                    $set('nim_ketua_lama', $user->nim);
                                }
                            }
                        })
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('nim_ketua_lama')
                        ->label('NIM Ketua Lama')
                        ->readOnly()
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_ketua_lama')
                        ->label('Tanda Tangan Ketua KMI Lama')
                        ->columnSpanFull()
                        ->image()
                        ->directory('ttd_sertijab')
                        ->imageResizeMode('cover')
                        ->imageResizeTargetWidth('250')
                        ->imageResizeTargetHeight('100'),
                ])->columns(2),

                Section::make('Ketua Baru')->schema([
                    Forms\Components\Select::make('ketua_baru')
                        ->label('Ketua Baru')
                        ->options(function () {
                            return \App\Models\User::where('role', 'ketua')->pluck('name', 'name');
                        })
                        ->live(debounce:100)
                        ->afterStateUpdated(function (callable $set, $state) {
                            if ($state) {
                                $user = \App\Models\User::where('name', $state)->first();
                                if ($user) {
                                    $set('nim_ketua_baru', $user->nim);
                                }
                            }
                        })
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('nim_ketua_baru')
                        ->label('NIM Ketua Baru')
                        ->readOnly()
                        ->required(),
                    Forms\Components\FileUpload::make('ttd_ketua_baru')
                        ->label('Tanda Tangan Ketua KMI Baru')
                        ->columnSpanFull()
                        ->image()
                        ->directory('ttd_sertijab')
                        ->imageResizeMode('cover')
                        ->imageResizeTargetWidth('250')
                        ->imageResizeTargetHeight('100'),
                ])->columns(2),

                Section::make('Informasi Tambahan')->schema([
                    Forms\Components\TextInput::make('warek_mhs')
                        ->label('Wakil Rektor Mahasiswa')
                        ->required(),
                    Forms\Components\TextInput::make('nip_warek_mhs')
                        ->label('NIP Wakil Rektor Mahasiswa')
                        ->required(),
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
                Tables\Columns\TextColumn::make('tanggal_surat')
                    ->label('Tanggal Surat')
                    ->date()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('periode_lama')
                    ->label('Periode Lama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('periode_baru')
                    ->label('Periode Baru')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ketua_lama')
                    ->label('Ketua Lama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ketua_baru')
                    ->label('Ketua Baru')
                    ->searchable(),
                Tables\Columns\TextColumn::make('warek_mhs')
                    ->label('Wakil Rektor Mahasiswa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pembina_kmi')
                    ->label('Pembina KMI')
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
                ->visible(fn ($record) => $record->ttd_ketua_lama != null && $record->ttd_ketua_baru != null)
                ->icon('heroicon-o-arrow-down')
                ->action(function ($record) {
                    $templateProcessor = new TemplateProcessor(public_path('template/template_sertijab.docx'));

                    $templateProcessor->setValue('tanggal_surat', Carbon::parse($record->tanggal_surat)->translatedFormat('d F Y'));
                    $templateProcessor->setValue('lama', $record->periode_lama);
                    $templateProcessor->setValue('baru', $record->periode_baru);
                    $templateProcessor->setValue('ketua_lama', $record->ketua_lama);
                    $templateProcessor->setValue('nim_ketua_lama', $record->nim_ketua_lama);
                    $templateProcessor->setImageValue('ttd_ketua_lama', public_path('storage/' . $record->ttd_ketua_lama));
                    $templateProcessor->setValue('ketua_baru', $record->ketua_baru);
                    $templateProcessor->setValue('nim_ketua_baru', $record->nim_ketua_baru);
                    $templateProcessor->setImageValue('ttd_ketua_baru', public_path('storage/' . $record->ttd_ketua_baru));
                    $templateProcessor->setValue('warek_mhs', $record->warek_mhs);
                    $templateProcessor->setValue('nip_warek_mhs', $record->nip_warek_mhs);
                    $templateProcessor->setValue('pembina_kmi', $record->pembina_kmi);
                    $templateProcessor->setValue('nip_pembina_kmi', $record->nip_pembina_kmi);
                    
                    $cleanNoSurat = str_replace('/', '_', $record->tanggal_surat);
                    $fileName = 'Sertijab_' . $cleanNoSurat;
                    $docxPath = public_path("storage/sertijab/{$fileName}.docx");
                
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
            'index' => Pages\ListSertijabs::route('/'),
            'create' => Pages\CreateSertijab::route('/create'),
            'edit' => Pages\EditSertijab::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        if ($user->role === 'sekretaris') {
            return parent::getEloquentQuery();
        }

        if ($user->role === 'ketua') {
            return parent::getEloquentQuery()
                ->where(function ($query) use ($user) {
                    $query->where('ketua_lama', $user->name)
                        ->orWhere('ketua_baru', $user->name)
                        ->orWhere('nim_ketua_lama', $user->nim)
                        ->orWhere('nim_ketua_baru', $user->nim);
                });
        }

        abort(403, 'Akses tidak diizinkan');
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->role === 'sekretaris';
    }
}
