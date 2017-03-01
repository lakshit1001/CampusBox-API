<?php
/*
 * This file is part of the Slim API skeleton package
 *
 * Copyright (c) 2016-2017 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/slim-api-skeleton
 *
 */
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
			"created_by_id" => ["type" => "integer", "required" => true],
			"title" => ["type" => "string", "required" => true],
			"description" => ["type" => "string", "required" => true],
			"timer" => ["type" => "text", "value" => new \DateTime()],
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
		$this->data([
			"content_id" => null,
			"created_by_id" => null,
			"title" => null,
			"description" => null,
			"timer" => null,
		
		]);
	}
	public static function relations(Mapper $mapper, Entity $entity) {
		return [
			// 'Images' => $mapper->hasMany($entity, 'App\ContentImage', 'content_id'),
			//'Updates' => $mapper->hasMany($entity, 'App\ContentUpdates', 'content_id'),
			//  'Type' => $mapper->belongsTo($entity, 'App\ContentType', 'content_type_id'),
			'Owner' => $mapper->belongsTo($entity, 'App\Student', 'created_by_id'),
			//'Participants' => $mapper->hasManyThrough($entity, 'App\Student', 'App\Participants', 'student_id', 'content_id'),
			// 'Tags' => $mapper->hasManyThrough($entity, 'App\Tag', 'App\ContentCategory', 'tag_id', 'content_id'),
			// 'Likes' => $mapper->hasManyThrough($entity, 'App\Student', 'App\ContentLikes', 'student_id', 'content_id'),
			// 'Bookmarked' => $mapper->hasManyThrough($entity, 'App\Student', 'App\ContentBookmarks', 'student_id', 'content_id'),
			//'StudentsBookmarked' => $mapper->hasMany($entity, 'App\ContentBookmarks', 'content_id'),
			'Appreciates' => $mapper->hasMany($entity, 'App\ContentAppreciate', 'content_id'),
			'Appreciated' => $mapper->hasMany($entity, 'App\ContentAppreciate', 'content_id'),
			'Bookmarks' => $mapper->hasMany($entity, 'App\ContentBookmarks', 'content_id'),
			'Bookmarked' => $mapper->hasMany($entity, 'App\ContentBookmarks', 'content_id'),
		];
	}
}