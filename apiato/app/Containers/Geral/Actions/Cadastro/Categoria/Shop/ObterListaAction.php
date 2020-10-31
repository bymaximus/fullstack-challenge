<?php

namespace App\Containers\Geral\Actions\Cadastro\Categoria\Shop;

use App\Ship\Parents\Actions\Action;
use Apiato\Core\Foundation\Facades\Apiato;

class ObterListaAction extends Action
{
    public function run()
    {
        return Apiato::call('Geral@Cadastro\Categoria\Shop\ObterListaTask');
    }
}
