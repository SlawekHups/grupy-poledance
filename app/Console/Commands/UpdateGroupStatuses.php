<?php

namespace App\Console\Commands;

use App\Models\Group;
use Illuminate\Console\Command;

class UpdateGroupStatuses extends Command
{
    protected $signature = 'groups:update-statuses';
    protected $description = 'Aktualizuje statusy grup na podstawie liczby członków';

    public function handle()
    {
        $this->info('Aktualizacja statusów grup...');
        
        $updated = 0;
        $groups = Group::all();
        
        foreach ($groups as $group) {
            $oldStatus = $group->status;
            $group->updateStatusBasedOnCapacity();
            $newStatus = $group->fresh()->status;
            
            if ($oldStatus !== $newStatus) {
                $this->line("Grupa '{$group->name}': {$oldStatus} → {$newStatus}");
                $updated++;
            }
        }
        
        $this->info("Zaktualizowano {$updated} grup.");
        
        // Podsumowanie
        $fullGroups = Group::where('status', 'full')->count();
        $activeGroups = Group::where('status', 'active')->count();
        $inactiveGroups = Group::where('status', 'inactive')->count();
        
        $this->table(
            ['Status', 'Liczba grup'],
            [
                ['Pełne', $fullGroups],
                ['Aktywne', $activeGroups],
                ['Nieaktywne', $inactiveGroups],
            ]
        );
        
        return Command::SUCCESS;
    }
}