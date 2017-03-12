<?php

 

namespace App;

use Spot\EntityInterface as Entity;
use Spot\EventEmitter;
use Spot\MapperInterface as Mapper;
use Tuupola\Base62;

class ContentTags extends \Spot\Entity {
	protected static $table = "content_tags";

	public static function fields() {
		return [

			"content_tag_id" => ["type" => "integer", "unsigned" => true, "primary" => true, "autoincrement" => true],
			"content_id" => ["type" => "integer", "unsigned" => true],
			"name" => ["type" => "string", "required" => true],
		];
	}

	public static function contents(EventEmitter $emitter) {
		// $emitter->on("beforeInsert", function (EntityInterface $entity, MapperInterface $mapper) {
		// 	$entity->content_tag_id = Base62::encode(random_bytes(9));
		// });

		// $emitter->on("beforeUpdate", function (EntityInterface $entity, MapperInterface $mapper) {
		// 	$entity->time_created = new \DateTime();
		// });
	}
	public function timestamp() {
		return $this->time_created->getTimestamp();
	}

	public function etag() {
		return md5($this->id . $this->timestamp());
	}

	public function clear() {
		$this->data([
		]);
	}

	public static function relations(Mapper $mapper, Entity $entity) {
		return [
			'Content' => $mapper->belongsTo($entity, 'App\Content', 'content_id'),
		];
	}
}
