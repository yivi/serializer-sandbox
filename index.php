<?php

require 'vendor/autoload.php';

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class UserDTO {
    /**
     * @param AddressDTO[] $addressBook
     */
    public function __construct(
        public string $name,
        public int $age,
        public ?AddressDTO $billingAddress,
        public ?AddressDTO $shippingAddress,
        public array $addressBook,
    ) {
    }
}

class AddressDTO {
    public function __construct(
        public string $street,
        public string $city,
    ) {
    }
}

$encoders = [new JsonEncoder()];

$extractor = new PropertyInfoExtractor([], [
    new PhpDocExtractor(),
    new ReflectionExtractor(),
]);

$normalizers = [
    new ObjectNormalizer(null, null, null, $extractor),
    new ArrayDenormalizer(),
];

$serializer = new Serializer($normalizers, $encoders);

$address = new AddressDTO('Rue Paradis', 'Marseille');
$user = new UserDTO('John', 25, $address, null, [$address]);

$jsonContent = $serializer->serialize($user, 'json');

dump($serializer->deserialize($jsonContent, UserDTO::class, 'json'));