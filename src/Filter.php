<?php
/*
 * @Description:
 * @Author: (c) Pian Zhou <pianzhou2021@163.com>
 * @Date: 2022-06-15 18:44:37
 * @LastEditors: Pian Zhou
 * @LastEditTime: 2022-07-21 22:59:27
 */
namespace Pianzhou\Laravel\Query\Filter;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

/**
 * Filter 
 *
 * @method where($column, $operator = null, $value = null, $boolean = 'and')
 * @method orWhere($column, $operator = null, $value = null)
 * @method whereNot($column, $operator = null, $value = null, $boolean = 'and')
 * @method orWhereNot($column, $operator = null, $value = null)
 * @method whereDate($column, $operator, $value = null, $boolean = 'and')
 * @method orWhereDate($column, $operator, $value = null)
 * @method whereTime($column, $operator, $value = null, $boolean = 'and')
 * @method orWhereTime($column, $operator, $value = null)
 * @method whereDay($column, $operator, $value = null, $boolean = 'and')
 * @method orWhereDay($column, $operator, $value = null)
 * @method whereMonth($column, $operator, $value = null, $boolean = 'and')
 * @method orWhereMonth($column, $operator, $value = null)
 * @method whereYear($column, $operator, $value = null, $boolean = 'and')
 * @method orWhereYear($column, $operator, $value = null)
 * @method whereJsonLength($column, $operator, $value = null, $boolean = 'and')
 * @method orWhereJsonLength($column, $operator, $value = null)
 * @method having($column, $operator = null, $value = null, $boolean = 'and')
 * @method orHaving($column, $operator = null, $value = null)
 * @method whereIn($column, $values, $boolean = 'and', $not = false)
 * @method orWhereIn($column, $values)
 * @method whereNotIn($column, $values, $boolean = 'and')
 * @method orWhereNotIn($column, $values)
 * @method whereIntegerInRaw($column, $values, $boolean = 'and', $not = false)
 * @method orWhereIntegerInRaw($column, $values)
 * @method whereIntegerNotInRaw($column, $values, $boolean = 'and')
 * @method orWhereIntegerNotInRaw($column, $values)
 * @method whereBetween($column, iterable $values, $boolean = 'and', $not = false)
 * @method whereBetweenColumns($column, array $values, $boolean = 'and', $not = false)
 * @method orWhereBetween($column, iterable $values)
 * @method orWhereBetweenColumns($column, array $values)
 * @method whereNotBetween($column, iterable $values, $boolean = 'and')
 * @method whereNotBetweenColumns($column, array $values, $boolean = 'and')
 * @method orWhereNotBetween($column, iterable $values)
 * @method orWhereNotBetweenColumns($column, array $values)
 *
 */
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
    public function when(string $name, \Closure $closure, int $mode = self::MODE_NULL): Filter
    {
        $this->builder->filter(Arr::get($this->params, $name), $closure, $mode);
        return $this;
    }

    /**
     * 扩展
     *
     * @param $method
     * @param $params
     * @return void
     */
    public function __call($method, $params)
    {
        return $this->when(current($params), function ($value) use ($method, $params) {
            call_user_func_array([$this, $method], array_merge($params, [ $value ]));
        });
    }

    /**
     * Register Query Builder method filter and filters
     *
     * @return void
     * @throws \Exception
     */
    static public function register()
    {
        Builder::macro('filter', function ($value, \Closure $closure, $mode = Filter::MODE_NULL) {
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

        Builder::macro('filters', function (array $values, \Closure $closure = null) {
            $filter = new Filter($this, $values);

            if ($closure !== null) {
                $closure->call($this, $filter);
            } else {
                foreach ($values as $key => $value) {
                    if (is_array($value)) {
                        $filter->whereIn($key);
                    } elseif (strpos($value, '%') !== false) {
                        $filter->where($key, 'LIKE');
                    } else {
                        $filter->where($key);
                    }
                }
            }
            return $this;
        });
    }
}
