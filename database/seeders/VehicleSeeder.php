<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Imagens de carros (Pexels URLs livres de copyright)
        $imgs = [
            'https://images.pexels.com/photos/170811/pexels-photo-170811.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/1149137/pexels-photo-1149137.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/120049/pexels-photo-120049.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/1007410/pexels-photo-1007410.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/116675/pexels-photo-116675.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/210019/pexels-photo-210019.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/244206/pexels-photo-244206.jpeg?auto=compress&cs=tinysrgb&w=800',
            'https://images.pexels.com/photos/337909/pexels-photo-337909.jpeg?auto=compress&cs=tinysrgb&w=800',
        ];

        $vehicles = [
            // ─── SUVs ─────────────────────────────────────────────
            ['ABC1D23','Volkswagen','T-Cross','1.0 200 TSI Comfortline',2023,2024,12500,'Flex','Automático','1.0 TSI','Branco',4,'SUV',115000,122000,5.7,['Ar Condicionado Digital','Direção Elétrica','Sensor de Ré','Câmera 360°','Vidros Elétricos','Multimídia 8"','Start-Stop','Controle de Tração'],'Pátio São Paulo','disponivel',false,true,true,false],
            ['XYZ9A88','Jeep','Compass','2.0 Longitude TD350 4x4',2022,2022,45000,'Diesel','Automático','2.0 Turbo','Preto',4,'SUV',135000,148000,8.8,['Bancos de Couro','Teto Solar Panorâmico','Painel TFT 10"','Piloto Automático','Sensores Dianteiros','Park Assist','Ar Digital Dual Zone'],'Pátio Curitiba','disponivel',true,true,false,false],
            ['JKL3M56','Hyundai','Creta','2.0 Ultimate',2023,2024,8000,'Flex','Automático','2.0 16V','Cinza','4','SUV',128000,135000,5.2,['Teto Solar','Central Multimídia 10.25"','Carregador Wireless','Frenagem Autônoma','Alerta Ponto Cego','Led DRL'],'Pátio São Paulo','disponivel',false,true,true,true],
            ['MNO4P78','Toyota','Corolla Cross','2.0 XRE Hybrid',2023,2024,15000,'Híbrido','CVT','2.0 Hybrid','Branco Pérola',4,'SUV',175000,185000,5.4,['Toyota Safety Sense','Banco Elétrico','JBL Premium Sound','Wireless Android Auto','Keyless Entry','7 Airbags'],'Pátio São Paulo','disponivel',true,true,true,false],

            // ─── Sedans ───────────────────────────────────────────
            ['QRS5T90','Honda','Civic','2.0 EXL CVT',2022,2023,28000,'Flex','CVT','2.0 i-VTEC','Prata',4,'Sedan',139000,145000,4.1,['Bancos de Couro','Teto Solar','Honda Sensing','Painel Digital','Wireless CarPlay','Câmera Multiângulo'],'Pátio Curitiba','disponivel',true,false,false,false],
            ['UVW6X12','Toyota','Corolla','2.0 XEi Dynamic Force',2021,2022,52000,'Flex','CVT','2.0 16V','Cinza Celestial',4,'Sedan',120000,130000,7.7,['Safety Sense','Multimídia 9"','Ar Digital','Keyless','Start-Stop','Controle de Cruzeiro Adaptativo'],'Pátio Belo Horizonte','disponivel',false,true,true,false],

            // ─── Hatches ──────────────────────────────────────────
            ['HYT1234','Chevrolet','Onix','1.0 Turbo LTZ',2021,2022,60000,'Flex','Manual','1.0 Turbo','Prata',4,'Hatch',72000,76500,5.9,['MyLink 8"','Ar Condicionado','Vidros Elétricos','Alarme','OnStar','Wi-Fi Nativo'],'Pátio Belo Horizonte','disponivel',false,false,false,false],
            ['DEF7G89','Volkswagen','Polo','1.0 TSI Highline',2022,2023,35000,'Flex','Automático','1.0 TSI','Vermelho',4,'Hatch',89000,95000,6.3,['Painel Digital','Climatronic','Sensor Chuva','Faróis LED','Multimídia VW Play','Banco Semi-Couro'],'Pátio São Paulo','disponivel',true,false,true,false],
            ['GHI8J01','Fiat','Argo','1.3 Trekking CVT',2023,2024,10000,'Flex','CVT','1.3 Firefly','Azul',4,'Hatch',82000,88000,6.8,['Wireless Mirroring','Ar Condicionado','Rodas 16"','Rack de Teto','Câmera de Ré','Start-Stop'],'Pátio São Paulo','disponivel',false,true,true,true],

            // ─── Pickups ──────────────────────────────────────────
            ['KLM2N34','Fiat','Toro','2.0 Ranch 4x4 Diesel',2023,2024,18000,'Diesel','Automático (9AT)','2.0 Turbo','Verde Selva',4,'Pickup',195000,210000,7.1,['Tração 4x4','Bancos de Couro Ventilados','Tela 10.1"','ACC','Estribos','Santo Antônio','Câmera 360°'],'Pátio Curitiba','disponivel',true,true,false,false],
            ['OPQ3R56','Chevrolet','S10','2.8 LTZ 4x4',2022,2023,40000,'Diesel','Automático','2.8 Duramax','Branco Summit',4,'Pickup',210000,225000,6.7,['MyLink 8"','Bancos Couro','Diferencial Traseiro','Hill Descent','Capota','Estribo Lateral'],'Pátio Belo Horizonte','disponivel',false,true,false,false],

            // ─── Vendidos / Reservados (para demonstrar status) ──
            ['STU4V78','Hyundai','HB20','1.0 Vision',2020,2021,72000,'Flex','Manual','1.0 12V','Branco',4,'Hatch',58000,63000,7.9,['Ar Condicionado','Direção Hidráulica','Vidros Elétricos','Som Bluetooth'],'Pátio São Paulo','vendido',false,false,false,false],
            ['WXY5Z90','Renault','Kwid','1.0 Zen',2022,2022,25000,'Flex','Manual','1.0 SCe','Vermelho',4,'Hatch',49900,54000,7.6,['Multimídia 8"','Ar Condicionado','Sensor de Ré','Vidros Elétricos'],'Pátio São Paulo','reservado',false,false,true,false],

            // ─── Premium ──────────────────────────────────────────
            ['ZAB6C12','BMW','320i','2.0 GP Turbo',2021,2022,30000,'Gasolina','Automático (8AT)','2.0 TwinPower','Preto Safira',4,'Sedan',235000,260000,9.6,['Teto Solar','Bancos Elétricos com Memória','iDrive 7.0','Harman Kardon','LED Adaptativo','Driving Assistant','M Sport Package'],'Pátio São Paulo','disponivel',true,false,false,false],
            ['DEF7G12','Mercedes-Benz','GLA 200','1.3 Turbo Advance',2022,2023,20000,'Gasolina','Automático (7DCT)','1.3 Turbo','Cinza Montanha',4,'SUV',215000,235000,8.5,['MBUX','Teto Panorâmico','Câmera 360°','Iluminação Ambiente 64 cores','Blind Spot','Freios Regenerativos'],'Pátio Curitiba','disponivel',true,true,false,true],
        ];

        foreach ($vehicles as $v) {
            // Sorteia 2-4 imagens para galeria
            $mediaImgs = array_slice($imgs, array_rand($imgs, 1), rand(2, 4));
            if (count($mediaImgs) < 2) $mediaImgs = array_slice($imgs, 0, 3);

            DB::table('vehicles')->insert([
                'plate'                 => $v[0],
                'brand'                 => $v[1],
                'model'                 => $v[2],
                'version'               => $v[3],
                'manufacture_year'      => $v[4],
                'model_year'            => $v[5],
                'mileage'               => $v[6],
                'fuel_type'             => $v[7],
                'transmission'          => $v[8],
                'engine'                => $v[9],
                'color'                 => $v[10],
                'doors'                 => $v[11],
                'category'              => $v[12],
                'sale_price'            => $v[13],
                'fipe_price'            => $v[14],
                'profit_margin'         => $v[15],
                'accessories'           => json_encode($v[16]),
                'location'              => $v[17],
                'status'                => $v[18],
                'has_report'            => $v[19],
                'has_factory_warranty'  => $v[20],
                'is_on_sale'            => $v[21],
                'is_just_arrived'       => $v[22],
                'media'                 => json_encode($mediaImgs),
                'created_at'            => $now->copy()->subDays(rand(0, 30)),
                'updated_at'            => $now,
            ]);
        }
    }
}
