<?php



namespace App;

use App\ContentItems;
use League\Fractal;

class ContentItemsTransformer extends Fractal\TransformerAbstract {

	public function transform(ContentItems $content_items) {
		return [
			// "event_bookmark_id" => (integer) $content_items->event_bookmark_id ?: 0,
		"id" => (integer) $content_items->content_item_id ?: 0,
		"priority" => (integer) $content_items->priority ?: 0,
		"type" => (string) $content_items->name ?: 4,
		"description" => (string) $content_items->description ?: null,
		"image" => (string) $content_items->image ?: null,
		"embed" => [
			"html" => (string) $content_items->embed ?: null,
			"url" => (integer) $content_items->embed_url ?: 0,
			],                
		];
		
	}
}
