<?php
namespace App\Security\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Ldap\Entry;
use Doctrine\DBAL\DriverManager;


class LdapUser implements UserInterface
{
    const LDAP_KEY_DISPLAY_NAME = 'displayName';
    const LDAP_KEY_MAIL = 'mail';

    protected $username;
    protected $password;
    protected $roles;
    protected $ldapEntry;
    protected $displayName;
    protected $eMail;
    protected $active;
    public function __construct($username, $password, array $roles, Entry $ldapEntry)
    {
        if ('' === $username || null === $username) {
            throw new \InvalidArgumentException('The username cannot be empty.');
        }
        $this->username = $username;
        $this->password = $password;
        $this->ldapEntry = $ldapEntry;
        $this->displayName = $this->extractSingleValueByKeyFromEntry(
            $ldapEntry,
            self::LDAP_KEY_DISPLAY_NAME,
            $username
        );
        $this->eMail = $this->extractSingleValueByKeyFromEntry($ldapEntry, self::LDAP_KEY_MAIL);
        $this->getUserDB();
        if($this->active != true){
            throw new CustomUserMessageAuthenticationException('Account is disabled.');
        }
    }

    protected function getUserDB()
    {
        $dsn=['url' => getenv('DATABASE_URL')];
		$dbh = DriverManager::getConnection($dsn);
		$stmt=$dbh->prepare('SELECT active FROM `users` WHERE username = :username LIMIT 1');
        $stmt->bindParam(':username',$this->username);
        $stmt->execute();
        $this->active=$stmt->fetch(\PDO::FETCH_COLUMN);
        $stmt = $dbh->prepare('CALL get_user_roles (:username);');
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();
        $this->roles=$stmt->fetchALL(\PDO::FETCH_COLUMN);

    }

    /**
     * @return Collection|Groups[]
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function __toString()
    {
        return (string)$this->getUsername();
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @return Entry
     */
    public function getLdapEntry()
    {
        return $this->ldapEntry;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {

        if($this->active==true){
            $roles = $this->roles;
            $roles[] = 'ROLE_USER';
            return array_unique($roles);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }

    /**
     * Extracts single value from entry's array value by key.
     *
     * @param Entry $entry Ldap entry
     * @param string $key Key
     * @param null|string $defaultValue Default value
     *
     * @return string|null
     */
    protected function extractSingleValueByKeyFromEntry(Entry $entry, $key, $defaultValue = null)
    {
        $value = $this->extractFromLdapEntry($entry, $key, $defaultValue);
        return is_array($value) && isset($value[0]) ? $value[0] : $defaultValue;
    }

    /**
     * Extracts value from entry by key.
     *
     * @param Entry $entry Ldap entry
     * @param string $key Key
     * @param mixed $defaultValue Default value
     *
     * @return array|mixed
     */
    protected function extractFromLdapEntry(Entry $entry, $key, $defaultValue = null)
    {
        if (!$entry->hasAttribute($key)) {
            return $defaultValue;
        }
        return $entry->getAttribute($key);
    }
}