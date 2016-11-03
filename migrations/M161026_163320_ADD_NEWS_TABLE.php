<?php
use yii\db\Schema;
use yii\db\Migration;

class m161026_163320_add_news_table extends Migration
{
    public function up() {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }        
        $this->createTable('{{%PUB.news}}', [
            "id" =>  Schema::TYPE_INTEGER . ' PRIMARY KEY NOT NULL',
            "title" => 'varchar(255) NOT NULL ',
            "content" => 'varchar(255) NOT NULL ',
        ], $tableOptions);
    }
    public function down() {
        $this->dropTable('{{%PUB.news}}');
    }
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
