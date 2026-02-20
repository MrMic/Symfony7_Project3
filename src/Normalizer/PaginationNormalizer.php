<?php

namespace App\Normalizer;

use App\Entity\Recipe;
use ArrayObject;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class PaginationNormalizer implements NormalizerInterface
{
    // INFO: CONSTRUCTOR ─────────────────────────────────────────────────────
    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly NormalizerInterface $normalizer
    ) {
        //
    }

    // ______________________________________________________________________
    public function normalize(
        mixed $data,
        ?string $format = null,
        array $context = []
    ): array|string|int|float|bool|ArrayObject|null {
        if (!($data instanceof PaginationInterface)) {
            throw new \RuntimeException('The data must be an instance of PaginationInterface.');
        }

        return [
            'items' => array_map(
                fn(Recipe $recipe) => $this->normalizer->normalize($recipe, $format, $context),
                $data->getItems()
            ),
            'total' => $data->getTotalItemCount(),
            'page' => $data->getCurrentPageNumber(),
            'lastPage' => ceil($data->getTotalItemCount() / $data->getItemNumberPerPage()),
        ];
    }

    // ______________________________________________________________________
    public function supportsNormalization(
        mixed $data,
        ?string $format = null,
        array $context = []
    ): bool {
        return $data instanceof PaginationInterface ;
    }

    // ______________________________________________________________________
    public function getSupportedTypes(?string $format): array
    {
        return [
            PaginationInterface::class => true,
        ];
    }
}
