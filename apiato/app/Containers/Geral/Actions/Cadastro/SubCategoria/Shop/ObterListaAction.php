<?php

namespace App\Containers\Geral\Actions\Cadastro\SubCategoria\Shop;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;

class ObterListaAction extends Action
{
    public function run()
    {
        return Apiato::call('Geral@Cadastro\SubCategoria\ObterListaTask');
    }
}
