<?php

namespace Database\Seeders;

use App\Models\Citizen;
use App\Models\CitizenNotification;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\ComplaintHistory;
use App\Models\Employee;
use App\Models\MunicipalService;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create(['name' => 'Administrateur Ahfir', 'email' => 'admin@ahfir.ma', 'role' => 'admin', 'password' => 'password']);
        $employeeUser = User::create(['name' => 'Nadia El Amrani', 'email' => 'agent@ahfir.ma', 'role' => 'employee', 'password' => 'password']);
        $citizenUser = User::create(['name' => 'Amine Alaoui', 'email' => 'citoyen@ahfir.ma', 'role' => 'citizen', 'password' => 'password']);

        $technical = MunicipalService::create(['name' => 'Services techniques', 'code' => 'TECH', 'description' => 'Voirie, éclairage et infrastructures.']);
        $cleanliness = MunicipalService::create(['name' => 'Propreté et environnement', 'code' => 'ENV', 'description' => 'Collecte et espaces publics.']);
        $adminService = MunicipalService::create(['name' => 'Administration générale', 'code' => 'ADM', 'description' => 'Accueil et coordination.']);

        $employee = Employee::create(['user_id' => $employeeUser->id, 'municipal_service_id' => $technical->id, 'employee_number' => 'EMP-001', 'first_name' => 'Nadia', 'last_name' => 'El Amrani', 'position' => 'Responsable technique', 'email' => 'agent@ahfir.ma', 'phone' => '0600000001', 'hire_date' => '2021-03-15']);
        $citizen = Citizen::create(['user_id' => $citizenUser->id, 'cin' => 'FA123456', 'first_name' => 'Amine', 'last_name' => 'Alaoui', 'birth_date' => '1990-04-12', 'email' => 'citoyen@ahfir.ma', 'phone' => '0612345678', 'address' => 'Quartier Al Qods, Ahfir']);
        Citizen::create(['cin' => 'FA654321', 'first_name' => 'Salma', 'last_name' => 'Idrissi', 'email' => 'salma@example.com', 'phone' => '0687654321', 'address' => 'Centre-ville, Ahfir']);

        $road = ComplaintCategory::create(['name_fr' => 'Voirie', 'name_en' => 'Roads', 'name_ar' => 'الطرق', 'color' => '#ff5d8f', 'icon' => 'bi-cone-striped']);
        $lighting = ComplaintCategory::create(['name_fr' => 'Éclairage public', 'name_en' => 'Street lighting', 'name_ar' => 'الإنارة العمومية', 'color' => '#ffb703', 'icon' => 'bi-lightbulb-fill']);
        ComplaintCategory::create(['name_fr' => 'Propreté', 'name_en' => 'Cleanliness', 'name_ar' => 'النظافة', 'color' => '#00c2a8', 'icon' => 'bi-recycle']);
        ComplaintCategory::create(['name_fr' => 'Nuisances', 'name_en' => 'Nuisances', 'name_ar' => 'الإزعاج', 'color' => '#7c3aed', 'icon' => 'bi-volume-up-fill']);

        $complaint = Complaint::create(['reference' => 'REC-202606-DEMO01', 'citizen_id' => $citizen->id, 'complaint_category_id' => $lighting->id, 'municipal_service_id' => $technical->id, 'employee_id' => $employee->id, 'subject' => 'Lampadaire en panne', 'description' => 'Le lampadaire près de la place Al Qods ne fonctionne plus.', 'location' => 'Quartier Al Qods', 'priority' => 'high', 'status' => 'processing', 'channel' => 'web']);
        Complaint::create(['reference' => 'REC-202606-DEMO02', 'citizen_id' => $citizen->id, 'complaint_category_id' => $road->id, 'municipal_service_id' => $technical->id, 'subject' => 'Nid-de-poule dangereux', 'description' => 'Un nid-de-poule important gêne la circulation.', 'location' => 'Avenue Hassan II', 'priority' => 'urgent', 'status' => 'new', 'channel' => 'web']);
        ComplaintHistory::create(['complaint_id' => $complaint->id, 'user_id' => $citizenUser->id, 'action' => 'created', 'new_status' => 'new', 'comment' => 'Réclamation déposée depuis le portail citoyen.']);
        ComplaintHistory::create(['complaint_id' => $complaint->id, 'user_id' => $admin->id, 'action' => 'status_changed', 'old_status' => 'new', 'new_status' => 'processing', 'comment' => 'Attribution au service technique.']);
        CitizenNotification::create(['citizen_id' => $citizen->id, 'complaint_id' => $complaint->id, 'title' => 'Réclamation prise en charge', 'message' => 'Votre réclamation REC-202606-DEMO01 est en cours de traitement.']);
    }
}
