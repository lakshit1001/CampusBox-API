<?php
namespace App;

use Spot\EntityInterface as Entity;
use Spot\EventEmitter;
use Spot\MapperInterface as Mapper;
use Tuupola\Base62;

class Student extends \Spot\Entity {
	protected static $table = "students";

	public static function fields() {
		return [
		"username" => ["type" => "string", "primary" => true],
		"college_id" => ["type" => "integer"],
		"name" => ["type" => "string"],
		"image" => ["type" => "string"],
		"roll_number" => ["type" => "integer"],
		"email" => ["type" => "string"],
		"phone" => ["type" => "integer"],
		"about" => ["type" => "string"],
		"hostel_id" => ["type" => "integer"],
		"room_number" => ["type" => "string"],
		"home_city" => ["type" => "string"],
		"grad_id" => ["type" => "integer"],
		"branch_id" => ["type" => "integer"],
		"year" => ["type" => "string"],
		"class_id" => ["type" => "integer"],
		"passout_year" => ["type" => "integer"],
		"birthday" => ["type" => "string"],
		"gender" => ["type" => "string"],
		];
	}

	public static function students(EventEmitter $emitter) {
		$emitter->on("beforeInsert", function (EntityInterface $entity, MapperInterface $mapper) {
			$entity->student_id = Base62::encode(random_bytes(9));
		});
	}

	public function clear() {
		$this->data([
			"college_id" => null,
			"name" => null,
			"image" => null,
			"username" => null,
			"roll_number" => null,
			"email" => null,
			"phone" => null,
			"about" => null,
			"hostelid" => null,
			"room_number" => null,
			"home_city" => null,
			"grad_id" => null,
			"branch_id" => null,
			"year" => null,
			"class_id" => null,
			"passout_year" => null,
			"birthday" => null,
			"gender" => null,
			]);
	}

	public static function relations(Mapper $mapper, Entity $entity) {
		return [
		'College' => $mapper->belongsTo($entity, 'App\College', 'college_id'),
		'Events' => $mapper->hasMany($entity, 'App\Event', 'created_by_username'),
		'SocialAccounts' => $mapper->hasMany($entity, 'App\SocialAccount', 'username'),
		'BookmarkedContents' => $mapper->hasManyThrough($entity, 'App\Content', 'App\ContentBookmarks', 'content_id', 'username'),
		'AttendingEvents' => $mapper->hasManyThrough($entity, 'App\Event', 'App\EventRsvp', 'event_id', 'username'),
		'Skills' => $mapper->hasMany($entity, 'App\StudentSkill', 'username'),
		'Followed' => $mapper->hasManyThrough($entity, 'App\Student', 'App\StudentFollow', 'followed_username', 'follower_username'),
		'Interets' => $mapper->hasMany($entity, 'App\StudentInterest', 'username'),

			// 'Following' => $mapper->hasManyThrough($entity, 'App\Student', 'App\Follow', 'follower_id', 'following_id'),
			// 'Followers' => $mapper->hasManyThrough($entity, 'App\Student', 'App\Follow', 'following_id', 'follower_id'),

			// 'ClassGroup' => $mapper->belongsTo($entity, 'App\ClassGroup', 'class_group_id'),
			// 'CreativeContents' => $mapper->hasMany($entity, 'App\CreativeContent', 'post_id'),
			// 'EventLikes' => $mapper->hasManyThrough($entity, 'App\Event', 'App\EventLike', 'post_id'),
			//'EventBookmarks' => $mapper->hasMany($entity, 'App\EventBookmarks', 'username'),
			// 'EventParticipated' => $mapper->hasManyThrough($entity, 'App\Event', 'App\Student', 'post_id', 'username'),
			//'Participants' => $mapper->hasMany($entity, 'App\Event', 'username'),
//			'Skills' => $mapper->hasMany($entity, 'App\Skill', 'username'),
		'BookmarkedContents' => $mapper->hasManyThrough($entity, 'App\Content', 'App\ContentBookmarks', 'content_id', 'username'),
		'AttendingEvents' => $mapper->hasManyThrough($entity, 'App\Event', 'App\EventRsvp', 'event_id', 'username'),
		'Skills' => $mapper->hasMany($entity, 'App\StudentSkill', 'username'),
		'Follower' => $mapper->hasManyThrough($entity, 'App\Student', 'App\StudentFollow', 'follower_username', 'username'),
			//'Socialid' => $mapper->hasOne($entity, 'App\Socialid', 'username')

			// 'hostel' => $mapper->hasOne($entity, 'App\College', 'user_id')
		];
	}
}
