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
        "id" => (integer) $event->event_id ?: 0,
        "title" => (string) $event->title ?: null,
        "subtitle" => (string) $event->subtitle ?: null,
        "details"=> [
            "venue" => (string) $event->venue ?: null,
            "type" => $event->Type['name']?:null,
            "team" => (integer) $event->price ?: 0,
            "price" => (integer) $event->price ?: 0,
            "description" => (string) $event->description ?: null,
            "rules" => (string) $event->description ?: null,
            ],
        "contact" => [
            [
                "name" => (string) $event->ContactPerson1['name'] ?: null,
                "link" =>  (integer) $event->ContactPerson1['student_id'] ?: 0,
                "image" => (string) $event->ContactPerson1['image'] ?: null
            ],
            [
                (integer) $event->ContactPerson1 ?: 0,
                "name" => (string) $event->ContactPerson1['name'] ?: null,
                "link" =>  (integer) $event->ContactPerson1['student_id'] ?: 0,
                "image" => (string) $event->ContactPerson1['image'] ?: null
                (integer) $event->contactperson2 ?: 0,
            ],
            ],
        "created"=> [
            "by" => [
                "name" => (string) $event->Owner['name'] ?: null,
                "link" =>  (integer) $event->Owner['student_id'] ?: 0,
                "image" => (string) $event->Owner['image'] ?: null
                ],
            "at" => $event->time_created ?: 0,
            ],
        "Actions"=> [
            "Bookmarked"=> [
                "status"=>(bool) $event->created_by_id ?: false, 
                "total" =>(integer) $event->created_by_id ?: 0,
            ],
            "Participants"=> [
                "status"=>(bool) $event->created_by_id ?: false, 
                "total" =>(integer) $event->created_by_id ?: 0,
            ],
            "Bookmarked"=> [
                "status"=>(bool) $event->created_by_id ?: false, 
                "total" =>(integer) $event->created_by_id ?: 0,
            ],
            ],
            "tags"=> [
                [
                "name" => (string) $event->Tag['name'] ?: null,
                "link" =>  (integer) $event->Tag['tag_id'] ?: 0,
                ],
                            "total" =>(integer) $event->created_by_id ?: 0,
            ],
        "links" => [
            "self" => "/events/{$event->id}",
        ],
        ];
    }
    public function includeBookmarks(Event $event) {
        $bookmarks = $event->StudentsBookmarked;

        return $this->collection($bookmarks, new EventBookmarksTransformer);
    }
    public function includeBookmarks(Event $event) {
        $bookmarks = $event->StudentsBookmarked;

        return $this->collection($bookmarks, new EventBookmarksTransformer);
        }
}