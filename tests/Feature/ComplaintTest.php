<?php

namespace Tests\Feature;

use App\Models\Citizen;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ComplaintTest extends TestCase
{
    use RefreshDatabase;

    public function test_citizen_can_submit_a_complaint(): void
    {
        $user = User::factory()->create(['role' => 'citizen']);
        $citizen = Citizen::create(['user_id' => $user->id, 'cin' => 'TEST-1', 'first_name' => 'Test', 'last_name' => 'Citizen', 'email' => $user->email]);
        $category = ComplaintCategory::create(['name_fr' => 'Voirie', 'name_en' => 'Roads', 'name_ar' => 'الطرق']);

        $this->actingAs($user)->post('/complaints', [
            'complaint_category_id' => $category->id,
            'subject' => 'Route abîmée',
            'description' => 'Description suffisamment détaillée',
            'priority' => 'high',
            'status' => 'new',
            'channel' => 'web',
        ])->assertRedirect();

        $this->assertDatabaseHas('complaints', ['citizen_id' => $citizen->id, 'subject' => 'Route abîmée']);
        $this->assertDatabaseCount('complaint_histories', 1);
        $this->assertDatabaseCount('citizen_notifications', 1);
    }

    public function test_citizen_cannot_view_another_citizens_complaint(): void
    {
        $user = User::factory()->create(['role' => 'citizen']);
        Citizen::create(['user_id' => $user->id, 'cin' => 'TEST-1', 'first_name' => 'First', 'last_name' => 'Citizen']);
        $other = Citizen::create(['cin' => 'TEST-2', 'first_name' => 'Other', 'last_name' => 'Citizen']);
        $category = ComplaintCategory::create(['name_fr' => 'Voirie', 'name_en' => 'Roads', 'name_ar' => 'الطرق']);
        $complaint = Complaint::create(['reference' => 'REC-OTHER', 'citizen_id' => $other->id, 'complaint_category_id' => $category->id, 'subject' => 'Private', 'description' => 'Private complaint']);

        $this->actingAs($user)->get(route('complaints.show', $complaint))->assertForbidden();
    }
}
