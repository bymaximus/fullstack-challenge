<?php

namespace App\Containers\Geral\Actions\Cadastro\SubCategoria;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;

class ObterListaAction extends Action
{
    public function run(Request $request)
    {
        return Apiato::call('Geral@Cadastro\SubCategoria\ObterListaTask');
    }
}
