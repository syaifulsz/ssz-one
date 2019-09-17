<?php

namespace app\core\models\sleekdb;

use SleekDB\SleekDB;
use app\core\models\Model as BaseModel;

class Model extends BaseModel
{
    protected $collectionName;
    protected $collectionDataDir;
    public $db;

    public function __construct( array $configs = [] )
    {
        parent::__construct( $configs );
        $this->db = SleekDB::store( $this->collectionName, $this->collectionDataDir );
    }
}