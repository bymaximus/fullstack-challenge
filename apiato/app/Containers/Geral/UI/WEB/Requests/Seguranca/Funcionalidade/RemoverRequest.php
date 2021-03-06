<?php

namespace App\Containers\Geral\UI\WEB\Requests\Seguranca\Funcionalidade;

use App\Ship\Parents\Requests\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;

class RemoverRequest extends Request
{
	const FUNCIONALIDADE_URL = '/seguranca/funcionalidade';

	/**
	 * The assigned Transporter for this Request
	 *
	 * @var string
	 */
	protected $transporter = \App\Containers\Geral\Data\Transporters\Seguranca\Funcionalidade\RemoverTransporter::class;

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
		'id',
	];

	/**
	 * @return  array
	 */
	public function rules()
	{
		return [
			'id' => 'required|exists:funcionalidade,id',
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
