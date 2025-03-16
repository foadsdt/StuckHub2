<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Notification;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

//#[AsDecorator('api_platform.doctrine.orm.state.persist_processor')]
class ProductStateProcessor implements ProcessorInterface
{

    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface     $decoratedProcessor,
        private Security               $security,
        private EntityManagerInterface $entityManager,
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
        $this->decoratedProcessor->process($data, $operation, $uriVariables, $context);

        $data->setIsSuppliedByAuthenticatedUser(
            $this->security->getUser() === $data->getSupplier()
        );

        $previousData = $context['previous_data'] ?? null;
        if ($previousData instanceof Product
            && $data->getIsVerified()
            && $data->getIsVerified() !== $previousData->getIsVerified()
        ) {
            $notification = new Notification();
            $notification->setProduct($data);
            $notification->setMessage('Product has been verified');
            $this->entityManager->persist($notification);
            $this->entityManager->flush();
        }

        return $data;

        // Handle the state


    }
}
