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
  protected $defaultIncludes = [
           // 'SocialAccounts',
          'Tags',
          'Items'

      ];
    public function transform(Content $content) {

        $appreciates = $content->Appreciated->select()->where(['username' => 'lakshit1001']);
        $this->params['appreciateValue'] = (count($appreciates) > 0 ? true : false); // returns true
       $bookmarks = $content->Bookmarked->select()->where(['username' => 'lakshit1001']);
        $this->params['bookmarkValue'] = (count($bookmarks) > 0 ? true : false); // returns true
        
        return [
            "id" => (integer) $content->content_id ?: 0,
            "title" => (string) $content->title ?: null,
            "content" => [
                "type" => $content->time_created ?: 0,
                "description" => (string) $content->description ?: null,
                "embed" => (string) $content->Owner['name'] ?: null,
                "images" => [
                        "alt" => (string) $content->Tag['name'] ?: null,
                        "link" => (integer) $content->Tag['tag_id'] ?: 0,
                    ],                
                 ],
            "created" => [
                "by" => [
                    "name" => (string) $content->Owner['name'] ?: null,
                    "username" => (integer) $content->Owner['username'] ?: 0,
                    "link" => (integer) $content->Owner['username'] ?: 0,
                    "image" => (string) $content->Owner['image'] ?: null,
                    ],
                "at" => $content->timer ?: 0,
                 ],
            "Actions" => [
                "Appriciate" => [
                    "status" => (bool) $this->params['appreciateValue'] ?: false,
                    "total" => (integer) count($content->Appreciates) ?: 0,
                    ],
                "Bookmarked" => [
                    "status" => (bool) $this->params['bookmarkValue'] ?: false,
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
                "total" => (integer) $content->created_by_username ?: 0,
            "links" => [
                "self" => "/contents/{$content->id}",
            ],
        ];
    }
     public function includeTags(Content $content) {
        $tags = $content->Tags;

        return $this->collection($tags, new ContentTagsTransformer);
    }
     public function includeItems(Content $content) {
        $items = $content->Items;

        return $this->collection($items, new ContentItemsTransformer);
    }
}