<?php

namespace App\Containers\Geral\UI\WEB\Requests\Cadastro\Produto;

use App\Ship\Parents\Requests\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;

class AdicionarRequest extends Request
{
    const FUNCIONALIDADE_URL = '/cadastro/produto';

    /**
     * The assigned Transporter for this Request
     *
     * @var string
     */
    protected $transporter = \App\Containers\Geral\Data\Transporters\Cadastro\Produto\AdicionarTransporter::class;

    /**
     * Define which Roles and/or Permissions has access to this request.
     *
     * @var  array
     */
    protected $access = [
        'permissions' => '',
        'roles' => '',
    ];

    /**
     * Id's that needs decoding before applying the validation rules.
     *
     * @var  array
     */
    protected $decode = [
        // 'id',
    ];

    /**
     * Defining the URL parameters (e.g, `/user/{id}`) allows applying
     * validation rules on them and allows accessing them like request data.
     *
     * @var  array
     */
    protected $urlParameters = [
        //'id',
    ];

    /**
     * @return  array
     */
    public function rules()
    {
        $self = $this;
        return [
            'id_categoria' => [
                'required',
                'numeric',
                Rule::exists('categoria', 'id')->where(function ($query) {
                    $query->whereNull('dt_remocao');
                }),
            ],
            'id_subcategoria' => [
                'required',
                'numeric',
                Rule::exists('sub_categoria', 'id')->where(function ($query) use ($self) {
                    $query->whereNull('dt_remocao')
                          ->where('id_categoria', '=', $self->id_categoria);
                }),
            ],
            'nome' => [
                'required',
                'string',
                'min:1',
                'max:100',
                Rule::unique('produto', 'nome')->where(function ($query) use ($self) {
                    $query->whereNull('dt_remocao')
                          ->where('id_subcategoria', '=', $self->id_subcategoria);
                }),
            ],
            'preco' => 'required|numeric|min:0.01',
            'cor' => 'nullable|array',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];
    }

    /**
     * @return  bool
     */
    public function authorize()
    {
        $controller = Route::current()->controller;
        if (!$controller ||
            !$controller->isLogged() ||
            !$controller->usuario()
        ) {
            return false;
        }
        $usuario = $controller->usuario();
        if (!$usuario ||
            !$usuario->funcionalidadePermitida(self::FUNCIONALIDADE_URL)
        ) {
            return false;
        }
        return $this->check([
            'hasAccess',
        ]);
    }
}
