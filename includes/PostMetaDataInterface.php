<?php
declare(strict_types=1);

namespace Dation\Woocommerce;

interface PostMetaDataInterface {

	public function getPostMeta(int $postId, string $metaKey, bool $single);
}