<?php

namespace FlexAuth\Type\Entity;

use Doctrine\Common\Persistence\ManagerRegistry;
use FlexAuth\Type\UserProviderFactoryInterface;
use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;

/**
 * Class EntityUserProviderFactory
 * @author Aleksandr Arofikin <sashaaro@gmail.com>
 */
class EntityUserProviderFactory implements UserProviderFactoryInterface
{
    const TYPE = 'entity';

    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function create($params)
    {
        return new EntityUserProvider($this->managerRegistry, $params['class'], $params['property']);
    }
}