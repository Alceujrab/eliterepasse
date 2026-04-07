<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            [
                'plate' => 'ABC1D23',
                'brand' => 'Volkswagen',
                'model' => 'T-Cross',
                'version' => '1.0 200 TSI 12V TOTAL FLEX AUTOMÁTICO',
                'manufacture_year' => 2023,
                'model_year' => 2024,
                'mileage' => 12500,
                'fuel_type' => 'Flex',
                'transmission' => 'Automático',
                'color' => 'Branco',
                'category' => 'SUV',
                'sale_price' => 115000.00,
                'fipe_price' => 122000.00,
                'profit_margin' => 7000.00,
                'accessories' => json_encode(['Ar Condicionado', 'Direção Elétrica', 'Sensor de Ré', 'Câmera', 'Vidros Elétricos', 'Multimídia']),
                'media' => json_encode(['https://images.pexels.com/photos/1149137/pexels-photo-1149137.jpeg?auto=compress&cs=tinysrgb&w=800', 'https://images.pexels.com/photos/170811/pexels-photo-170811.jpeg?auto=compress&cs=tinysrgb&w=800']),
                'location' => json_encode(['city' => 'São Paulo', 'state' => 'SP']),
                'status' => 'available',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'plate' => 'XYZ9A88',
                'brand' => 'Jeep',
                'model' => 'Compass',
                'version' => '2.0 16V FLEX LONGITUDE',
                'manufacture_year' => 2022,
                'model_year' => 2022,
                'mileage' => 45000,
                'fuel_type' => 'Flex',
                'transmission' => 'Automático',
                'color' => 'Preto',
                'category' => 'SUV',
                'sale_price' => 135000.00,
                'fipe_price' => 140000.00,
                'profit_margin' => 5000.00,
                'accessories' => json_encode(['Ar Condicionado Digital', 'Bancos de Couro', 'Teto Solar', 'Painel TFT']),
                'media' => json_encode(['https://images.pexels.com/photos/120049/pexels-photo-120049.jpeg?auto=compress&cs=tinysrgb&w=800']),
                'location' => json_encode(['city' => 'Curitiba', 'state' => 'PR']),
                'status' => 'available',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'plate' => 'HYT1234',
                'brand' => 'Chevrolet',
                'model' => 'Onix',
                'version' => '1.0 TURBO LTZ',
                'manufacture_year' => 2021,
                'model_year' => 2022,
                'mileage' => 60000,
                'fuel_type' => 'Flex',
                'transmission' => 'Manual',
                'color' => 'Prata',
                'category' => 'Hatch',
                'sale_price' => 72000.00,
                'fipe_price' => 76500.00,
                'profit_margin' => 4500.00,
                'accessories' => json_encode(['MyLink', 'Ar Condicionado', 'Vidros Elétricos', 'Alarme']),
                'media' => json_encode(['https://images.pexels.com/photos/1007410/pexels-photo-1007410.jpeg?auto=compress&cs=tinysrgb&w=800']),
                'location' => json_encode(['city' => 'Belo Horizonte', 'state' => 'MG']),
                'status' => 'available',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        DB::table('vehicles')->insert($vehicles);
    }
}
