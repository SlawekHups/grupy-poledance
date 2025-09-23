<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Payment;
use App\Models\Lesson;
use App\Models\Group;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PerformanceOptimizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test sprawdzający redukcję zapytań N+1 w UserResource
     */
    public function test_user_resource_n1_queries_optimization()
    {
        // Tworzenie testowych danych
        $group = Group::factory()->create();
        $users = User::factory()->count(10)->create();
        
        // Przypisanie użytkowników do grupy
        foreach ($users as $user) {
            $user->groups()->attach($group);
        }

        // Reset licznika zapytań
        DB::enableQueryLog();
        DB::flushQueryLog();

        // Wykonanie zapytania z eager loading (optymalizowane)
        $usersWithGroups = User::with(['groups', 'payments', 'addresses', 'attendances'])
            ->where('role', '!=', 'admin')
            ->get();

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // Sprawdzenie czy liczba zapytań jest minimalna
        $this->assertLessThanOrEqual(5, $queryCount, 
            "Liczba zapytań powinna być mniejsza lub równa 5, otrzymano: {$queryCount}"
        );

        // Sprawdzenie czy dane są poprawnie załadowane
        $this->assertCount(10, $usersWithGroups);
        $this->assertTrue($usersWithGroups->first()->relationLoaded('groups'));
    }

    /**
     * Test sprawdzający redukcję zapytań N+1 w PaymentResource
     */
    public function test_payment_resource_n1_queries_optimization()
    {
        // Tworzenie testowych danych
        $users = User::factory()->count(5)->create();
        $payments = Payment::factory()->count(20)->create();

        // Reset licznika zapytań
        DB::enableQueryLog();
        DB::flushQueryLog();

        // Wykonanie zapytania z eager loading (optymalizowane)
        $paymentsWithUsers = Payment::with(['user'])
            ->orderBy('paid')
            ->orderByDesc('updated_at')
            ->get();

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // Sprawdzenie czy liczba zapytań jest minimalna
        $this->assertLessThanOrEqual(3, $queryCount, 
            "Liczba zapytań powinna być mniejsza lub równa 3, otrzymano: {$queryCount}"
        );

        // Sprawdzenie czy dane są poprawnie załadowane
        $this->assertCount(20, $paymentsWithUsers);
        $this->assertTrue($paymentsWithUsers->first()->relationLoaded('user'));
    }

    /**
     * Test sprawdzający redukcję zapytań N+1 w LessonResource
     */
    public function test_lesson_resource_n1_queries_optimization()
    {
        // Tworzenie testowych danych
        $group = Group::factory()->create();
        $creator = User::factory()->create();
        $lessons = Lesson::factory()->count(15)->create([
            'group_id' => $group->id,
            'created_by' => $creator->id
        ]);

        // Reset licznika zapytań
        DB::enableQueryLog();
        DB::flushQueryLog();

        // Wykonanie zapytania z eager loading (optymalizowane)
        $lessonsWithRelations = Lesson::with(['group', 'creator'])
            ->orderBy('date', 'asc')
            ->get();

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // Sprawdzenie czy liczba zapytań jest minimalna
        $this->assertLessThanOrEqual(3, $queryCount, 
            "Liczba zapytań powinna być mniejsza lub równa 3, otrzymano: {$queryCount}"
        );

        // Sprawdzenie czy dane są poprawnie załadowane
        $this->assertCount(15, $lessonsWithRelations);
        $this->assertTrue($lessonsWithRelations->first()->relationLoaded('group'));
        $this->assertTrue($lessonsWithRelations->first()->relationLoaded('creator'));
    }

    /**
     * Test sprawdzający czy indeksy zostały dodane
     */
    public function test_database_indexes_exist()
    {
        // Sprawdzenie indeksów w tabeli users
        $userIndexes = DB::select("SHOW INDEX FROM users WHERE Key_name LIKE 'users_%'");
        $this->assertGreaterThan(0, count($userIndexes), 'Brak indeksów w tabeli users');

        // Sprawdzenie indeksów w tabeli payments
        $paymentIndexes = DB::select("SHOW INDEX FROM payments WHERE Key_name LIKE 'payments_%'");
        $this->assertGreaterThan(0, count($paymentIndexes), 'Brak indeksów w tabeli payments');

        // Sprawdzenie indeksów w tabeli lessons
        $lessonIndexes = DB::select("SHOW INDEX FROM lessons WHERE Key_name LIKE 'lessons_%'");
        $this->assertGreaterThan(0, count($lessonIndexes), 'Brak indeksów w tabeli lessons');

        // Sprawdzenie indeksów w tabeli group_user
        $groupUserIndexes = DB::select("SHOW INDEX FROM group_user WHERE Key_name LIKE 'group_user_%'");
        $this->assertGreaterThan(0, count($groupUserIndexes), 'Brak indeksów w tabeli group_user');
    }

    /**
     * Test sprawdzający wydajność zapytań z indeksami
     */
    public function test_indexed_queries_performance()
    {
        // Tworzenie testowych danych
        User::factory()->count(100)->create(['role' => 'user', 'is_active' => true]);
        User::factory()->count(50)->create(['role' => 'user', 'is_active' => false]);
        
        Payment::factory()->count(200)->create(['paid' => false]);
        Payment::factory()->count(100)->create(['paid' => true]);

        // Test zapytania z indeksem na role
        $startTime = microtime(true);
        $activeUsers = User::where('role', '!=', 'admin')->get();
        $roleQueryTime = microtime(true) - $startTime;

        // Test zapytania z indeksem na is_active
        $startTime = microtime(true);
        $activeUsers = User::where('is_active', true)->get();
        $activeQueryTime = microtime(true) - $startTime;

        // Test zapytania z indeksem na paid
        $startTime = microtime(true);
        $unpaidPayments = Payment::where('paid', false)->get();
        $paidQueryTime = microtime(true) - $startTime;

        // Sprawdzenie czy zapytania są szybkie (mniej niż 100ms)
        $this->assertLessThan(0.1, $roleQueryTime, 'Zapytanie z indeksem role jest zbyt wolne');
        $this->assertLessThan(0.1, $activeQueryTime, 'Zapytanie z indeksem is_active jest zbyt wolne');
        $this->assertLessThan(0.1, $paidQueryTime, 'Zapytanie z indeksem paid jest zbyt wolne');
    }
}
