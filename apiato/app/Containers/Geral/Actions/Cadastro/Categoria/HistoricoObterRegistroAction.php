<?php

namespace App\Containers\Geral\Actions\Cadastro\Categoria;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;

class HistoricoObterRegistroAction extends Action
{
    public function run(Request $request)
    {
        return Apiato::call('Geral@Cadastro\Categoria\HistoricoObterRegistroTask', [$request->id_historico]);
    }
}
