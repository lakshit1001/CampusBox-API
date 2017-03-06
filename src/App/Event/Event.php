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
			"created_by_id" => ["type" => "integer", "required" => true],
			"title" => ["type" => "string", "required" => true],
			"subtitle" => ["type" => "string"],
			"description" => ["type" => "string", "required" => true],
			"contactperson1" => ["type" => "integer"],
			"contactperson2" => ["type" => "integer"],
			"venue" => ["type" => "string", "required" => true],
			"inter" => ["type" => "integer", "required" => true, "default" => "0"],
			"time_created" => ["type" => "datetime", "value" => new \DateTime()],
			"event_type_id" => ["type" => "string"],
			"price" => ["type" => "integer"],
			"titlescore" => ["type" => "integer"],
			"descriptionscore" => ["type" => "integer"],
			"score" => ["type" => "integer"],
		];
	}
	public function clear() {
		$this->data([
		]);
	}
	public static function relations(Mapper $mapper, Entity $entity) {
		return [
			// 'Images' => $mapper->hasMany($entity, 'App\EventImage', 'event_id'),
			//'Updates' => $mapper->hasMany($entity, 'App\EventUpdates', 'event_id'),
			//  'Type' => $mapper->belongsTo($entity, 'App\EventType', 'event_type_id'),
			//'Owner' => $mapper->belongsTo($entity, 'App\Student', 'created_by_id'),
			//'Participants' => $mapper->hasManyThrough($entity, 'App\Student', 'App\Participants', 'student_id', 'event_id'),
			// 'Tags' => $mapper->hasManyThrough($entity, 'App\Tag', 'App\ContentCategory', 'tag_id', 'event_id'),
			// 'Likes' => $mapper->hasManyThrough($entity, 'App\Student', 'App\EventLikes', 'student_id', 'event_id'),
			// 'Bookmarked' => $mapper->hasManyThrough($entity, 'App\Student', 'App\EventBookmarks', 'student_id', 'event_id'),
			//'StudentsBookmarked' => $mapper->hasMany($entity, 'App\EventBookmarks', 'event_id'),
			'Bookmarks' => $mapper->hasMany($entity, 'App\EventBookmarks', 'event_id'),
			'Bookmarked' => $mapper->hasMany($entity, 'App\EventBookmarks', 'event_id'),
		];
	}
}