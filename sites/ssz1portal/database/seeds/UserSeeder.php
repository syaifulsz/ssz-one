<?php

use Phinx\Seed\AbstractSeed;
use app\models\User;

class UserSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [];

        $ssz = new User( [
            'name' => 'Syaiful Shah Zinan',
            'username' => 'admin',
            'email' => 'i.works@live.com',
            'contact_phone_number' => '0192009000'
        ] );
        $ssz->setRoleAttribute( User::ROLE_ADMIN );
        $ssz->setPasswordAttribute( '123456' );
        $ssz = array_merge( $ssz->toArray(), [
            'updated_at' => date( 'Y-m-d H:i:s' ),
            'created_at' => date( 'Y-m-d H:i:s' )
        ] );
        $data[] = $ssz;

        $moderator = new User( [
            'name' => 'Moderator',
            'username' => 'moderator',
            'email' => 'moder@tor.com',
            'contact_phone_number' => '0193003000'
        ] );
        $moderator->setRoleAttribute( User::ROLE_STUDENT );
        $moderator->setPasswordAttribute( '123456' );
        $moderator = array_merge( $moderator->toArray(), [
            'updated_at' => date( 'Y-m-d H:i:s' ),
            'created_at' => date( 'Y-m-d H:i:s' )
        ] );
        $data[] = $moderator;

        $this
            ->table( 'users' )
            ->insert( $data )
            ->save();
    }
}
