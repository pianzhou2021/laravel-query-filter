<?php
/*
 * @Description:
 * @Author: (c) Pian Zhou <pianzhou2021@163.com>
 * @Date: 2022-06-15 18:44:37
 * @LastEditors: Pian Zhou
 * @LastEditTime: 2022-06-15 18:59:27
 */
namespace Pianzhou\Laravel\Query\Filter;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

class Filter
{
    const MODE_EMPTY = 1;
    const MODE_NULL  = 2;
    const MODE_BOOL  = 3;
    /**
     * @var Builder
     */
    protected $builder;
    /**
     * @var array
     */
    protected $params = [];

    /**
     * construct
     *
     * @param Builder $builder
     * @param array $params
     */
    public function __construct(Builder $builder, array $params)
    {
        $this->builder  = $builder;
        $this->params   = $params;
    }

    /**
     * when
     *
     * @param string $name
     * @param \Closure $closure
     * @param int $mode
     * @return $this
     */
    public function when(string $name, \Closure $closure, int $mode = self::MODE_EMPTY): Filter
    {
        $this->builder->filter(Arr::get($this->params, $name), $closure, $mode);
        return $this;
    }

    /**
     * Register Query Builder method filter and filters
     *
     * @return void
     * @throws \Exception
     */
    static public function register()
    {
        Builder::macro('filter', function ($value, \Closure $closure, $mode = Filter::MODE_EMPTY) {
            switch ($mode) {
                case Filter::MODE_EMPTY : {
                    $skip = empty($value);
                } break;
                case Filter::MODE_NULL : {
                    $skip = is_null($value);
                } break;
                case Filter::MODE_BOOL : {
                    $skip = $value !== true;
                } break;
                default: {
                    throw new \Exception("UNKNOWN MODE : [{$mode}]");
                }
            }

            if (!$skip) {
                $closure->call($this, $value);
            }
            return $this;
        });

        Builder::macro('filters', function (array $values, \Closure $closure) {
            $filter = new Filter($this, $values);
            $closure->call($this, $filter);
            return $this;
        });
    }
}
