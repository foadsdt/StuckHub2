<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Doctrine\Common\State\RemoveProcessor;
use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\UserApi;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\PasswordHasher;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class EntityClassDtoStateProcessor implements ProcessorInterface
{
    public function __construct(
        private UserRepository     $userRepository,
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $processor,
        #[Autowire(service: RemoveProcessor::class)]
        private ProcessorInterface $removeProcessor,
        private PasswordHasher     $passwordHasher,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        assert($data instanceof UserApi);

        $entity = $this->mapDtoToEntity($data);

        if ($operation instanceof DeleteOperationInterface) {
            $this->removeProcessor->process($entity, $operation, $uriVariables, $context);

            return null;
        }

        $this->processor->process($entity, $operation, $uriVariables, $context);

        $data->id = $entity->getId();

        return $data;

    }

    private function mapDtoToEntity(object $dto): object
    {
        assert($dto instanceof UserApi);

        if ($dto->id) {
            $entity = $this->userRepository->find($dto->id);

            if (!$entity) {
                throw new \Exception(sprintf('User with id %d not found', $dto->id));
            }

        } else {
            $entity = new User();
        }

        $entity->setEmail($dto->email);
        $entity->setUsername($dto->username);

        if ($dto->password) {
            $entity->setPassword($this->passwordHasher->hash($dto->password));
        }

        // TODO: handle Products

        return $entity;
    }
}
