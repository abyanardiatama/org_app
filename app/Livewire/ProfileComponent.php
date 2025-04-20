<?php

namespace App\Livewire;

use Filament\Forms;
use Livewire\Component;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Filament\Forms\Components\Section;
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
                    ->schema([
                        Forms\Components\TextInput::make('nim')
                            ->label('NIM')
                            ->columnSpan(2)
                            ->default(fn() => Auth::user()->nim)
                            ->required(),
                        Forms\Components\Select::make('divisi_id')
                            ->label('Divisi')
                            ->relationship('divisi', 'nama_divisi')
                            ->options(
                                Auth::user()->divisi->pluck('nama_divisi', 'id')
                            )
                            ->searchable()
                            ->default(fn() => Auth::user()->divisi->id) // Use the ID instead of nama_divisi
                            ->columnSpan(2)
                            ->required(),
                        Forms\Components\TextInput::make('prodi')
                            ->label('Prodi')
                            ->columnSpan(2)
                            ->default(fn() => Auth::user()->prodi)
                            ->required(),
                        Forms\Components\TextInput::make('fakultas')
                            ->label('Fakultas')
                            ->columnSpan(2)
                            ->default(fn() => Auth::user()->fakultas)
                            ->required(),
                        Forms\Components\TextInput::make('angkatan')
                            ->label('Angkatan')
                            ->numeric()
                            ->columnSpan(2)
                            ->default(fn() => Auth::user()->angkatan)
                            ->required(),
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
                            ->columnSpan(2)
                            ->searchable()
                            ->default(fn() => Auth::user()->amanah)
                            ->required(),
                        Forms\Components\TextInput::make('no_hp')
                            ->label('No HP')
                            ->tel()
                            ->prefix('+62')
                            ->columnSpan(2)
                            ->default(fn() => Auth::user()->no_hp)
                            ->required(),
                        Forms\Components\Select::make('role')
                            ->label('Role')
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
                            ->default(fn() => Auth::user()->role) // Disable for non-ketua and non-sekretaris
                            ->visible(fn() => in_array(Auth::user()->role, ['ketua', 'sekretaris'])) // Show only for non-ketua and non-sekretaris
                            ->required(),
                        Forms\Components\TextInput::make('role')
                            ->label('Role')
                            ->disabled() // Disable for ketua and sekertaris
                            ->visible(fn() => !in_array(Auth::user()->role, ['ketua', 'sekretaris'])) // Show only for non-ketua and non-sekretaris
                            ->default(fn() => ucwords(Auth::user()->role)),
                        
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
