<?php

 

namespace App;

use App\EventTags;
use League\Fractal;

class EventTagsTransformer extends Fractal\TransformerAbstract {

	public function transform(EventTags $event_tags) {
		return [
			// "event_bookmark_id" => (integer) $event_tags->event_bookmark_id ?: 0,
			"id" => (integer) $event_tags->event_tag_id ?: 0,
			"name" => (string) $event_tags->name ?: 4,
			"eventId" => (integer) $event_tags->event_id ?: 0,
		];
	}
}
