<?php

/*
 * This file is part of the Slim API skeleton package
 *
 * Copyright (c) 2016-2017 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/slim-api-skeleton
 *
 */

namespace App;

use App\EventBookmarks;
use League\Fractal;

class EventBookmarksTransformer extends Fractal\TransformerAbstract {

	public function transform(EventBookmarks $eventbookmarks) {
		return [
			// "event_bookmark_id" => (integer) $eventbookmarks->event_bookmark_id ?: 0,
			"event_id" => (integer) $eventbookmarks->event_id ?: 0,
			"student_id" => (integer) $eventbookmarks->student_id ?: 0,
		];
	}
}
