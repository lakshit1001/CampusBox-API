<?php
namespace App;
use App\Content;
use League\Fractal;

class ContentMiniTransformer extends Fractal\TransformerAbstract {

    private $params = [];

    function __construct($params = []) {
        $this->params = $params;
        $this->params['value'] = false;
    }

    protected $defaultIncludes = [
    'items'
    ];

    public function transform(Content $content) {

        $appreciates = null;
        $bookmarks = null;
        $this->params['appreciateValue'] = 0;
        $this->params['bookmarkValue'] = 0;
        if(isset($this->params['type']) && $this->params['type'] == 'get'){
            $appreciates = $content->Appreciated;
            for ($i=0; $i < count($appreciates); $i++) { 
                if($appreciates[$i]->username == $this->params['username']){
                    $this->params['appreciateValue'] = true;
                    break;
                }
            }
            $bookmarks = $content->Bookmarked;
            for ($i=0; $i < count($bookmarks); $i++) { 
                if($bookmarks[$i]->username == $this->params['username']){
                    $this->params['bookmarkValue'] = true;
                    break;
                }
            }
        }

        $type = $content->Type['default_view_type'];

        $this->params['view_type'] = $type;

        return [
        "id" => (integer) $content->content_id ?: 0,
        "title" => (string) $content->title ?: null,
        "view_type" => (integer) $this->params['view_type'],
        "content_type" => $content->content_type_id ?: 0,              
        "created_at" => $content->timer ?: 0,
        "owner" => [
        "username" => (string) $content->Owner['username'] ?: null,
        "name" => (string) $content->Owner['name'] ?: null,
        "about" => (string) $content->Owner['about'] ?: null,
        "photo" => (string) $content->Owner['image'] ?: null,
        ],
        "actions" => [
        "appreciate" => [
        "status" => (bool) $this->params['appreciateValue'] ?: false,
        "total" => (integer) count($appreciates) ?: 0,
        ],
        "bookmarks" => [
        "status" => (bool) $this->params['bookmarkValue'] ?: false,
        "total" =>  count($bookmarks) ?: 0,
        ],
        ],
        ];
    }
    public function includeitems(Content $content) {
        $items = $content->Items;

        return $this->collection($items, new ContentItemsTransformer);
    }
}