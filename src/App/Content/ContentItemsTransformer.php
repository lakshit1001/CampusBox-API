<?php



namespace App;

use App\ContentItems;
use League\Fractal;

class ContentItemsTransformer extends Fractal\TransformerAbstract {

	public function transform(ContentItems $content_items) {
	    if ($content_items->content_item_type == 'text') {
				return [
					"id" => (integer) $content_items->content_item_id ?: 0,
					"type" => (string) $content_items->content_item_type ?: 4,
					"priority" => (string) $content_items->priority ?: 0,
					"description" => (string) $content_items->description ?: null	
				];
			} elseif (($content_items->content_item_type == 'cover') || ($content_items->content_item_type == 'image')) {
				return [
					"id" => (integer) $content_items->content_item_id ?: 0,
					"type" => (string) $content_items->content_item_type ?: 4,
					"priority" => (string) $content_items->priority ?: 0,
					"image" => "https://campusbox.org/dist/api/public/contentsImage/" . $content_items->content_item_id
				];
			} else{
				return [
					"id" => (integer) $content_items->content_item_id ?: 0,
					"type" => (string) $content_items->content_item_type ?: 4,
					"priority" => (integer) $content_items->priority ?: 0,
					"embedUrl" => (string) $content_items->embed_url ?: null,
					"sourceUrl" => (string) $content_items->embed ?: null,
				];
			}
		
	}
}
