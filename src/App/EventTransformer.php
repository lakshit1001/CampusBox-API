<?php
namespace App;
use App\Event;
use League\Fractal;

class EventTransformer extends Fractal\TransformerAbstract {
	protected $availableIncludes = [
		'bookmarks',
	];

	public function transform(Event $event) {
		return [
			"event_id" => (integer) $event->event_id ?: 0,
			"college_id" => (integer) $event->college_id ?: 0,
			"created_by_id" => (integer) $event->created_by_id ?: 0,
			"title" => (string) $event->title ?: null,
			"subtitle" => (string) $event->subtitle ?: null,
			"description" => (string) $event->description ?: null,
			"contactperson1" => (integer) $event->contactperson1 ?: 0,
			"contactperson2" => (integer) $event->contactperson2 ?: 0,
			"venue" => (string) $event->venue ?: null,
			"inter" => (integer) $event->inter ?: 0,
			"time_created" => $event->time_created ?: 0,
			"type" => $event->Type['name'],
			"price" => (integer) $event->price ?: 0,
			"created_by" => (string) $event->Owner['name'] ?: null,
			// "bookmarks" => $event->Bookmarked,
			"participants" => $event->Participants,
			"Owner" => $event->Owner->name ?: null,
			"links" => [
				"self" => "/events/{$event->id}",
			],
		];
	}
	public function includeBookmarks(Event $event) {
		$bookmark = $event->event_id;

		return $this->item($bookmarks, new EventBookmarksTransformer);
	}
}