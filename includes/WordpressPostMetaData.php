<?php
declare(strict_types=1);

namespace Dation\Woocommerce;

class WordpressPostMetaData implements PostMetaDataInterface {

	public function getPostMeta(int $postId, string $metaKey, bool $single) {
		return get_post_meta($postId, $metaKey, $single);
	}
}