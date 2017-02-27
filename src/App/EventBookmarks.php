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

class EventBookmarks extends \Spot\Entity {
	protected static $table = "event_bookmarks";

	public static function fields() {
		return [

			"event_bookmark_id" => ["type" => "integer", "unsigned" => true, "primary" => true, "autoincrement" => true],
			"event_id" => ["type" => "integer", "unsigned" => true],
			"student_id" => ["type" => "integer", "unsigned" => true],
			"timed" => ["type" => "datetime"],
		];
	}

	public static function events(EventEmitter $emitter) {
		$emitter->on("beforeInsert", function (EntityInterface $entity, MapperInterface $mapper) {
			$entity->event_id = Base62::encode(random_bytes(9));
		});

		$emitter->on("beforeUpdate", function (EntityInterface $entity, MapperInterface $mapper) {
			$entity->time_created = new \DateTime();
		});
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
			'Event' => $mapper->belongsTo($entity, 'App\Event', 'event_id'),
			'Student' => $mapper->belongsTo($entity, 'App\Event', 'event_id')
		];
	}
}
