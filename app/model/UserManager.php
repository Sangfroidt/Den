<?php

namespace App\Model;

use Nette;
use Nette\Security\Passwords;



/**
 * Users managment.
 */
class UserManager implements Nette\Security\IAuthenticator
{
    use Nette\SmartObject;

    private const
    TABLE_NAME = 'users',
    COLUMN_ID = 'id',
    COLUMN_NAME = 'name',
    COLUMN_PASSWORD_HASH = 'password';

    /** @var Nette\Database\Context */
     private $database;
     

 	public function __construct(Nette\Database\Context $database)
 	{
 		$this->database = $database;
    }
     
    /**
 	 * Performs an authentication.
 	 * @throws Nette\Security\AuthenticationException
    */
    public function authenticate(array $credentials): Nette\Security\IIdentity
    {
        [$name, $password] = $credentials;
        //vytažení z dabáze
        $row = $this->database->table('users')
            ->where(self::COLUMN_NAME, $name)
            ->fetch();
        //pokud neexistuje hodí exception
        if (!$row) {
            throw new Nette\Security\AuthenticationException('Toto jméno neexistuje.', self::IDENTITY_NOT_FOUND);
        } 
        elseif (!(new Nette\Security\Passwords)->verify($password, $row[self::COLUMN_PASSWORD_HASH])) {
            throw new Nette\Security\AuthenticationException('Heslo není správně.', self::INVALID_CREDENTIAL);
        }
        //rehash v případě, že potřebuje nový hash
        elseif ((new Nette\Security\Passwords)->needsRehash($row[self::COLUMN_PASSWORD_HASH])) {
            $row->update([
                self::COLUMN_PASSWORD_HASH => (new Nette\Security\Passwords)->hash($password),
            ]);
        }
        

        $arr = $row->toArray();
        unset($arr[self::COLUMN_PASSWORD_HASH]);
        return new Nette\Security\Identity($row[self::COLUMN_NAME], $arr);
    }

    //přidá uživatele do databáze
 	public function add(string $name, string $password): void
 	{
    //není ošetřena duplicita jmen
 	try {
 		$this->database->table('users')->insert([
 		self::COLUMN_NAME=> $name,
 		self::COLUMN_PASSWORD_HASH => (new Nette\Security\Passwords)->hash($password)]);
 		} catch (Nette\Database\UniqueConstraintViolationException $e) {
	}
 }

}