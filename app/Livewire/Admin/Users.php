<?php

namespace App\Livewire\Admin;

use App\Actions\Users\CreateUserAccount;
use App\Actions\Users\UpdateUserAccount;
use App\Concerns\ProfileValidationRules;
use App\Enums\UserRole;
use App\Models\Institucion;
use App\Models\User;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Usuarios')]
class Users extends Component
{
    use ProfileValidationRules;

    public ?int $userId = null;

    public string $name = '';

    public string $email = '';

    public string $role = '';

    public string $institucion_id = '';

    public string $numero_celular = '';

    public string $direccion = '';

    public string $telefono_oficina = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * @var array<int, string>
     */
    private array $formFields = [
        'userId', 'name', 'email', 'role', 'institucion_id',
        'numero_celular', 'direccion', 'telefono_oficina',
        'password', 'password_confirmation',
    ];

    /**
     * Open the form to create a new user account.
     */
    public function create(): void
    {
        $this->reset($this->formFields);

        Flux::modal('user-form')->show();
    }

    /**
     * Open the form to edit an existing user account.
     */
    public function edit(User $user): void
    {
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role->value;
        $this->institucion_id = $user->institucion_id ? (string) $user->institucion_id : '';
        $this->numero_celular = $user->numero_celular ?? '';
        $this->direccion = $user->direccion ?? '';
        $this->telefono_oficina = $user->telefono_oficina ?? '';
        $this->password = '';
        $this->password_confirmation = '';

        Flux::modal('user-form')->show();
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => $this->nameRules(),
            'email' => $this->emailRules($this->userId),
            'role' => ['required', Rule::enum(UserRole::class)->except(UserRole::Admin)],
            'institucion_id' => [
                Rule::requiredIf(fn () => $this->role === UserRole::Solicitante->value),
                'nullable',
                Rule::exists(Institucion::class, 'id')->where('activo', true),
            ],
            'numero_celular' => ['nullable', 'string', 'max:20'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono_oficina' => ['nullable', 'string', 'max:20'],
            'password' => [
                $this->userId === null ? 'required' : 'nullable',
                'string',
                Password::default(),
                'confirmed',
            ],
        ];
    }

    /**
     * Create or update the user account being edited.
     */
    public function save(CreateUserAccount $createUserAccount, UpdateUserAccount $updateUserAccount): void
    {
        $validated = $this->validate();

        $role = UserRole::from($validated['role']);
        $institucion = filled($validated['institucion_id'])
            ? Institucion::query()->whereKey($validated['institucion_id'])->first()
            : null;

        if ($this->userId === null) {
            $createUserAccount->handle(
                name: $validated['name'],
                email: $validated['email'],
                password: $validated['password'],
                role: $role,
                institucion: $institucion,
                numeroCelular: filled($validated['numero_celular']) ? $validated['numero_celular'] : null,
                direccion: filled($validated['direccion']) ? $validated['direccion'] : null,
                telefonoOficina: filled($validated['telefono_oficina']) ? $validated['telefono_oficina'] : null,
            );
        } else {
            $updateUserAccount->handle(
                user: User::findOrFail($this->userId),
                name: $validated['name'],
                email: $validated['email'],
                role: $role,
                institucion: $institucion,
                password: $validated['password'] !== null && $validated['password'] !== '' ? $validated['password'] : null,
                numeroCelular: filled($validated['numero_celular']) ? $validated['numero_celular'] : null,
                direccion: filled($validated['direccion']) ? $validated['direccion'] : null,
                telefonoOficina: filled($validated['telefono_oficina']) ? $validated['telefono_oficina'] : null,
            );
        }

        $this->reset($this->formFields);

        Flux::modal('user-form')->close();

        Flux::toast(variant: 'success', text: __('Usuario guardado.'));

        unset($this->users);
    }

    /**
     * @return Collection<int, User>
     */
    #[Computed]
    public function users(): Collection
    {
        return User::query()->whereNot('role', UserRole::Admin)->with('institucion')->latest()->get();
    }

    /**
     * @return Collection<int, Institucion>
     */
    #[Computed]
    public function instituciones(): Collection
    {
        return Institucion::active()->orderBy('nombre')->get();
    }
}
