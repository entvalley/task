<?php

namespace Entvalley\AppBundle\Service;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class AclManager
{
    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $context;

    /**
     * @var \Symfony\Component\Security\Acl\Model\AclProviderInterface
     */
    protected $provider;

    /**
     * @param \Symfony\Component\Security\Acl\Model\AclProviderInterface $provider
     * @param \Symfony\Component\Security\Core\SecurityContextInterface $context
     */
    public function __construct(AclProviderInterface $provider, SecurityContextInterface $context)
    {
        $this->provider = $provider;
        $this->context = $context;
    }

    /**
     * @param $entity
     * @param int $mask
     * @param $user
     * @return AclManager
     */
    public function grant($entity, $mask = MaskBuilder::MASK_OWNER, $user = null)
    {
        $acl = $this->getAcl($entity);
        $acl->insertObjectAce($this->getSecurityIdentity($user), $mask);
        $this->provider->updateAcl($acl);
        return $this;
    }

    /**
     * @param $entity
     * @param int $mask
     * @return AclManager
     */
    public function update($entity, $mask = MaskBuilder::MASK_OWNER)
    {
        $acl = $this->getAcl($entity);
        $aces = $acl->getObjectAces();

        $securityIdentity = $this->getSecurityIdentity();

        foreach ($aces as $index => $ace) {
            if ($securityIdentity->equals($ace->getSecurityIdentity())) {
                $acl->updateObjectAce($index, $ace->getMask() & $mask);
            }
        }

        $this->provider->updateAcl($acl);
        return $this;
    }

    /**
     * @return \Symfony\Component\Security\Acl\Domain\UserSecurityIdentity
     */
    private function getSecurityIdentity($user = null)
    {
        if ($user === null) {
            $user = $this->context->getToken()->getUser();
        }
        $securityIdentity = UserSecurityIdentity::fromAccount($user);
        return $securityIdentity;
    }

    /**
     * @param $entity
     * @return \Symfony\Component\Security\Acl\Model\AclInterface
     */
    private function getAcl($entity)
    {
        $aclProvider = $this->provider;
        $objectIdentity = ObjectIdentity::fromDomainObject($entity);
        $acl = $aclProvider->findAcl($objectIdentity);

        if (!$acl) {
            $acl = $aclProvider->createAcl($objectIdentity);
        }

        return $acl;
    }
}