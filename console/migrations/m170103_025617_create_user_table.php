<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m170103_025617_create_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = ($this->db->driverName === 'mysql') ? 'ENGINE=InnoDB DEFAULT CHARSET=latin1' : null;

        $this->createTable('user', [
            'id' => $this->string()->notNull(),
            'name' => $this->string()->notNull(),
            'email' => $this->string()->notNull()->unique(),
            'encrypted_password' => $this->string(60)->notNull(),
            'access_token' => $this->string()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('user_pk', 'user', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user');
    }
}
