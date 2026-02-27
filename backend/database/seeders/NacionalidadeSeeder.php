<?php

namespace Database\Seeders;

use App\Models\Nacionalidade;
use Illuminate\Database\Seeder;

class NacionalidadeSeeder extends Seeder
{
    public function run(): void
    {
        $registros = [
            ['descricao' => 'Brasileira', 'gentilico' => 'Brasileiro(a)'],
            ['descricao' => 'Alemã', 'gentilico' => 'Alemão/Alemã'],
            ['descricao' => 'Argentina', 'gentilico' => 'Argentino(a)'],
            ['descricao' => 'Australiana', 'gentilico' => 'Australiano(a)'],
            ['descricao' => 'Austríaca', 'gentilico' => 'Austríaco(a)'],
            ['descricao' => 'Belga', 'gentilico' => 'Belga'],
            ['descricao' => 'Boliviana', 'gentilico' => 'Boliviano(a)'],
            ['descricao' => 'Canadense', 'gentilico' => 'Canadense'],
            ['descricao' => 'Chilena', 'gentilico' => 'Chileno(a)'],
            ['descricao' => 'Chinesa', 'gentilico' => 'Chinês/Chinesa'],
            ['descricao' => 'Colombiana', 'gentilico' => 'Colombiano(a)'],
            ['descricao' => 'Coreana', 'gentilico' => 'Coreano(a)'],
            ['descricao' => 'Cubana', 'gentilico' => 'Cubano(a)'],
            ['descricao' => 'Equatoriana', 'gentilico' => 'Equatoriano(a)'],
            ['descricao' => 'Espanhola', 'gentilico' => 'Espanhol(a)'],
            ['descricao' => 'Estadunidense', 'gentilico' => 'Americano(a)'],
            ['descricao' => 'Francesa', 'gentilico' => 'Francês/Francesa'],
            ['descricao' => 'Grega', 'gentilico' => 'Grego(a)'],
            ['descricao' => 'Holandesa', 'gentilico' => 'Holandês/Holandesa'],
            ['descricao' => 'Indiana', 'gentilico' => 'Indiano(a)'],
            ['descricao' => 'Inglesa', 'gentilico' => 'Inglês/Inglesa'],
            ['descricao' => 'Iraniana', 'gentilico' => 'Iraniano(a)'],
            ['descricao' => 'Italiana', 'gentilico' => 'Italiano(a)'],
            ['descricao' => 'Japonesa', 'gentilico' => 'Japonês/Japonesa'],
            ['descricao' => 'Mexicana', 'gentilico' => 'Mexicano(a)'],
            ['descricao' => 'Nigeriana', 'gentilico' => 'Nigeriano(a)'],
            ['descricao' => 'Paraguaia', 'gentilico' => 'Paraguaio(a)'],
            ['descricao' => 'Peruana', 'gentilico' => 'Peruano(a)'],
            ['descricao' => 'Polonesa', 'gentilico' => 'Polonês/Polonesa'],
            ['descricao' => 'Portuguesa', 'gentilico' => 'Português(a)'],
            ['descricao' => 'Russa', 'gentilico' => 'Russo(a)'],
            ['descricao' => 'Suíça', 'gentilico' => 'Suíço(a)'],
            ['descricao' => 'Turca', 'gentilico' => 'Turco(a)'],
            ['descricao' => 'Ucraniana', 'gentilico' => 'Ucraniano(a)'],
            ['descricao' => 'Uruguaia', 'gentilico' => 'Uruguaio(a)'],
            ['descricao' => 'Venezuelana', 'gentilico' => 'Venezuelano(a)'],
            ['descricao' => 'Outra', 'gentilico' => null],
        ];

        foreach ($registros as $dados) {
            Nacionalidade::updateOrCreate(
                ['descricao' => $dados['descricao']],
                ['gentilico' => $dados['gentilico'], 'is_ativo' => true],
            );
        }
    }
}
