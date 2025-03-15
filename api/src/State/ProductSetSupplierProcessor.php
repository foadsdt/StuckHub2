<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator('api_platform.doctrine.orm.state.persist_processor')]
class ProductSetSupplierProcessor implements ProcessorInterface
{

    public function __construct(
        private ProcessorInterface $decoratedProcessor,
        private Security           $security
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        /** @var User $user */
        $user = $this->security->getUser();

        if ($data instanceof Product && !$data->getSupplier() && $user) {
            $data->setSupplier($user);
        }

//        return $this->decoratedProcessor->process($data, $operation, $uriVariables, $context);
         $processor = $this->decoratedProcessor->process($data, $operation, $uriVariables, $context);

        if($data instanceof Product) {
            $data->setIsSuppliedByAuthenticatedUser(
                $this->security->getUser() === $data->getSupplier()
            );
        }

        return $processor;

        // Handle the state


    }
}
