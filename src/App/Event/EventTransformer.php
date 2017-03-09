<?php
namespace App;
use App\Event;
use League\Fractal;

class EventTransformer extends Fractal\TransformerAbstract {

	private $params = [];

	function __construct($params = []) {
		$this->params = $params;
		$this->params['value'] = false;
	}
	public function transform(Event $event) {
        if($this->params['type'] != 'get'){
            $bookmarks = $event->Bookmarked->select()->where(['username' => $this->params['value']]);
            $this->params['value'] = (count($bookmarks) > 0 ? true : false); // returns true
        } else {
            $bookmarks = null;
            $this->params['value'] = 0;
        }
        return [
			"id" => (integer) $event->event_id ?: 0,
			"title" => (string) $event->title ?: null,
			"subtitle" => (string) $event->subtitle ?: null,
            "details" => [
                "venue" => (string) $event->venue ?: null,
                "type" => $event->Type['name'] ?: null,
                "team" => (integer) $event->price ?: 0,
                "price" => (integer) $event->price ?: 0,
                "description" => (string) $event->description ?: null,
                "rules" => (string) $event->description ?: null,
            ],
            "timings" => [
                "date" => [
                    "start" => (string) $event->date_start ?: null,
                    "end" => (string) $event->date_end ?: null,
		          	],
                "time" => [
                    "start" => (string) $event->date_start ?: null,
                    "end" => (string) $event->date_end ?: null,
                 ],
            ],
            "Actions" => [
				"Bookmarked" => [
					"status" => (bool) $this->params['value'] ?: false,
					"total" =>  count($event->Bookmarks) ?: 0,
                    "bookmarks" => count($bookmarks),
				],
				"Participants" => [
					"status" => (bool) $event->created_by_username ?: false,
					"total" => (integer) $event->created_by_username ?: 0,
				]
			],
            "contact" => [
                [
                    "name" => (string) $event->ContactPerson1['name'] ?: null,
                    "username" => (integer) $event->ContactPerson1['username'] ?: 0,
                    "link" => (integer) $event->ContactPerson1['username'] ?: 0,
                    "image" => (string) $event->ContactPerson1['image'] ?: null,
                ],
                [
                    "name" => (string) $event->ContactPerson1['name'] ?: null,
                    "link" => (integer) $event->ContactPerson1['username'] ?: 0,
                    "link" => (integer) $event->ContactPerson1['username'] ?: 0,
                    "image" => (string) $event->ContactPerson1['image'] ?: null,
                ],
            ],
            "created" => [
                "by" => [
                    "name" => (string) $event->Owner['name'] ?: null,
                    "username" => (integer) $event->Owner['username'] ?: 0,
                    "link" => (integer) $event->Owner['username'] ?: 0,
                    "image" => (string) $event->Owner['image'] ?: null,
                ],
                "at" => $event->time_created ?: 0,
            ],
			"tags" => [
				[
					"name" => (string) $event->Tag['name'] ?: null,
					"link" => (integer) $event->Tag['tag_id'] ?: 0,
				],
				"total" => (integer) $event->created_by_username ?: 0,
			],
			"links" => [
				"self" => "/events/{$event->id}",
			],
		];
	}
}