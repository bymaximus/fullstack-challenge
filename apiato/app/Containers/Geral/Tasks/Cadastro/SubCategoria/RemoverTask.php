<?php

namespace App\Containers\Geral\Tasks\Cadastro\SubCategoria;

use App\Containers\Geral\Data\Repositories\Cadastro\SubCategoriaRepository;
use App\Containers\Geral\Models\SubCategoria;
use App\Ship\Exceptions\DeleteResourceFailedException;
use App\Ship\Parents\Tasks\Task;
use Watson\Validating\ValidationException;
use Exception;

class RemoverTask extends Task
{
    protected $repository;

    public function __construct(SubCategoriaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run($id)
    {
        try {
            SubCategoria::flushQueryCache();
            /**
             * @var SubCategoria
             */
            $model = $this->repository->find($id);
            if ($model) {
                if ($model->delete()) {
                    return $model;
                } else {
                    throw new Exception('Erro ao remover registro.');
                }
            } else {
                throw new Exception('Registro não encontrado.');
            }
        } catch (ValidationException $err) {
            throw $err;
        } catch (Exception $err) {
            throw new DeleteResourceFailedException($err->getMessage());
        }
    }
}
