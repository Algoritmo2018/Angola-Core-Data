<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\Status;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Bank::create([
            'bank_name' => 'Banco Angolano de Investimentos S.A.',
            'short_name' => 'BAI',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0040',
        ]);

        Bank::create([
            'bank_name' => 'Banco Yetu S.A.',
            'short_name' => 'YETU',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0066',
        ]);

        Bank::create([
            'bank_name' => 'Banco Angolano de Negócios e Comércio',
            'short_name' => 'BANC',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0053',
        ]);

        Bank::create([
            'bank_name' => 'Banco BAI Micro Finanças S.A.',
            'short_name' => 'BMF',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0048',
        ]);

        Bank::create([
            'bank_name' => 'Banco BIC Angola',
            'short_name' => 'BIC',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0051',
        ]);

        Bank::create([
            'bank_name' => 'Banco Caixa Geral Angola (Totta) S.A.',
            'short_name' => 'BCGA',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0004',
        ]);

        Bank::create([
            'bank_name' => 'Banco Comercial Angolano S.A.',
            'short_name' => 'BCA',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0043',
        ]);

        Bank::create([
            'bank_name' => 'Banco Comercial do Huambo S.A.',
            'short_name' => 'BCH',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0059',
        ]);

        Bank::create([
            'bank_name' => 'Banco de Comércio e Indústria S.A.',
            'short_name' => 'BCI',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0005',
        ]);

        Bank::create([
            'bank_name' => 'Banco de Desenvolvimento de Angola S.A.',
            'short_name' => 'BDA',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0054',
        ]);

        Bank::create([
            'bank_name' => 'Banco de Fomento Angola S.A.',
            'short_name' => 'BFA',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0006',
        ]);

        Bank::create([
            'bank_name' => 'Banco de Investimento Rural S.A.',
            'short_name' => 'BIR',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0067',
        ]);

        Bank::create([
            'bank_name' => 'Banco de Negócios Internacional S.A.',
            'short_name' => 'BNI',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0052',
        ]);

        Bank::create([
            'bank_name' => 'Banco de Poupança e Crédito S.A.',
            'short_name' => 'BPC',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0010',
        ]);

        Bank::create([
            'bank_name' => 'Banco Económico (Angola)',
            'short_name' => 'BE',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0045',
        ]);

        Bank::create([
            'bank_name' => 'Banco Keve S.A.',
            'short_name' => 'KEVE',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0047',
        ]);

        Bank::create([
            'bank_name' => 'Banco Kwanza Investimento S.A.',
            'short_name' => 'BKI',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0057',
        ]);

        Bank::create([
            'bank_name' => 'Banco Prestígio S.A.',
            'short_name' => 'BPG',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0064',
        ]);

        Bank::create([
            'bank_name' => 'Banco Millennium Atlântico S.A.',
            'short_name' => 'ATL',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0055',
        ]);

        Bank::create([
            'bank_name' => 'Banco Mais S.A.',
            'short_name' => 'BMAIS',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0065',
        ]);

        Bank::create([
            'bank_name' => 'Banco Sol S.A.',
            'short_name' => 'BSOL',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0044',
        ]);

        Bank::create([
            'bank_name' => 'Banco Valor S.A.',
            'short_name' => 'BVB',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0062',
        ]);

        Bank::create([
            'bank_name' => 'Banco VTB África S.A.',
            'short_name' => 'VTB',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0056',
        ]);

        Bank::create([
            'bank_name' => 'Finibanco Angola S.A.',
            'short_name' => 'FNB',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0058',
        ]);

        Bank::create([
            'bank_name' => 'Standard Bank de Angola S.A.',
            'short_name' => 'SBA',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0060',
        ]);

        Bank::create([
            'bank_name' => 'Standard Chartered Bank de Angola S.A.',
            'short_name' => 'SCBS',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0063',
        ]);

        Bank::create([
            'bank_name' => 'Banco de Crédito do Sul S.A.',
            'short_name' => 'BCS',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0070',
        ]);

        Bank::create([
            'bank_name' => 'Banco Postal',
            'short_name' => 'BPT',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0069',
        ]);

        Bank::create([
            'bank_name' => 'Banco da China Limitada — Sucursal em Luanda',
            'short_name' => 'BOCLB',
            'country_prefix' => 'AO06',
            'bank_prefix' => '0071',
        ]);
    }
}
