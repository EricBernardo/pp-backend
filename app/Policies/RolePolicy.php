<?php

namespace App\Policies;

class RolePolicy extends BasePolicy
{
    public function create()
    {
        return false;
    }

    public function update()
    {
        return false;
    }

    public function delete()
    {
        return false;
    }

    public function forceDelete()
    {
        return false;
    }

    public function restore()
    {
        return false;
    }
}
