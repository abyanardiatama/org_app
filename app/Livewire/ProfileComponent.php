<?php

namespace App\Livewire;

use Filament\Forms;
use Livewire\Component;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;

class ProfileComponent extends Component implements HasForms
{
    use InteractsWithForms;
    use HasSort;

    public ?array $data = [];

    protected static int $sort = 50;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->model(Auth::user())
            ->schema([
                Section::make('Profile Data')
                    ->aside()
                    ->description('Manage your profile data')
                    ->columns(2) // Arrange fields into two columns
                    ->schema([
                        Forms\Components\TextInput::make('nim')
                            ->label('NIM')
                            ->columnSpan(1)
                            ->default(fn() => Auth::user()->nim)
                            ,
                        Forms\Components\Select::make('divisi_id')
                            ->label('Divisi')
                            ->relationship('divisi', 'nama_divisi')
                            ->options(
                                Auth::user()->divisi->pluck('nama_divisi', 'id')
                            )
                            ->searchable()
                            ->default(fn() => Auth::user()->divisi->id) // Use the ID instead of nama_divisi
                            ->columnSpan(1)
                            ,
                        Forms\Components\TextInput::make('prodi')
                            ->label('Prodi')
                            ->columnSpan(1)
                            ->default(fn() => Auth::user()->prodi)
                            ,
                        Forms\Components\TextInput::make('fakultas')
                            ->label('Fakultas')
                            ->columnSpan(1)
                            ->default(fn() => Auth::user()->fakultas)
                            ,
                        Forms\Components\TextInput::make('angkatan')
                            ->label('Angkatan')
                            ->numeric()
                            ->columnSpan(1)
                            ->default(fn() => Auth::user()->angkatan)
                            ,
                        Forms\Components\Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->default(fn() => Auth::user()->gender)
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ]),
                        Forms\Components\Select::make('amanah')
                            ->label('Amanah')
                            ->options(
                                [
                                    'Ketua Umum',
                                    'Wakil Ketua Umum',
                                    'Sekretaris Umum',
                                    'Bendahara Umum',
                                    'Ketua Divisi',
                                    'Wakil Ketua Divisi',
                                    'Staff',
                                ]
                            )
                            ->columnSpan(1)
                            ->searchable()
                            ->default(fn() => Auth::user()->amanah)
                            ,
                        Forms\Components\Select::make('role')
                            ->label('Role')
                            ->columnSpan(1)
                            ->options([
                                'admin' => 'Admin',
                                'bendahara' => 'Bendahara',
                                'sekretaris' => 'Sekretaris',
                                'ketua' => 'Ketua',
                                'anggota' => 'Anggota',
                                'external' => 'External',
                                'bsomtq' => 'BSOMTQ',
                                'phkmi' => 'PHKMI',
                            ])
                            ->default(fn() => Auth::user()->role) // Use the current user's role as the default value
                            ->visible(fn() => in_array(Auth::user()->role, ['ketua', 'sekretaris'])) // Show only for ketua and sekretaris
                            ->required(),
                        Forms\Components\TextInput::make('role')
                            ->label('Role')
                            ->columnSpan(1)
                            ->disabled() // Disable for ketua and sekertaris
                            ->visible(fn() => !in_array(Auth::user()->role, ['ketua', 'sekretaris'])) // Show only for non-ketua and non-sekretaris
                            ->default(fn() => ucwords(Auth::user()->role)),
                        Forms\Components\TextInput::make('no_hp')
                            ->label('No HP')
                            ->tel()
                            ->prefix('+62')
                            ->columnSpan(2)
                            ->default(fn() => Auth::user()->no_hp)
                            ,
                        
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        // //save the data to the database
        $user = User::find(Auth::user()->id);
        $user->nim = $data['nim'];
        $user->divisi_id = $data['divisi_id'];
        $user->prodi = $data['prodi'];
        $user->fakultas = $data['fakultas'];
        $user->angkatan = $data['angkatan'];
        $user->amanah = $data['amanah'];
        $user->no_hp = $data['no_hp'];
        // $user->role = $data['role'];
        $user->save();
        
        // Update role hanya jika user ketua atau sekretaris
        if (in_array(Auth::user()->role, ['ketua', 'sekretaris'])) {
            $user->role = $data['role'];
        }
        
        $user->save();
        Notification::make()
        ->title('Data berhasil disimpan')
        ->success()
        ->send();
    }

    public function render(): View
    {
        return view('livewire.profile-component');
    }
}
