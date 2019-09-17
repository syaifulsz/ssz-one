<?php

namespace app\models;

use app\core\models\sleekdb\Model as BaseModel;

class User extends BaseModel
{
    const AUTH_COOKIE_KEY = 'LOGGED_IN_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_MODERATOR = 'ROLE_MODERATOR';

    protected $collectionName = 'users';
}
