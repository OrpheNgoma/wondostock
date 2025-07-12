<div>
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900">Rôles & Permissions</h2>
            <p class="mt-1 text-sm text-gray-500">Définissez ce que chaque rôle peut faire dans l'application.</p>
        </div>
    </div>

    <div class="mt-10 grid grid-cols-1 gap-x-8 gap-y-8 md:grid-cols-3">
        <div class="md:col-span-1">
            <div class="bg-white p-4 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Rôles</h3>
                <div class="mt-4 space-y-2">
                    @foreach($roles as $role)
                        <button wire:click="selectRole({{ $role->id }})" class="w-full text-left p-3 rounded-lg {{ $selectedRole?->id === $role->id ? 'bg-indigo-600 text-white' : 'hover:bg-gray-100' }}">
                            {{ $role->name }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="md:col-span-2">
            @if($selectedRole)
            <div class="bg-white p-6 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                 <h3 class="text-lg font-semibold leading-6 text-gray-900">Permissions pour : {{ $selectedRole->name }}</h3>
                 <div class="mt-6 space-y-6">
                    @foreach($permissions as $group => $permissionList)
                    <div>
                        <h4 class="text-base font-medium text-gray-800 capitalize border-b pb-2">{{ $group }}</h4>
                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($permissionList as $permission)
                            <div class="relative flex items-start">
                                <div class="flex h-6 items-center">
                                    <input wire:model="rolePermissions" id="permission-{{$permission->id}}" value="{{$permission->id}}" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </div>
                                <div class="ml-3 text-sm leading-6">
                                    <label for="permission-{{$permission->id}}" class="font-medium text-gray-900">{{ $permission->name }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                 </div>
                 <div class="mt-8 flex justify-end">
                     <button wire:click="savePermissions" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Sauvegarder les Permissions</button>
                 </div>
            </div>
            @else
            <div class="text-center py-20">
                <p class="text-gray-500">Veuillez sélectionner un rôle pour voir ses permissions.</p>
            </div>
            @endif
        </div>
    </div>
</div>