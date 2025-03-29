<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\ProductApi;
use App\Entity\Notification;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductStateProcessor implements ProcessorInterface
{

    public function __construct(
        private readonly EntityClassDtoStateProcessor $innerProcessor,
        private readonly EntityManagerInterface       $em,
        private readonly ProductRepository            $productRepository,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        assert($data instanceof ProductApi);
        $result = $this->innerProcessor->process($data, $operation, $uriVariables, $context);

        $previousData = $context['previous_data'] ?? null;
        if ($previousData instanceof ProductApi
            && $data->isVerified
            && $previousData->isVerified !== $data->isVerified
        ) {
            $entity = $this->productRepository->find($data->id);
            $notification = new Notification();
            $notification->setProduct($entity);
            $notification->setMessage('Product Has Been Verified');
            $this->em->persist($notification);
            $this->em->flush();
        }

        return $result;
    }
}
