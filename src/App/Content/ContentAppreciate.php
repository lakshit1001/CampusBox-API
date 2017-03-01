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

use Spot\Entity ;
use Spot\EntityInterface ;
use Spot\EventEmitter;
use Spot\MapperInterface;
use Tuupola\Base62;

class ContentAppreciate extends \Spot\Entity {
	protected static $table = "content_appreciates";

	public static function fields() {
		return [

			"content_appreciate_id" => ["type" => "integer", "unsigned" => true, "primary" => true, "autoincrement" => true],
			"content_id" => ["type" => "integer", "unsigned" => true],
			"student_id" => ["type" => "integer", "unsigned" => true],
			"timer" => ["type" => "datetime"],
		];
	}

	public static function contents(EventEmitter $emitter) {
		$emitter->on("beforeInsert", function (EntityInterface $entity, MapperInterface $mapper) {
			$entity->timer = new \DateTime();
		});

		$emitter->on("beforeUpdate", function (EntityInterface $entity, MapperInterface $mapper) {
			$entity->timer = new \DateTime();
		});
	}
	public function timestamp() {
		return $this->timer->getTimestamp();
	}

	public function etag() {
		return md5($this->content_id . $this->timestamp());
	}
 
	public function clear() {
		$this->data([
		]);
	}

	public static function relations(MapperInterface $mapper, EntityInterface $entity) {
		return [
			'Content' => $mapper->belongsTo($entity, 'App\Content', 'content_id'),
			'Student' => $mapper->belongsTo($entity, 'App\Content', 'student_id')
		];
	}
}
