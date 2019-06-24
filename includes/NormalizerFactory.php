<?php

namespace Dation\Woocommerce;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class NormalizerFactory {
	/** @var ObjectNormalizer $normalizer */
	private static $normalizer;

	public static function getNormalizer(): ObjectNormalizer{
		if(null === self::$normalizer) {
			return self::createNormalizer();
		} else {
			return self::$normalizer;
		}
	}

	private static function createNormalizer(): ObjectNormalizer {
		// a full list of extractors is shown further below
		$phpDocExtractor = new PhpDocExtractor();
		$reflectionExtractor = new ReflectionExtractor();

		// list of PropertyListExtractorInterface (any iterable)
		$listExtractors = [$reflectionExtractor];

		// list of PropertyTypeExtractorInterface (any iterable)
		$typeExtractors = [$phpDocExtractor, $reflectionExtractor];

		// list of PropertyDescriptionExtractorInterface (any iterable)
		$descriptionExtractors = [$phpDocExtractor];

		// list of PropertyAccessExtractorInterface (any iterable)
		$accessExtractors = [$reflectionExtractor];

		// list of PropertyInitializableExtractorInterface (any iterable)
		$propertyInitializableExtractors = [$reflectionExtractor];

		$propertyInfo = new PropertyInfoExtractor(
			$listExtractors,
			$typeExtractors,
			$descriptionExtractors,
			$accessExtractors,
			$propertyInitializableExtractors
		);

		return new ObjectNormalizer(null, null, null, $propertyInfo);
	}
}