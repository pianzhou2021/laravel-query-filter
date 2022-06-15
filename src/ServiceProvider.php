<?php
/*
 * @Description:
 * @Author: (c) Pian Zhou <pianzhou2021@163.com>
 * @Date: 2022-06-15 18:44:37
 * @LastEditors: Pian Zhou
 * @LastEditTime: 2022-06-15 18:59:27
 */

namespace Pianzhou\Laravel\Query\Filter;


class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     * @throws \Exception
     */
    public function register()
    {
        Filter::register();
    }
}