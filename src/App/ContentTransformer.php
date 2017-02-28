<?php
namespace App;
use App\Content;
use League\Fractal;

class ContentTransformer extends Fractal\TransformerAbstract {

    private $params = [];

    function __construct($params = []) {
        $this->params = $params;
        $this->params['value'] = false;
    }

    public function transform(Content $content) {

        $bookmarks = $content->Bookmarked->select()->where(['student_id' => '1']);
        $this->params['value'] = (count($bookmarks) > 0 ? true : false); // returns true
        
        return [
            "id" => (integer) $content->content_id ?: 0,
            "title" => (string) $content->title ?: null,
            "content" => [
                "type" => $content->time_created ?: 0,
                "description" => (string) $content->Owner['name'] ?: null,
                "embed" => (string) $content->Owner['name'] ?: null,
                "images" => [
                        "alt" => (string) $content->Tag['name'] ?: null,
                        "link" => (integer) $content->Tag['tag_id'] ?: 0,
                    ],                
                 ],
            "created" => [
                "by" => [
                    "name" => (string) $content->Owner['name'] ?: null,
                    "link" => (integer) $content->Owner['student_id'] ?: 0,
                    "image" => (string) $content->Owner['image'] ?: null,
                    ],
                "at" => $content->time_created ?: 0,
                 ],
            "Actions" => [
                "Appriciate" => [
                    "status" => (bool) $content->created_by_id ?: false,
                    "total" => (integer) $content->created_by_id ?: 0,
                    ]
                "Bookmarked" => [
                    "status" => (bool) $this->params['value'] ?: false,
                    "total" =>  count($content->Bookmarks) ?: 0,
                    ],
                ],
            "details" => [
                "software" => [
                        "name" => (string) $content->Tag['name'] ?: null,
                        "link" => (integer) $content->Tag['tag_id'] ?: 0,
                    ],
                "euquipment" => [
                        "name" => (string) $content->Tag['name'] ?: null,
                        "link" => (integer) $content->Tag['tag_id'] ?: 0,
                         ]
                 ],

            "tags" => [
                    "name" => (string) $content->Tag['name'] ?: null,
                    "link" => (integer) $content->Tag['tag_id'] ?: 0,
                ],
                "total" => (integer) $content->created_by_id ?: 0,
            "links" => [
                "self" => "/contents/{$content->id}",
            ],
        ];
    }
}