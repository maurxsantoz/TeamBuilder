<?php
use PHPUnit\Framework\TestCase;
require "..\Model\DB.php";
class DBTest extends TestCase
{
    private function testPrep(): void
    {
        exec('mysql -u root <"C:\Users\maurx\OneDrive - CPNV\Bureau\CPNV\PRW1\Teambuilder\db\teambuilder.sql"');
    }
//    public function TestSelectManyValid(): void
//    {
//        $this->assertEquals('user@example.com',DB::selectMany("SELECT * FROM roles", []));
//    }
    public function TestInsertValid(): void
    {
        $this->assertEquals('user@example.com',insert("INSERT INTO roles(slug,name) VALUES (:slug, :name)", ["slug" => "XXX", "name" => "Slasher"]));
    }
    public function testExecuteValid(): void
    {
        $this->testPrep();
        $db=new DB();
        $db->insert("INSERT INTO roles(slug,name) VALUES (:slug, :name)", ["slug" => "XXX", "name" => "Slasher"]);
        $this->assertEquals(1,$db->execute("UPDATE roles set name = :name WHERE slug = :slug", ["slug" => "XXX", "name" => "Correcteur"]));
    }
    public function testExecute2ndTime(): void
    {
        $this->testPrep();
        $db=new DB();
        $db->insert("INSERT INTO roles(slug,name) VALUES (:slug, :name)", ["slug" => "XXX", "name" => "Slasher"]);
        $db->execute("UPDATE roles set name = :name WHERE slug = :slug", ["slug" => "XXX", "name" => "Correcteur"]);
        $this->assertEquals(0,$db->execute("UPDATE roles set name = :name WHERE slug = :slug", ["slug" => "XXX", "name" => "Correcteur"]));
    }
//    public function TestSelectOneValid(): void
//    {
//        $this->assertEquals('user@example.com',DB::selectOne("SELECT * FROM roles where slug = :slug", ["slug" => "MOD"]));
//    }


}