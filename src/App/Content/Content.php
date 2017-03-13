<?php
namespace App;
use Spot\EntityInterface as Entity;
use Spot\EventEmitter;
use Spot\MapperInterface as Mapper;
use Tuupola\Base62;

class Content extends \Spot\Entity {
	protected static $table = "contents";
	public static function fields() {
		return [
			"content_id" => ["type" => "integer", "unsigned" => true, "primary" => true, "autoincrement" => true],
			"created_by_username" => ["type" => "string", "required" => true],
			"college_id" => ["type" => "integer", "required" => true],
			"content_type_id" => ["type" => "integer", "required" => true],
			"title" => ["type" => "string", "required" => true],
			"timer" => ["type" => "string"]
		];
	}
	public static function contents(EventEmitter $emitter) {
		$emitter->on("beforeInsert", function (EntityInterface $entity, MapperInterface $mapper) {
			$entity->content_id = Base62::encode(random_bytes(9));
		});
		$emitter->on("beforeUpdate", function (EntityInterface $entity, MapperInterface $mapper) {
			$entity->timer = new \DateTime();
		});
	}
	public function timestamp() {
		return $this->timer;
	}
	public function etag() {
		return md5($this->id . $this->timestamp());
	}
	public function clear() {
	}
	public static function relations(Mapper $mapper, Entity $entity) {
		return [
			// 'Images' => $mapper->hasMany($entity, 'App\ContentImage', 'content_id'),
			//'Updates' => $mapper->hasMany($entity, 'App\ContentUpdates', 'content_id'),
			//  'Type' => $mapper->belongsTo($entity, 'App\ContentType', 'content_type_id'),
			//'Owner' => $mapper->belongsTo($entity, 'App\Student', 'created_by_username'),
			//'Participants' => $mapper->hasManyThrough($entity, 'App\Student', 'App\Participants', 'username', 'content_id'),
			// 'Tags' => $mapper->hasManyThrough($entity, 'App\Tag', 'App\ContentCategory', 'tag_id', 'content_id'),
			// 'Likes' => $mapper->hasManyThrough($entity, 'App\Student', 'App\ContentLikes', 'username', 'content_id'),
			// 'Bookmarked' => $mapper->hasManyThrough($entity, 'App\Student', 'App\ContentBookmarks', 'username', 'content_id'),
			//'StudentsBookmarked' => $mapper->hasMany($entity, 'App\ContentBookmarks', 'content_id'),
			'Items' => $mapper->hasMany($entity, 'App\ContentItems', 'content_id'),
			'Appreciates' => $mapper->hasMany($entity, 'App\ContentAppreciate', 'content_id'),
			'Appreciated' => $mapper->hasMany($entity, 'App\ContentAppreciate', 'content_id'),
			'Bookmarks' => $mapper->hasMany($entity, 'App\ContentBookmarks', 'content_id'),
			'Bookmarked' => $mapper->hasMany($entity, 'App\ContentBookmarks', 'content_id'),
		];
	}
}