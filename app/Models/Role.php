<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class Role extends Model
{
    use Sushi;

    public $incrementing = false;

    protected $keyType = 'string';

    protected function casts()
    {
        return [
            'permissions' => 'json'
        ];
    }

    public function getRows()
    {
        return [
            [
                'key' => 'admin',
                'name' => 'Admin',
                'description' => 'Admins can perform any action',
                'permissions' => json_encode([ '*' ])
            ],
            [
                'key' => 'user',
                'name' => 'User',
                'description' => 'User can only manage links',
                'permissions' => json_encode([
                    'link:viewAny',
                    'link:view',
                    'link:create',
                    'link:update',
                    'link:delete',
                ])
            ]
        ];
    }
}
