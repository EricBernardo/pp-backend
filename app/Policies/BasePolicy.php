<?php

namespace App\Policies;

class BasePolicy
{
    public function create()
    {
        return request()->user()->isAdmin();
    }

    public function update()
    {
        return request()->user()->isAdmin();
    }

    public function delete()
    {
        return request()->user()->isAdmin();
    }

    public function forceDelete()
    {
        return request()->user()->isAdmin();
    }

    public function restore()
    {
        return request()->user()->isAdmin();
    }
}
