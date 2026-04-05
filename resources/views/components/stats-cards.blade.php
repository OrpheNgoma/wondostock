<div class="grid grid-cols-2 gap-4">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="text-2xl font-bold text-blue-600">{{ $totalLocks }}</div>
        <div class="text-sm text-blue-700">Total des verrouillages</div>
    </div>
    
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="text-2xl font-bold text-red-600">{{ $activeLocks }}</div>
        <div class="text-sm text-red-700">Verrouillages actifs</div>
    </div>
    
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="text-2xl font-bold text-yellow-600">{{ $expiredLocks }}</div>
        <div class="text-sm text-yellow-700">Verrouillages expirés</div>
    </div>
    
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="text-2xl font-bold text-green-600">{{ $companiesWithLocks }}</div>
        <div class="text-sm text-green-700">Entreprises concernées</div>
    </div>
</div>