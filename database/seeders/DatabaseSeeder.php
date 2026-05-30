<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Client;
use App\Models\Product;
use App\Models\Tender;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear admin
        $this->call([
            AdminUserSeeder::class,
        ]);

        // Obtener o crear usuario de prueba
        $testUser = User::where('email', 'test@example.com')->first();
        if (!$testUser) {
            $testUser = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'role' => 'admin',
            ]);
        }

        // Crear clientes de prueba
        $clients = [
            [
                'name' => 'Acme Corporation',
                'email' => 'contact@acme.com',
                'phone' => '555-0101',
                'address' => '123 Business Ave, New York, NY',
                'created_by' => $testUser->id,
            ],
            [
                'name' => 'TechSolutions Inc',
                'email' => 'info@techsol.com',
                'phone' => '555-0102',
                'address' => '456 Tech Park, San Francisco, CA',
                'created_by' => $testUser->id,
            ],
            [
                'name' => 'Global Industries Ltd',
                'email' => 'support@global.com',
                'phone' => '555-0103',
                'address' => '789 Industrial Blvd, Chicago, IL',
                'created_by' => $testUser->id,
            ],
        ];

        $clientModels = [];
        foreach ($clients as $client) {
            $clientModels[] = Client::create($client);
        }

        // Crear productos de prueba
        $products = [
            [
                'sku' => 'PROD-001',
                'name' => 'Laptop Dell Inspiron 15',
                'unit_price' => 799.99,
                'stock' => 25,
                'created_by' => $testUser->id,
            ],
            [
                'sku' => 'PROD-002',
                'name' => 'Monitor LG UltraWide 34"',
                'unit_price' => 549.99,
                'stock' => 15,
                'created_by' => $testUser->id,
            ],
            [
                'sku' => 'PROD-003',
                'name' => 'Teclado Mecánico RGB Corsair',
                'unit_price' => 189.99,
                'stock' => 42,
                'created_by' => $testUser->id,
            ],
            [
                'sku' => 'PROD-004',
                'name' => 'Mouse Logitech MX Master 3',
                'unit_price' => 99.99,
                'stock' => 58,
                'created_by' => $testUser->id,
            ],
            [
                'sku' => 'PROD-005',
                'name' => 'Monitor ASUS ProArt PA328Q 32"',
                'unit_price' => 1299.99,
                'stock' => 8,
                'created_by' => $testUser->id,
            ],
        ];

        $productModels = [];
        foreach ($products as $product) {
            $productModels[] = Product::create($product);
        }

        // Crear licitaciones de prueba
        $tenders = [
            [
                'title' => 'Equipamiento de Oficina - Etapa 1',
                'description' => 'Adquisición de equipos informáticos para la modernización de las estaciones de trabajo de la oficina principal. Incluye laptops, monitores y periféricos.',
                'client_id' => $clientModels[0]->id,
                'max_budget' => 25000.00,
                'total_amount' => 22500.00,
                'status' => 'activa',
                'delivery_deadline' => now()->addDays(30),
                'created_by' => $testUser->id,
            ],
            [
                'title' => 'Infraestructura de Red y Servidores',
                'description' => 'Contratación para la instalación y configuración de infraestructura de red moderna, incluyendo servidores dedicados y soluciones de almacenamiento.',
                'client_id' => $clientModels[1]->id,
                'max_budget' => 45000.00,
                'total_amount' => 42000.00,
                'status' => 'por_cobrar',
                'delivery_deadline' => now()->addDays(45),
                'created_by' => $testUser->id,
            ],
            [
                'title' => 'Renovación de Periféricos y Accesorios',
                'description' => 'Compra de periféricos de oficina incluyendo teclados, ratones, docking stations y cables para 50 estaciones de trabajo.',
                'client_id' => $clientModels[2]->id,
                'max_budget' => 15000.00,
                'total_amount' => 14200.00,
                'status' => 'activa',
                'delivery_deadline' => now()->addDays(20),
                'created_by' => $testUser->id,
            ],
            [
                'title' => 'Solución de Respaldo y Recuperación ante Desastres',
                'description' => 'Implementación de sistema redundante de respaldo con hardware especializados y software de gestión centralizada.',
                'client_id' => $clientModels[0]->id,
                'max_budget' => 65000.00,
                'total_amount' => 0.00,
                'status' => 'finalizada',
                'delivery_deadline' => now()->subDays(10),
                'created_by' => $testUser->id,
            ],
        ];

        $tenderModels = [];
        foreach ($tenders as $tender) {
            $tenderModels[] = Tender::create($tender);
        }

        // Asociar productos a licitaciones (Relación Muchos a Muchos)
        if (count($tenderModels) > 0 && count($productModels) > 0) {
            // Licitación 1: Laptops y Monitores
            $tenderModels[0]->products()->attach($productModels[0], [
                'quantity' => 10,
                'unit_price' => $productModels[0]->unit_price,
                'added_by' => $testUser->id,
            ]);
            $tenderModels[0]->products()->attach($productModels[1], [
                'quantity' => 15,
                'unit_price' => $productModels[1]->unit_price,
                'added_by' => $testUser->id,
            ]);

            // Licitación 2: Todos los periféricos
            for ($i = 0; $i < count($productModels); $i++) {
                $tenderModels[1]->products()->attach($productModels[$i], [
                    'quantity' => rand(5, 20),
                    'unit_price' => $productModels[$i]->unit_price,
                    'added_by' => $testUser->id,
                ]);
            }

            // Licitación 3: Solo periféricos
            $tenderModels[2]->products()->attach($productModels[2], [
                'quantity' => 50,
                'unit_price' => $productModels[2]->unit_price,
                'added_by' => $testUser->id,
            ]);
            $tenderModels[2]->products()->attach($productModels[3], [
                'quantity' => 50,
                'unit_price' => $productModels[3]->unit_price,
                'added_by' => $testUser->id,
            ]);
        }
    }
}
