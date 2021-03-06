<?php

namespace App\Containers\Geral\Tasks\Cadastro\SubCategoria;

use App\Containers\Geral\Data\Repositories\Cadastro\SubCategoriaRepository;
use App\Containers\Geral\Models\SubCategoria;
use App\Ship\Exceptions\CreateResourceFailedException;
use App\Ship\Parents\Tasks\Task;
use Watson\Validating\ValidationException;
use Exception;

class AdicionarTask extends Task
{
    protected $repository;

    public function __construct(SubCategoriaRepository $repository)
    {
        $this->repository = $repository;
    }

    public function run(array $data)
    {
        try {
            SubCategoria::flushQueryCache();
            /**
             * @var SubCategoria
             */
            $model = $this->repository->create($data);
            if ($model) {
                if ($model->isValid()) {
                    return $model;
                } else {
                    return $model->saveOrFail();
                }
            } else {
                $modelErros = $model->getErrors();
                if ($modelErros &&
                    $modelErros instanceof \Illuminate\Support\MessageBag
                ) {
                    $modelErros = $modelErros->getMessages();
                }
                if (!$modelErros) {
                    throw new Exception('Registro não criado.');
                } else {
                    $erros = [];
                    foreach ($modelErros as $campo => $campoErros) {
                        if (is_array($campoErros)) {
                            $erros[] = $campoErros[0];
                        } else {
                            $erros[] = $campoErros;
                        }
                    }
                    if ($erros) {
                        throw new Exception('Os dados fornecidos são inválidos. Erros: ' . join(' ', $erros));
                    }
                    throw new Exception('Os dados fornecidos são inválidos.');
                }
            }
        } catch (ValidationException $err) {
            throw $err;
        } catch (Exception $err) {
            throw new CreateResourceFailedException($err->getMessage());
        }
    }
}
