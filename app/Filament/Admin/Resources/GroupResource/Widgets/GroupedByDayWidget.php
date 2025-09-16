<?php

namespace App\Filament\Admin\Resources\GroupResource\Widgets;

use App\Models\Group;
use Filament\Widgets\Widget;

class GroupedByDayWidget extends Widget
{
    protected static string $view = 'filament.admin.groups.grouped-by-day-widget';

    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 2;
    
    protected $listeners = ['refreshWidget' => '$refresh'];


    public function getViewData(): array
    {
        $groups = Group::withCount('members')->get();
        
        // Grupuj grupy według dnia tygodnia
        $groupedByDay = [];
        
        foreach ($groups as $group) {
            $dayName = $this->extractDayFromGroupName($group->name);
            $groupedByDay[$dayName][] = $group;
        }
        
        // Sortuj dni tygodnia
        $dayOrder = ['poniedziałek', 'wtorek', 'środa', 'czwartek', 'piątek', 'sobota', 'niedziela', 'inne'];
        $sortedGroupedByDay = [];
        
        foreach ($dayOrder as $day) {
            if (isset($groupedByDay[$day])) {
                $sortedGroupedByDay[$day] = $groupedByDay[$day];
            }
        }
        
        return ['groupedByDay' => $sortedGroupedByDay];
    }

    private function extractDayFromGroupName(string $groupName): string
    {
        $groupName = strtolower(trim($groupName));
        
        // Sprawdź różne warianty środy (z i bez polskich znaków)
        if (str_contains($groupName, 'poniedziałek') || str_contains($groupName, 'pon')) {
            return 'poniedziałek';
        } elseif (str_contains($groupName, 'wtorek') || str_contains($groupName, 'wt')) {
            return 'wtorek';
        } elseif (str_contains($groupName, 'środa') || str_contains($groupName, 'śr') || 
                  str_contains($groupName, 'sroda') || str_contains($groupName, 'sr') ||
                  str_contains($groupName, 'Środa') || str_contains($groupName, 'Śr')) {
            return 'środa';
        } elseif (str_contains($groupName, 'czwartek') || str_contains($groupName, 'czw')) {
            return 'czwartek';
        } elseif (str_contains($groupName, 'piątek') || str_contains($groupName, 'pt')) {
            return 'piątek';
        } elseif (str_contains($groupName, 'sobota') || str_contains($groupName, 'sob')) {
            return 'sobota';
        } elseif (str_contains($groupName, 'niedziela') || str_contains($groupName, 'nd')) {
            return 'niedziela';
        }
        
        return 'inne';
    }

}
