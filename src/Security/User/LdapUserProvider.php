<?php
namespace App\Security\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Security\Core\User\LdapUserProvider as BaseLdapUserProvider;
class LdapUserProvider extends BaseLdapUserProvider
{
    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof LdapUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        return new LdapUser($user->getUsername(), null, $user->getRoles(), $user->getLdapEntry());
    }
    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $class === 'App\Security\User\LdapUser';
    }
    /**
     * {@inheritdoc}
     */
    protected function loadUser($username, Entry $entry)
    {
        $user = parent::loadUser($username, $entry);
        return new LdapUser($username, $user->getPassword(), $user->getRoles(), $entry);
    }
}