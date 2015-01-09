<?php

namespace Columnis\Service\Factory;

use Columnis\Model\Page;

class PageFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     *
     * @return Page
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Page();
    }
}
