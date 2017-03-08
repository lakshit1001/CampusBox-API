<?php

 

namespace App;

use App\EventBookmarks;
use League\Fractal;

class EventBookmarksTransformer extends Fractal\TransformerAbstract {

	public function transform(EventBookmarks $eventbookmarks) {
		return [
			// "event_bookmark_id" => (integer) $eventbookmarks->event_bookmark_id ?: 0,
			"event_id" => (integer) $eventbookmarks->event_id ?: 0,
			"username" => (integer) $eventbookmarks->username ?: 0,
		];
	}
}
