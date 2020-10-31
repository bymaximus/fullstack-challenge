<?php

namespace App\Containers\Geral\Actions\Cadastro\SubCategoria;

use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;
use Apiato\Core\Foundation\Facades\Apiato;

class ObterTodosAction extends Action
{
    public function run(Request $request)
    {
        $registros = Apiato::call('Geral@Cadastro\SubCategoria\ObterTodosTask', [], ['addRequestCriteria']);
        $request = app('datatables.request');
        if ($request &&
            $request->orderableColumns()
        ) {
            return datatables()->eloquent($registros->select(['id', 'nome', 'id_categoria', 'dt_criacao', 'dt_alteracao']));
        } else {
            return datatables()->eloquent($registros->select(['id', 'nome', 'id_categoria', 'dt_criacao', 'dt_alteracao'])->oldest('nome'));
        }
    }
}
