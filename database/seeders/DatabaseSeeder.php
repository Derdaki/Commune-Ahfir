<?php

namespace Database\Seeders;

use App\Models\AdministrativeRequest;
use App\Models\Citizen;
use App\Models\CitizenNotification;
use App\Models\Employee;
use App\Models\MunicipalService;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create(['name' => 'Administrateur Ahfir', 'email' => 'admin@ahfir.ma', 'role' => 'admin', 'password' => 'password']);
        User::create(['name' => 'Agent communal', 'email' => 'agent@ahfir.ma', 'role' => 'agent', 'password' => 'password']);

        $civil = MunicipalService::create(['name' => 'État civil', 'code' => 'EC', 'description' => 'Actes et certificats d’état civil.']);
        $urbanisme = MunicipalService::create(['name' => 'Urbanisme', 'code' => 'URB', 'description' => 'Autorisations et dossiers d’urbanisme.']);
        MunicipalService::create(['name' => 'Affaires sociales', 'code' => 'SOC', 'description' => 'Accompagnement et attestations sociales.']);

        $employee = Employee::create([
            'municipal_service_id' => $civil->id, 'employee_number' => 'EMP-001', 'first_name' => 'Nadia',
            'last_name' => 'El Amrani', 'position' => 'Responsable état civil', 'email' => 'nadia@ahfir.ma',
            'phone' => '0600000001', 'hire_date' => '2021-03-15',
        ]);
        Employee::create([
            'municipal_service_id' => $urbanisme->id, 'employee_number' => 'EMP-002', 'first_name' => 'Youssef',
            'last_name' => 'Bennani', 'position' => 'Agent urbanisme', 'email' => 'youssef@ahfir.ma',
            'phone' => '0600000002', 'hire_date' => '2022-09-01',
        ]);

        $citizen = Citizen::create([
            'cin' => 'FA123456', 'first_name' => 'Amine', 'last_name' => 'Alaoui', 'birth_date' => '1990-04-12',
            'email' => 'amine@example.com', 'phone' => '0612345678', 'address' => 'Quartier Al Qods, Ahfir',
        ]);
        Citizen::create([
            'cin' => 'FA654321', 'first_name' => 'Salma', 'last_name' => 'Idrissi', 'birth_date' => '1987-11-03',
            'email' => 'salma@example.com', 'phone' => '0687654321', 'address' => 'Centre-ville, Ahfir',
        ]);

        $request = AdministrativeRequest::create([
            'reference' => 'AHF-DEMO-001', 'citizen_id' => $citizen->id, 'municipal_service_id' => $civil->id,
            'employee_id' => $employee->id, 'type' => 'Extrait d’acte de naissance', 'description' => 'Deux copies en français.',
            'status' => 'processing', 'submitted_at' => now()->subDays(2),
        ]);
        CitizenNotification::create([
            'citizen_id' => $citizen->id, 'administrative_request_id' => $request->id,
            'title' => 'Demande prise en charge', 'message' => 'Votre demande AHF-DEMO-001 est en cours de traitement.',
        ]);
    }
}
