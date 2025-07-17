<div class="w-full flex flex-col md:flex-row gap-6 justify-center my-6">
    <div class="flex-1 bg-green-100 rounded-xl shadow p-6 py-10 flex flex-col items-center text-center justify-center">
        <div class="text-3xl font-bold mb-1" style="color: #16a34a;">{{ $presentCount }}</div>
        <div class="mt-2 font-semibold" style="color: #166534;">Obecności</div>
    </div>
    <div class="flex-1 bg-red-100 rounded-xl shadow p-6 py-10 flex flex-col items-center text-center justify-center">
        <div class="text-3xl font-bold mb-1" style="color: #dc2626;">{{ $absentCount }}</div>
        <div class="mt-2 font-semibold" style="color: #991b1b;">Nieobecności</div>
    </div>
    <div class="flex-1 bg-blue-100 rounded-xl shadow p-6 py-10 flex flex-col items-center text-center justify-center">
        <div class="text-3xl font-bold mb-1" style="color: #2563eb;">{{ $percent }}%</div>
        <div class="mt-2 font-semibold" style="color: #1e40af;">Frekwencja</div>
    </div>
</div> 