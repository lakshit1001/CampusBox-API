<?php

namespace App;
use Spot\EntityInterface as Entity;
use Spot\EventEmitter;
use Spot\MapperInterface as Mapper;
use Tuupola\Base62;

class Event extends \Spot\Entity {
	protected static $table = "events";
	public static function fields() {
		return [
		"event_id" => ["type" => "integer", "unsigned" => true, "primary" => true, "autoincrement" => true],
		"college_id" => ["type" => "integer", "unsigned" => true],
		"created_by_username" => ["type" => "string", "required" => true],
		"title" => ["type" => "string", "required" => true],
		"subtitle" => ["type" => "string"],
		"venue" => ["type" => "string", "required" => true],
		"description" => ["type" => "string", "required" => true],
		"contactperson1" => ["type" => "integer"],
		"contactperson2" => ["type" => "integer"],
		"audience" => ["type" => "integer", "required" => true, "default" => "0"],
		"time_created" => ["type" => "datetime", "value" => new \DateTime()],
		"event_type_id" => ["type" => "integer"],
		"event_category_id" => ["type" => "integer"],
		"price" => ["type" => "integer"],
		
		"image" => ["type" => "string"],
		"link" => ["type" => "string"],
		"organiser_name" => ["type" => "string"],
		"organiser_phone" => ["type" => "integer"],
		"organiser_link" => ["type" => "string"],

		"to_date" => ["type" => "string"],
		"to_time" => ["type" => "string"],
		"to_period" => ["type" => "integer"],
		"from_date" => ["type" => "string"],
		"from_time" => ["type" => "string"],
		"from_period" => ["type" => "integer"]



		];
	}
	// public static function events(EventEmitter $emitter) {
	// 	$emitter->on("beforeInsert", function (Entity $entity, MapperInterface $mapper) {
	// 		$entity->event_id = Base62::encode(random_bytes(9));
	// 	});
	// 	$emitter->on("beforeUpdate", function (EntityInterface $entity, MapperInterface $mapper) {
	// 		$entity->time_created = new \DateTime();
	// 	});
	// }
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
		'Tags' => $mapper->hasMany($entity, 'App\EventTags','event_id'),
		//'Type' => $mapper->belongsTo($entity, 'App\EventType', 'event_type_id'),
		//'Owner' => $mapper->belongsTo($entity, 'App\Student', 'created_by_username'),
		//'Bookmarks' => $mapper->hasMany($entity, 'App\EventBookmarks', 'event_id'),
		//'Bookmarked' => $mapper->hasMany($entity, 'App\EventBookmarks', 'event_id'),
			// 'Images' => $mapper->hasMany($entity, 'App\EventImage', 'event_id'),
			//'Updates' => $mapper->hasMany($entity, 'App\EventUpdates', 'event_id'),
			//'Participants' => $mapper->hasManyThrough($entity, 'App\Student', 'App\Participants', 'username', 'event_id'),
			// 'Likes' => $mapper->hasManyThrough($entity, 'App\Student', 'App\EventLikes', 'username', 'event_id'),
			// 'Bookmarked' => $mapper->hasManyThrough($entity, 'App\Student', 'App\EventBookmarks', 'username', 'event_id'),
			//'StudentsBookmarked' => $mapper->hasMany($entity, 'App\EventBookmarks', 'event_id'),
		];
	}
}