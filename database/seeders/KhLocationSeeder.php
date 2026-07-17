<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KhLocationSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('kh_villages')->truncate();
        DB::table('kh_communes')->truncate();
        DB::table('kh_districts')->truncate();
        DB::table('kh_provinces')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = now();

        // ─── PROVINCES ──────────────────────────────────────────────────────
        $provinces = [
            ['code'=>'KH-1', 'name'=>'Banteay Meanchey','name_km'=>'បន្ទាយមានជ័យ','type'=>'province'],
            ['code'=>'KH-2', 'name'=>'Battambang',       'name_km'=>'បាត់ដំបង',    'type'=>'province'],
            ['code'=>'KH-3', 'name'=>'Kampong Cham',     'name_km'=>'កំពង់ចាម',    'type'=>'province'],
            ['code'=>'KH-4', 'name'=>'Kampong Chhnang',  'name_km'=>'កំពង់ឆ្នាំង', 'type'=>'province'],
            ['code'=>'KH-5', 'name'=>'Kampong Speu',     'name_km'=>'កំពង់ស្ពឺ',   'type'=>'province'],
            ['code'=>'KH-6', 'name'=>'Kampong Thom',     'name_km'=>'កំពង់ធំ',     'type'=>'province'],
            ['code'=>'KH-7', 'name'=>'Kampot',           'name_km'=>'កំពត',        'type'=>'province'],
            ['code'=>'KH-8', 'name'=>'Kandal',           'name_km'=>'កណ្ដាល',      'type'=>'province'],
            ['code'=>'KH-9', 'name'=>'Kep',              'name_km'=>'កែប',         'type'=>'province'],
            ['code'=>'KH-10','name'=>'Koh Kong',         'name_km'=>'កោះកុង',      'type'=>'province'],
            ['code'=>'KH-11','name'=>'Kratié',           'name_km'=>'ក្រចេះ',      'type'=>'province'],
            ['code'=>'KH-12','name'=>'Mondulkiri',       'name_km'=>'មណ្ឌលគិរី',  'type'=>'province'],
            ['code'=>'KH-13','name'=>'Oddar Meanchey',   'name_km'=>'ឧត្ដរមានជ័យ','type'=>'province'],
            ['code'=>'KH-14','name'=>'Pailin',           'name_km'=>'បៃលិន',       'type'=>'province'],
            ['code'=>'KH-15','name'=>'Phnom Penh',       'name_km'=>'ភ្នំពេញ',     'type'=>'city'],
            ['code'=>'KH-16','name'=>'Preah Sihanouk',   'name_km'=>'ព្រះសីហនុ',  'type'=>'province'],
            ['code'=>'KH-17','name'=>'Preah Vihear',     'name_km'=>'ព្រះវិហារ',  'type'=>'province'],
            ['code'=>'KH-18','name'=>'Prey Veng',        'name_km'=>'ព្រៃវែង',    'type'=>'province'],
            ['code'=>'KH-19','name'=>'Pursat',           'name_km'=>'ពោធិ៍សាត',   'type'=>'province'],
            ['code'=>'KH-20','name'=>'Ratanakiri',       'name_km'=>'រតនគិរី',    'type'=>'province'],
            ['code'=>'KH-21','name'=>'Siem Reap',        'name_km'=>'សៀមរាប',      'type'=>'province'],
            ['code'=>'KH-22','name'=>'Stung Treng',      'name_km'=>'ស្ទឹងត្រែង', 'type'=>'province'],
            ['code'=>'KH-23','name'=>'Svay Rieng',       'name_km'=>'ស្វាយរៀង',   'type'=>'province'],
            ['code'=>'KH-24','name'=>'Takéo',            'name_km'=>'តាកែវ',       'type'=>'province'],
            ['code'=>'KH-25','name'=>'Tboung Khmum',     'name_km'=>'ត្បូងឃ្មុំ', 'type'=>'province'],
        ];
        foreach ($provinces as $p) {
            DB::table('kh_provinces')->insert(array_merge($p, ['created_at'=>$now,'updated_at'=>$now]));
        }

        // ─── DISTRICTS ──────────────────────────────────────────────────────
        $districts = [
            // KH-1 Banteay Meanchey
            ['code'=>'KH-1-01','province_code'=>'KH-1', 'name'=>'Mongkol Borei',     'name_km'=>'មង្គលបុរី',    'type'=>'district'],
            ['code'=>'KH-1-02','province_code'=>'KH-1', 'name'=>'Phnum Srok',        'name_km'=>'ភ្នំស្រុក',    'type'=>'district'],
            ['code'=>'KH-1-03','province_code'=>'KH-1', 'name'=>'Preah Netr Preah',  'name_km'=>'ព្រះនេត្រព្រះ','type'=>'district'],
            ['code'=>'KH-1-04','province_code'=>'KH-1', 'name'=>'Serei Saophoan',    'name_km'=>'សិរីសោភ័ណ',  'type'=>'municipality'],
            ['code'=>'KH-1-05','province_code'=>'KH-1', 'name'=>'Svay Chek',         'name_km'=>'ស្វាយជ្រែក',  'type'=>'district'],
            ['code'=>'KH-1-06','province_code'=>'KH-1', 'name'=>'Thmar Puok',        'name_km'=>'ថ្មពួក',      'type'=>'district'],
            // KH-2 Battambang
            ['code'=>'KH-2-01','province_code'=>'KH-2', 'name'=>'Battambang',        'name_km'=>'បាត់ដំបង',    'type'=>'municipality'],
            ['code'=>'KH-2-02','province_code'=>'KH-2', 'name'=>'Banan',             'name_km'=>'បាណន',        'type'=>'district'],
            ['code'=>'KH-2-03','province_code'=>'KH-2', 'name'=>'Ek Phnom',          'name_km'=>'ឯកភ្នំ',      'type'=>'district'],
            ['code'=>'KH-2-04','province_code'=>'KH-2', 'name'=>'Kamrieng',          'name_km'=>'កំរៀង',       'type'=>'district'],
            ['code'=>'KH-2-05','province_code'=>'KH-2', 'name'=>'Moung Ruessei',     'name_km'=>'មូលរំស្សី',   'type'=>'district'],
            ['code'=>'KH-2-06','province_code'=>'KH-2', 'name'=>'Phnum Proek',       'name_km'=>'ភ្នំព្រឹក',  'type'=>'district'],
            ['code'=>'KH-2-07','province_code'=>'KH-2', 'name'=>'Rotanak Mondol',    'name_km'=>'រតនៈមណ្ឌល',  'type'=>'district'],
            ['code'=>'KH-2-08','province_code'=>'KH-2', 'name'=>'Samlout',           'name_km'=>'សំឡូត',      'type'=>'district'],
            ['code'=>'KH-2-09','province_code'=>'KH-2', 'name'=>'Sampov Loun',       'name_km'=>'សំបួរលូន',   'type'=>'district'],
            ['code'=>'KH-2-10','province_code'=>'KH-2', 'name'=>'Sangkae',           'name_km'=>'សង្កែ',      'type'=>'district'],
            ['code'=>'KH-2-11','province_code'=>'KH-2', 'name'=>'Thma Koul',         'name_km'=>'ថ្មគោល',     'type'=>'district'],
            // KH-3 Kampong Cham
            ['code'=>'KH-3-01','province_code'=>'KH-3', 'name'=>'Batheay',           'name_km'=>'បាធាយ',      'type'=>'district'],
            ['code'=>'KH-3-02','province_code'=>'KH-3', 'name'=>'Chamkar Leu',       'name_km'=>'ចំការលើ',    'type'=>'district'],
            ['code'=>'KH-3-03','province_code'=>'KH-3', 'name'=>'Cheung Prey',       'name_km'=>'ជើងព្រៃ',   'type'=>'district'],
            ['code'=>'KH-3-04','province_code'=>'KH-3', 'name'=>'Kampong Cham',      'name_km'=>'កំពង់ចាម',   'type'=>'municipality'],
            ['code'=>'KH-3-05','province_code'=>'KH-3', 'name'=>'Kampong Siem',      'name_km'=>'កំពង់សៀម',   'type'=>'district'],
            ['code'=>'KH-3-06','province_code'=>'KH-3', 'name'=>'Kang Meas',         'name_km'=>'កាំងម្អស',   'type'=>'district'],
            ['code'=>'KH-3-07','province_code'=>'KH-3', 'name'=>'Koh Sotin',         'name_km'=>'កោះសូទិន',  'type'=>'district'],
            ['code'=>'KH-3-08','province_code'=>'KH-3', 'name'=>'Prey Chhor',        'name_km'=>'ព្រៃឈរ',    'type'=>'district'],
            ['code'=>'KH-3-09','province_code'=>'KH-3', 'name'=>'Srey Santhor',      'name_km'=>'ស្រីសន្ធរ', 'type'=>'district'],
            ['code'=>'KH-3-10','province_code'=>'KH-3', 'name'=>'Stueng Trang',      'name_km'=>'ស្ទឹងត្រង់','type'=>'district'],
            // KH-7 Kampot
            ['code'=>'KH-7-01','province_code'=>'KH-7', 'name'=>'Angkor Chey',       'name_km'=>'អង្គរជ័យ',   'type'=>'district'],
            ['code'=>'KH-7-02','province_code'=>'KH-7', 'name'=>'Banteay Meas',      'name_km'=>'បន្ទាយមាស',  'type'=>'district'],
            ['code'=>'KH-7-03','province_code'=>'KH-7', 'name'=>'Chhouk',            'name_km'=>'ឈូក',        'type'=>'district'],
            ['code'=>'KH-7-04','province_code'=>'KH-7', 'name'=>'Dang Tong',         'name_km'=>'ដំណង',       'type'=>'district'],
            ['code'=>'KH-7-05','province_code'=>'KH-7', 'name'=>'Kampot',            'name_km'=>'កំពត',       'type'=>'municipality'],
            ['code'=>'KH-7-06','province_code'=>'KH-7', 'name'=>'Kompong Trach',     'name_km'=>'កំពង់ត្រាច','type'=>'district'],
            ['code'=>'KH-7-07','province_code'=>'KH-7', 'name'=>'Tuek Chhou',        'name_km'=>'ទឹកឈូ',     'type'=>'district'],
            // KH-8 Kandal
            ['code'=>'KH-8-01','province_code'=>'KH-8', 'name'=>'Angk Snuol',        'name_km'=>'អង្កស្នួល', 'type'=>'district'],
            ['code'=>'KH-8-02','province_code'=>'KH-8', 'name'=>'Kandal Stueng',     'name_km'=>'កណ្ដាលស្ទឹង','type'=>'district'],
            ['code'=>'KH-8-03','province_code'=>'KH-8', 'name'=>'Khsach Kandal',     'name_km'=>'ល្ហកកណ្ដាល','type'=>'district'],
            ['code'=>'KH-8-04','province_code'=>'KH-8', 'name'=>'Koh Thom',          'name_km'=>'កោះធំ',      'type'=>'district'],
            ['code'=>'KH-8-05','province_code'=>'KH-8', 'name'=>'Leuk Daek',         'name_km'=>'ឡើកដែក',    'type'=>'district'],
            ['code'=>'KH-8-06','province_code'=>'KH-8', 'name'=>'Lvea Aem',          'name_km'=>'ល្វាអែម',    'type'=>'district'],
            ['code'=>'KH-8-07','province_code'=>'KH-8', 'name'=>'Muk Kampoul',       'name_km'=>'មុខកំពូល',  'type'=>'district'],
            ['code'=>'KH-8-08','province_code'=>'KH-8', 'name'=>'Ponhea Lueu',       'name_km'=>'ពញាឡើ',     'type'=>'district'],
            ['code'=>'KH-8-09','province_code'=>'KH-8', 'name'=>'S Aang',            'name_km'=>'ស្អាង',      'type'=>'district'],
            ['code'=>'KH-8-10','province_code'=>'KH-8', 'name'=>'Ta Khmau',          'name_km'=>'តាខ្មៅ',     'type'=>'municipality'],
            // KH-15 Phnom Penh
            ['code'=>'KH-15-01','province_code'=>'KH-15','name'=>'Chamkar Mon',      'name_km'=>'ចំការមន',    'type'=>'khan'],
            ['code'=>'KH-15-02','province_code'=>'KH-15','name'=>'Doun Penh',        'name_km'=>'ដូនពេញ',     'type'=>'khan'],
            ['code'=>'KH-15-03','province_code'=>'KH-15','name'=>'Prampir Meakkakra','name_km'=>'ប្រំពីរ មករា','type'=>'khan'],
            ['code'=>'KH-15-04','province_code'=>'KH-15','name'=>'Tuol Kouk',        'name_km'=>'ទួលគោក',     'type'=>'khan'],
            ['code'=>'KH-15-05','province_code'=>'KH-15','name'=>'Dangkao',          'name_km'=>'ដងកោ',       'type'=>'khan'],
            ['code'=>'KH-15-06','province_code'=>'KH-15','name'=>'Mean Chey',        'name_km'=>'មានជ័យ',     'type'=>'khan'],
            ['code'=>'KH-15-07','province_code'=>'KH-15','name'=>'Russey Keo',       'name_km'=>'រស្មីកែវ',   'type'=>'khan'],
            ['code'=>'KH-15-08','province_code'=>'KH-15','name'=>'Sen Sok',          'name_km'=>'សែនសុខ',     'type'=>'khan'],
            ['code'=>'KH-15-09','province_code'=>'KH-15','name'=>'Por Sen Chey',     'name_km'=>'ពោធិ៍សែនជ័យ','type'=>'khan'],
            ['code'=>'KH-15-10','province_code'=>'KH-15','name'=>'Chroy Changvar',   'name_km'=>'ជ្រោយចង្វារ','type'=>'khan'],
            ['code'=>'KH-15-11','province_code'=>'KH-15','name'=>'Prek Pnov',        'name_km'=>'ព្រែកព្នៅ',  'type'=>'khan'],
            ['code'=>'KH-15-12','province_code'=>'KH-15','name'=>'Chbar Ampov',      'name_km'=>'ច្បារអំពៅ',  'type'=>'khan'],
            ['code'=>'KH-15-13','province_code'=>'KH-15','name'=>'Boeng Keng Kang',  'name_km'=>'បឹងកេងកាំង','type'=>'khan'],
            ['code'=>'KH-15-14','province_code'=>'KH-15','name'=>'Kamboul',          'name_km'=>'កំបូល',      'type'=>'khan'],
            // KH-21 Siem Reap
            ['code'=>'KH-21-01','province_code'=>'KH-21','name'=>'Angkor Chum',      'name_km'=>'អង្គរជូម',   'type'=>'district'],
            ['code'=>'KH-21-02','province_code'=>'KH-21','name'=>'Angkor Thom',      'name_km'=>'អង្គរធំ',    'type'=>'district'],
            ['code'=>'KH-21-03','province_code'=>'KH-21','name'=>'Banteay Srei',     'name_km'=>'បន្ទាយស្រី', 'type'=>'district'],
            ['code'=>'KH-21-04','province_code'=>'KH-21','name'=>'Chi Kraeng',       'name_km'=>'ជីក្រែង',    'type'=>'district'],
            ['code'=>'KH-21-05','province_code'=>'KH-21','name'=>'Kralanh',          'name_km'=>'ក្រឡាញ',     'type'=>'district'],
            ['code'=>'KH-21-06','province_code'=>'KH-21','name'=>'Prasat Bakong',    'name_km'=>'ប្រាសាទបាគង','type'=>'district'],
            ['code'=>'KH-21-07','province_code'=>'KH-21','name'=>'Puok',             'name_km'=>'ពួក',        'type'=>'district'],
            ['code'=>'KH-21-08','province_code'=>'KH-21','name'=>'Siem Reap',        'name_km'=>'សៀមរាប',     'type'=>'municipality'],
            ['code'=>'KH-21-09','province_code'=>'KH-21','name'=>'Sotr Nikum',       'name_km'=>'ស្ទើរនិគម', 'type'=>'district'],
            ['code'=>'KH-21-10','province_code'=>'KH-21','name'=>'Svay Leu',         'name_km'=>'ស្វាយឡើ',   'type'=>'district'],
            ['code'=>'KH-21-11','province_code'=>'KH-21','name'=>'Varin',            'name_km'=>'វ៉ារីន',     'type'=>'district'],
            // KH-24 Takéo — full 10 districts
            ['code'=>'KH-24-01','province_code'=>'KH-24','name'=>'Angkor Borei',     'name_km'=>'អង្គរបូរី',  'type'=>'district'],
            ['code'=>'KH-24-02','province_code'=>'KH-24','name'=>'Bati',             'name_km'=>'បាទី',       'type'=>'district'],
            ['code'=>'KH-24-03','province_code'=>'KH-24','name'=>'Borei Cholsar',    'name_km'=>'បូរីជ្រសារ', 'type'=>'district'],
            ['code'=>'KH-24-04','province_code'=>'KH-24','name'=>'Doun Kaev',        'name_km'=>'ដូនកែវ',     'type'=>'district'],
            ['code'=>'KH-24-05','province_code'=>'KH-24','name'=>'Kaoh Andaet',      'name_km'=>'កោះអណ្ដែត', 'type'=>'district'],
            ['code'=>'KH-24-06','province_code'=>'KH-24','name'=>'Kirivong',         'name_km'=>'គិរីវង',     'type'=>'district'],
            ['code'=>'KH-24-07','province_code'=>'KH-24','name'=>'Prey Kabbas',      'name_km'=>'ព្រៃក្បាស',  'type'=>'district'],
            ['code'=>'KH-24-08','province_code'=>'KH-24','name'=>'Samraong',         'name_km'=>'សំរោង',      'type'=>'district'],
            ['code'=>'KH-24-09','province_code'=>'KH-24','name'=>'Tram Kak',         'name_km'=>'ត្រាំក្អក',  'type'=>'district'],
            ['code'=>'KH-24-10','province_code'=>'KH-24','name'=>'Treang',           'name_km'=>'ត្រែង',      'type'=>'district'],
            ['code'=>'KH-24-11','province_code'=>'KH-24','name'=>'Daun Keo',         'name_km'=>'ដូនកែវ',     'type'=>'municipality'],
        ];
        foreach ($districts as $d) {
            DB::table('kh_districts')->insert(array_merge($d, ['created_at'=>$now,'updated_at'=>$now]));
        }

        // ─── COMMUNES ───────────────────────────────────────────────────────
        $communes = [
            // Chamkar Mon (KH-15-01)
            ['code'=>'KH-15-01-001','district_code'=>'KH-15-01','name'=>'Boeng Keng Kang 1','name_km'=>'បឹងកេងកាំងទី១','type'=>'sangkat'],
            ['code'=>'KH-15-01-002','district_code'=>'KH-15-01','name'=>'Boeng Keng Kang 2','name_km'=>'បឹងកេងកាំងទី២','type'=>'sangkat'],
            ['code'=>'KH-15-01-003','district_code'=>'KH-15-01','name'=>'Boeng Keng Kang 3','name_km'=>'បឹងកេងកាំងទី៣','type'=>'sangkat'],
            ['code'=>'KH-15-01-004','district_code'=>'KH-15-01','name'=>'Tonle Bassac',     'name_km'=>'តោនលេបាសាក',  'type'=>'sangkat'],
            ['code'=>'KH-15-01-005','district_code'=>'KH-15-01','name'=>'Olympic',           'name_km'=>'អូឡាំពិក',   'type'=>'sangkat'],
            ['code'=>'KH-15-01-006','district_code'=>'KH-15-01','name'=>'Tumnup Tuk',        'name_km'=>'ទំនប់ទូក',   'type'=>'sangkat'],
            // Doun Penh (KH-15-02)
            ['code'=>'KH-15-02-001','district_code'=>'KH-15-02','name'=>'Phsar Kandal 1',   'name_km'=>'ផ្សារកណ្ដាលទី១','type'=>'sangkat'],
            ['code'=>'KH-15-02-002','district_code'=>'KH-15-02','name'=>'Phsar Kandal 2',   'name_km'=>'ផ្សារកណ្ដាលទី២','type'=>'sangkat'],
            ['code'=>'KH-15-02-003','district_code'=>'KH-15-02','name'=>'Phsar Thmei 1',    'name_km'=>'ផ្សារថ្មីទី១', 'type'=>'sangkat'],
            ['code'=>'KH-15-02-004','district_code'=>'KH-15-02','name'=>'Phsar Thmei 2',    'name_km'=>'ផ្សារថ្មីទី២', 'type'=>'sangkat'],
            ['code'=>'KH-15-02-005','district_code'=>'KH-15-02','name'=>'Phsar Thmei 3',    'name_km'=>'ផ្សារថ្មីទី៣', 'type'=>'sangkat'],
            ['code'=>'KH-15-02-006','district_code'=>'KH-15-02','name'=>'Wat Phnom',         'name_km'=>'វត្តភ្នំ',    'type'=>'sangkat'],
            // Tuol Kouk (KH-15-04)
            ['code'=>'KH-15-04-001','district_code'=>'KH-15-04','name'=>'Boeng Kok 1',      'name_km'=>'បឹងកក់ ១',   'type'=>'sangkat'],
            ['code'=>'KH-15-04-002','district_code'=>'KH-15-04','name'=>'Boeng Kok 2',      'name_km'=>'បឹងកក់ ២',   'type'=>'sangkat'],
            ['code'=>'KH-15-04-003','district_code'=>'KH-15-04','name'=>'Phsar Depou 1',    'name_km'=>'ផ្សារដេប៉ូ ១','type'=>'sangkat'],
            ['code'=>'KH-15-04-004','district_code'=>'KH-15-04','name'=>'Phsar Depou 2',    'name_km'=>'ផ្សារដេប៉ូ ២','type'=>'sangkat'],
            ['code'=>'KH-15-04-005','district_code'=>'KH-15-04','name'=>'Phsar Depou 3',    'name_km'=>'ផ្សារដេប៉ូ ៣','type'=>'sangkat'],
            ['code'=>'KH-15-04-006','district_code'=>'KH-15-04','name'=>'Touk Thla',        'name_km'=>'ទូកថ្លា',     'type'=>'sangkat'],
            ['code'=>'KH-15-04-007','district_code'=>'KH-15-04','name'=>'Tuek L\'ak 1',     'name_km'=>'ទឹកល្អក់ ១', 'type'=>'sangkat'],
            ['code'=>'KH-15-04-008','district_code'=>'KH-15-04','name'=>'Tuek L\'ak 2',     'name_km'=>'ទឹកល្អក់ ២', 'type'=>'sangkat'],
            ['code'=>'KH-15-04-009','district_code'=>'KH-15-04','name'=>'Tuek L\'ak 3',     'name_km'=>'ទឹកល្អក់ ៣', 'type'=>'sangkat'],
            // Sen Sok (KH-15-08)
            ['code'=>'KH-15-08-001','district_code'=>'KH-15-08','name'=>'Krang Thnong',     'name_km'=>'ក្រង់ថ្នង',  'type'=>'sangkat'],
            ['code'=>'KH-15-08-002','district_code'=>'KH-15-08','name'=>'Phnom Penh Thmei', 'name_km'=>'ភ្នំពេញថ្មី','type'=>'sangkat'],
            ['code'=>'KH-15-08-003','district_code'=>'KH-15-08','name'=>'Ruessei Kaev',     'name_km'=>'រស្មីកែវ',   'type'=>'sangkat'],
            ['code'=>'KH-15-08-004','district_code'=>'KH-15-08','name'=>'Tuek Thla',        'name_km'=>'ទឹកថ្លា',    'type'=>'sangkat'],
            // Siem Reap city (KH-21-08)
            ['code'=>'KH-21-08-001','district_code'=>'KH-21-08','name'=>'Sala Kamreuk',     'name_km'=>'សាលាកំរើក', 'type'=>'sangkat'],
            ['code'=>'KH-21-08-002','district_code'=>'KH-21-08','name'=>'Svay Dangkum',     'name_km'=>'ស្វាយដង្គំ','type'=>'sangkat'],
            ['code'=>'KH-21-08-003','district_code'=>'KH-21-08','name'=>'Kouk Chak',        'name_km'=>'គោកចក',      'type'=>'sangkat'],
            ['code'=>'KH-21-08-004','district_code'=>'KH-21-08','name'=>'Nokor Thum',       'name_km'=>'នគរធំ',      'type'=>'sangkat'],
            ['code'=>'KH-21-08-005','district_code'=>'KH-21-08','name'=>'Sla Kram',         'name_km'=>'ស្លក្រាម',   'type'=>'sangkat'],
            // Takéo — Angkor Borei (KH-24-01)
            ['code'=>'KH-24-01-001','district_code'=>'KH-24-01','name'=>'Angkor Borei',     'name_km'=>'អង្គរបូរី',  'type'=>'commune'],
            ['code'=>'KH-24-01-002','district_code'=>'KH-24-01','name'=>'Prey Lvea',        'name_km'=>'ព្រៃល្វា',   'type'=>'commune'],
            ['code'=>'KH-24-01-003','district_code'=>'KH-24-01','name'=>'Rohal',            'name_km'=>'រោហ៍',       'type'=>'commune'],
            ['code'=>'KH-24-01-004','district_code'=>'KH-24-01','name'=>'Tani',             'name_km'=>'តានី',       'type'=>'commune'],
            ['code'=>'KH-24-01-005','district_code'=>'KH-24-01','name'=>'Trea',             'name_km'=>'ត្រា',        'type'=>'commune'],
            // Takéo — Bati (KH-24-02)
            ['code'=>'KH-24-02-001','district_code'=>'KH-24-02','name'=>'Bati',             'name_km'=>'បាទី',       'type'=>'commune'],
            ['code'=>'KH-24-02-002','district_code'=>'KH-24-02','name'=>'Damnak Chheukrom', 'name_km'=>'ដំណាក់ជ្រៃក្រោម','type'=>'commune'],
            ['code'=>'KH-24-02-003','district_code'=>'KH-24-02','name'=>'Kampong Kandal',   'name_km'=>'កំពង់កណ្ដាល','type'=>'commune'],
            ['code'=>'KH-24-02-004','district_code'=>'KH-24-02','name'=>'Khnach Romeas',    'name_km'=>'ក្នាចរំអាស', 'type'=>'commune'],
            ['code'=>'KH-24-02-005','district_code'=>'KH-24-02','name'=>'Mesar Prey',       'name_km'=>'មេសាព្រៃ',  'type'=>'commune'],
            ['code'=>'KH-24-02-006','district_code'=>'KH-24-02','name'=>'Prey Kuy',         'name_km'=>'ព្រៃគួយ',   'type'=>'commune'],
            ['code'=>'KH-24-02-007','district_code'=>'KH-24-02','name'=>'Prey Nheat',       'name_km'=>'ព្រៃញ៉ែត',  'type'=>'commune'],
            ['code'=>'KH-24-02-008','district_code'=>'KH-24-02','name'=>'Ta Ing',           'name_km'=>'តាឥង',       'type'=>'commune'],
            // Takéo — Kirivong (KH-24-06)
            ['code'=>'KH-24-06-001','district_code'=>'KH-24-06','name'=>'Ang Roka',         'name_km'=>'អង្គរក',     'type'=>'commune'],
            ['code'=>'KH-24-06-002','district_code'=>'KH-24-06','name'=>'Kirivong',         'name_km'=>'គិរីវង',     'type'=>'commune'],
            ['code'=>'KH-24-06-003','district_code'=>'KH-24-06','name'=>'Pong Ro',          'name_km'=>'ពោងរ',       'type'=>'commune'],
            ['code'=>'KH-24-06-004','district_code'=>'KH-24-06','name'=>'Prey Kabbas',      'name_km'=>'ព្រៃក្បាស',  'type'=>'commune'],
            ['code'=>'KH-24-06-005','district_code'=>'KH-24-06','name'=>'Samraong',         'name_km'=>'សំរោង',      'type'=>'commune'],
            // Takéo — Tram Kak (KH-24-09)
            ['code'=>'KH-24-09-001','district_code'=>'KH-24-09','name'=>'Angkor Meanchey',  'name_km'=>'អង្គរមានជ័យ','type'=>'commune'],
            ['code'=>'KH-24-09-002','district_code'=>'KH-24-09','name'=>'Cheung Prey',      'name_km'=>'ជើងព្រៃ',   'type'=>'commune'],
            ['code'=>'KH-24-09-003','district_code'=>'KH-24-09','name'=>'Norea',            'name_km'=>'នរា',        'type'=>'commune'],
            ['code'=>'KH-24-09-004','district_code'=>'KH-24-09','name'=>'Tram Kak',         'name_km'=>'ត្រាំក្អក',  'type'=>'commune'],
        ];
        foreach ($communes as $c) {
            DB::table('kh_communes')->insert(array_merge($c, ['created_at'=>$now,'updated_at'=>$now]));
        }

        // ─── VILLAGES ───────────────────────────────────────────────────────
        $villages = [
            // Prey Lvea commune (KH-24-01-002) — Angkor Borei, Takéo
            ['code'=>'KH-24-01-002-001','commune_code'=>'KH-24-01-002','name'=>'Ang Kra Sang',   'name_km'=>'អង្គក្រសាំង'],
            ['code'=>'KH-24-01-002-002','commune_code'=>'KH-24-01-002','name'=>'Prey Lvea',       'name_km'=>'ព្រៃល្វា'],
            ['code'=>'KH-24-01-002-003','commune_code'=>'KH-24-01-002','name'=>'Kandaol',         'name_km'=>'កន្ដោល'],
            ['code'=>'KH-24-01-002-004','commune_code'=>'KH-24-01-002','name'=>'Champa',          'name_km'=>'ចំប៉ា'],
            // Angkor Borei commune (KH-24-01-001)
            ['code'=>'KH-24-01-001-001','commune_code'=>'KH-24-01-001','name'=>'Angkor Borei',    'name_km'=>'អង្គរបូរី'],
            ['code'=>'KH-24-01-001-002','commune_code'=>'KH-24-01-001','name'=>'Kompong Kor',     'name_km'=>'កំពង់គោ'],
            // Sala Kamreuk (KH-21-08-001) — Siem Reap
            ['code'=>'KH-21-08-001-001','commune_code'=>'KH-21-08-001','name'=>'Phum Wat Bo',     'name_km'=>'ភូមិវត្ដបូ'],
            ['code'=>'KH-21-08-001-002','commune_code'=>'KH-21-08-001','name'=>'Phum Taphul',     'name_km'=>'ភូមិតាផុល'],
            ['code'=>'KH-21-08-001-003','commune_code'=>'KH-21-08-001','name'=>'Phum Samrong',    'name_km'=>'ភូមិសំរោង'],
            // Boeng Keng Kang 1 (KH-15-01-001) — Phnom Penh
            ['code'=>'KH-15-01-001-001','commune_code'=>'KH-15-01-001','name'=>'Phum 1',          'name_km'=>'ភូមិ ១'],
            ['code'=>'KH-15-01-001-002','commune_code'=>'KH-15-01-001','name'=>'Phum 2',          'name_km'=>'ភូមិ ២'],
            ['code'=>'KH-15-01-001-003','commune_code'=>'KH-15-01-001','name'=>'Phum 3',          'name_km'=>'ភូមិ ៣'],
            // Wat Phnom (KH-15-02-006) — Phnom Penh
            ['code'=>'KH-15-02-006-001','commune_code'=>'KH-15-02-006','name'=>'Phum 1',          'name_km'=>'ភូមិ ១'],
            ['code'=>'KH-15-02-006-002','commune_code'=>'KH-15-02-006','name'=>'Phum 2',          'name_km'=>'ភូមិ ២'],
            // Tuek Thla (KH-15-08-004) — Sen Sok, Phnom Penh
            ['code'=>'KH-15-08-004-001','commune_code'=>'KH-15-08-004','name'=>'Phum 1',          'name_km'=>'ភូមិ ១'],
            ['code'=>'KH-15-08-004-002','commune_code'=>'KH-15-08-004','name'=>'Phum 2',          'name_km'=>'ភូមិ ២'],
            ['code'=>'KH-15-08-004-003','commune_code'=>'KH-15-08-004','name'=>'Phum 3',          'name_km'=>'ភូមិ ៣'],
        ];
        foreach ($villages as $v) {
            DB::table('kh_villages')->insert(array_merge($v, ['created_at'=>$now,'updated_at'=>$now]));
        }
    }
}
