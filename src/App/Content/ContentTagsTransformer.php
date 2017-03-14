<?php

 

namespace App;

use App\ContentTags;
use League\Fractal;

class ContentTagsTransformer extends Fractal\TransformerAbstract {

	public function transform(ContentTags $content_tags) {
		return [
			// "event_bookmark_id" => (integer) $content_tags->event_bookmark_id ?: 0,
			"name" => (string) $content_tags->name ?: 4,
		];
	}
}
