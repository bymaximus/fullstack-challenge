<?php

namespace App\Containers\Geral\Models;

use App\Containers\Geral\Models\MainModel;
use Illuminate\Validation\Rule;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Laravel\Scout\Searchable;
use Exception;

/**
 * @property integer $id
 * @property string $nome
 * @property string $dt_criacao
 * @property string $dt_alteracao
 * @property string $dt_remocao
 */
class Categoria extends MainModel
{
    use QueryCacheable;

    //use Searchable;

    public $cacheFor = 180;

    protected static $flushCacheOnUpdate = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categoria';

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'nome',
        'dt_criacao',
        'dt_alteracao',
        'dt_remocao'
    ];

    public static $labels = [
        'id' => 'ID',
        'nome' => 'Nome',
        'dt_criacao' => 'Dt. Criação',
        'dt_alteracao' => 'Dt. Alteração',
        'dt_remocao' => 'Dt. Remoção',
    ];

    public function getRules()
    {
        if (!$this->id ||
            !$this->exists
        ) {
            return [
                'nome' => [
                    'required',
                    'string',
                    'min:1',
                    'max:100',
                    Rule::unique('categoria', 'nome')->where(function ($query) {
                        $query->whereNull('dt_remocao');
                    }),
                ],
            ];
        } else {
            $self = $this;
            return [
                'nome' => [
                    'required',
                    'string',
                    'min:1',
                    'max:100',
                    Rule::unique('categoria', 'nome')->where(function ($query) use ($self) {
                        $query->whereNull('dt_remocao')
                              ->where('id', '<>', $self->id);
                    }),
                ],
            ];
        }
    }

    /*public static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            $model->subCategoria->filter(function ($item) {
                return $item->shouldBeSearchable();
            })->searchable();
        });
    }*/

    public function subCategoria()
    {
        return $this->hasMany('App\Containers\Geral\Models\SubCategoria', 'id_categoria', 'id')->orderBy('nome');
	}

  	public function scopeShop($query)
    {
        return $query->has('subCategoria');
    }

    /*public function toSearchableArray()
    {
        $array = $this->toArray();

        $array = $this->transform($array);

        $array['subCategoria'] = $this->subCategoria->map(function ($data) {
            return $data['nome'];
        })->toArray();

        return $array;
    }*/
}
