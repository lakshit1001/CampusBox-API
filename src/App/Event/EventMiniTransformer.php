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
    'tags'
    ];

    public function transform(Event $event) {
        $this->params['state'] = 0;             // State implies state of going or interested
        $participants = null;
        if(isset($this->params['type']) && $this->params['type'] == 'get'){

            $participants = $event->Participants;
            for ($i=0; $i < count($participants); $i++) { 
                if($participants[$i]->username == $this->params['username']){

                    if ((bool)$participants[$i]->state)
                        $this->params['state'] = 1;
                    else
                        $this->params['state'] = 2;
                    
                    break;
                }
            }

            /**
             * Decides if event is an online event or offline
             * 
             * True means Offline event and city of the event's location is sent
             * False means Online event and base domain of the  event's link is sent
             */
            if ((bool)$event->loc_type) {
                $loc = explode(',', $event->venue);
                $this->params['location_data'] = (string) end($loc) ?: null;
            }
            else{
                $pieces = parse_url($event->link);
                $domain = isset($pieces['host']) ? $pieces['host'] : '';
                if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) 
                    $this->params['location_data'] = (string) $regs['domain'] ?: null;
                else
                    $this->params['location_data'] = null;
            }

            /**
             * Get date and month from datetime
             */
            $date = new \DateTime($event->from_date);
            $this->params['date'] = $date->format('d');
            $this->params['month'] = $date->format('M');

        }
        return [
        "id" => (integer) $event->event_id ?: 0,
        "title" => (string) $event->title ?: null,
        "subtitle" => (string) $event->subtitle ?: null,
        "type" => (int) $event->event_type_id ?: 0,
        "price" => (integer) $event->price ?: 0,
        "image" =>"http://campusbox.org/dist/api/public/eventsImage/".$event->event_id,
        "college_id" => (integer) $event->college_id ?: 0,
        "audience" => (string) $event->audience ?: null,
        "participants" => [
        "status" => (int) $this->params['state'] ?: 0,
        "total" =>  count($participants) ?: 0,
        ],
        "location" => [
        "type" => (bool)$event->loc_type,
        "data" => (string) $this->params['location_data'] ?: null
        ],
        "timings" => [
        "day" => $this->params['date'] ?: null,
        "month" => $this->params['month'] ?: null
        ],

        // "deails" => [
        // "venue" => (string) $event->venue ?: null,
        // "team" => (integer) $event->price ?: 0,
        // "price" => (integer) $event->price ?: 0,
        // "description" => (string) $event->description ?: null,
        // "rules" => (string) $event->description ?: null,
        // ],
        // "image" =>(string) $event->image ?: null,
        // "organiser" => [
        // "name" => (string) $event->organiser_name ?: null,
        // "link" =>(string) $event->organiser_link ?: null,
        // "phone" =>(string) $event->organiser_phone ?: null,
        // ],
        // "timings" => [
        // "from" => [
        // "date"=>(string) $event->from_date ?: null,
        // "time" =>(string) $event->from_time ?: null,
        // "period"=>(integer) $event->from_period ?: null,
        // ],
        // "to" => [
        // "date"=> (string) $event->to_date ?: null,
        // "time" =>(string) $event->to_time ?: null,
        // "period"=>(integer) $event->to_period ?: null,
        // ],
        // ],


        // "created" => [
        // "by" => [
        // "name" => (string) $event->Owner['name'] ?: null,
        // "username" => (integer) $event->Owner['username'] ?: 0,
        // "image" => (string) $event->Owner['image'] ?: null,
        // ],
        // "at" => $event->time_created ?: 0,
        // ],

        // "links" => [
        // "self" => "/events/{$event->id}",
        // ],
        ];
    }
    public function includetags(Event $event) {
        $tags = $event->Tags;

        return $this->collection($tags, new EventTagsTransformer);
    }
}