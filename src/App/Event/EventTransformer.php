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
     protected $defaultIncludes = [
           // 'SocialAccounts',
          'Tags'
      ];
	public function transform(Event $event) {
        if(isset($this->params['type']) && $this->params['type']!= 'get'){
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
                "image" =>(string) $event->image ?: null,
            "organiser" => [
                "name" => (string) $event->organiser_name ?: null,
                "link" =>(string) $event->organiser_link ?: null,
                "phone" =>(string) $event->organiser_phone ?: null,
            ],
            "timings" => [
                "from" => [
                    "date"=>(string) $event->from_date ?: null,
                    "time" =>(string) $event->from_time ?: null,
                    "period"=>(integer) $event->from_period ?: null,
                ],
                "to" => [
                    "date"=> (string) $event->to_date ?: null,
                    "time" =>(string) $event->to_time ?: null,
                    "period"=>(integer) $event->to_period ?: null,
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
            
            "created" => [
                "by" => [
                    "name" => (string) $event->Owner['name'] ?: null,
                    "username" => (integer) $event->Owner['username'] ?: 0,
                    "image" => (string) $event->Owner['image'] ?: null,
                ],
                "at" => $event->time_created ?: 0,
            ],
			
			"links" => [
				"self" => "/events/{$event->id}",
			],
		];
	}
    public function includeTags(Event $event) {
        $tags = $event->Tags;

        return $this->collection($tags, new EventTagsTransformer);
    }
}