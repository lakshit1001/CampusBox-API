<?php
namespace App;
use App\Event;
use League\Fractal;

class EventDashboardTransformer extends Fractal\TransformerAbstract {

	private $params = [];

	function __construct($params = []) {
		$this->params = $params;
		$this->params['value'] = false;
	}

	public function transform(Event $event) {

        if(isset($this->params['type']) && $this->params['type'] == 'get'){
            $bookmarks = $event->Bookmarked;
            for ($i=0; $i < count($bookmarks); $i++) { 
                if($bookmarks[$i]->username == $this->params['username']){
                    $this->params['value1'] = true;
                    break;
                }
            }
            $participants = $event->Participants;
            for ($i=0; $i < count($participants); $i++) { 
                if($participants[$i]->username == $this->params['username']){
                    $this->params['value2'] = true;
                    break;
                }
            }
        } else {
            $bookmarks = null;
            $participants = null;
            $this->params['value1'] = 0;
            $this->params['value2'] = 0;
        }		
        return [
			"id" => (integer) $event->event_id ?: 0,
			"title" => (string) $event->title ?: null,
			"subtitle" => (string) $event->subtitle ?: null,
            "details" => [
                "venue" => (string) $event->venue ?: null,
                "type" => $event->Type['name'] ?: null,
                "price" => (integer) $event->price ?: 0,
            ],
            "image" =>(string) $event->image ?: null,

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
                    "status" => (bool) $this->params['value1'] ?: false,
                    "total" =>  count($bookmarks) ?: 0,
                ],
                "Participants" => [
                    "status" => (bool) $this->params['value2'] ?: false,
                    "total" =>  count($event->Participants) ?: 0,
                ]
            ],
            "contact" => [
                [
                    "name" => (string) $event->ContactPerson1['name'] ?: null,
                    "link" => (string) $event->ContactPerson1['username'] ?: 0,
                    "image" => (string) $event->ContactPerson1['image'] ?: null,
                ],
                [
                    "name" => (string) $event->ContactPerson1['name'] ?: null,
                    "link" => (string) $event->ContactPerson1['username'] ?: 0,
                    "image" => (string) $event->ContactPerson1['image'] ?: null,
                ],
            ],
            "created" => [
                "by" => [
                    "name" => (string) $event->Owner['name'] ?: null,
                    "link" => (string) $event->Owner['username'] ?: 0,
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