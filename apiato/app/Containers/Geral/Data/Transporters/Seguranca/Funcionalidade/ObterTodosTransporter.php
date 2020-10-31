<?php

namespace App\Containers\Geral\Data\Transporters\Seguranca\Funcionalidade;

use App\Ship\Parents\Transporters\Transporter;

class ObterTodosTransporter extends Transporter
{
	/**
	 * @var array
	 */
	protected $schema = [
		'type' => 'object',
		'properties' => [
			// enter all properties here

			// allow for undefined properties
			// 'additionalProperties' => true,
		],
		'required' => [
			// define the properties that MUST be set
		],
		'default' => [
			// provide default values for specific properties here
		]
	];
}