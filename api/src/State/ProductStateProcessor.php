<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

//#[AsDecorator('api_platform.doctrine.orm.state.persist_processor')]
class ProductStateProcessor implements ProcessorInterface
{

    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $decoratedProcessor,
        private Security           $security
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        /** @var User $user */
        $user = $this->security->getUser();

        assert($data instanceof Product);

        if (!$data->getSupplier() && $user) {
            $data->setSupplier($user);
        }

//        return $this->decoratedProcessor->process($data, $operation, $uriVariables, $context);
        $processor = $this->decoratedProcessor->process($data, $operation, $uriVariables, $context);

        $data->setIsSuppliedByAuthenticatedUser(
            $this->security->getUser() === $data->getSupplier()
        );

        return $processor;

        // Handle the state


    }
}
