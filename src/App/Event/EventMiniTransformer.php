<?php
namespace App;
use App\Event;
use League\Fractal;

class EventMiniTransformer extends Fractal\TransformerAbstract {

    private $params = [];

    function __construct($params = []) {
        $this->params = $params;
    }
     protected $defaultIncludes = [
           // 'SocialAccounts',
          'Tags'
      ];
    public function transform(Event $event) {
        $this->params['value1'] = false;
        $this->params['value2'] = false;
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
            "college_id" => (integer) $event->college_id ?: 0,
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
                    "status" => (bool) $this->params['value1'] ?: false,
                    "total" =>  count($bookmarks) ?: 0,
                ],
                "Participants" => [
                    "status" => (bool) $this->params['value2'] ?: false,
                    "total" =>  count($event->Participants) ?: 0,
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