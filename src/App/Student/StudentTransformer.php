<?php



namespace App;

use App\Student;
use League\Fractal;

class StudentTransformer extends Fractal\TransformerAbstract
{
  protected $availableIncludes = [
  'Events',
  'Skills',
          // 'SocialAccounts',
  'Followed',
  'BookmarkedContents',
          // 'BookmarkedEvents'
  ];
  protected $defaultIncludes = [
  'Events',
  'Skills',
           // 'SocialAccounts',
  'Followed',
  'BookmarkedContents',
  'AttendingEvents'
  ];
  public function transform(Student $student)
  {
    return [
    "username" => (integer)$student->username?: 0 ,
    "name" => (string)$student->name?: null,
    "subtitle" => (string)$student->about?: null,
    "photo" => (string)$student->image?: null,
    
    "college" => [
    "roll_number" => (integer)$student->roll_number?: null,
    "name" => (string)$student->College['name']?: null,
    "hostelid" => (integer)$student->hostelid?: null,
    "room_number" => (string)$student->room_number?: null,
    ],
    "contacts" => [
    "email" => (string)$student->email?: null,  
    "phone" => (integer)$student->phone?: null,
    ],
    "about" => [
    "age" => (integer)$student->age?: null,
    "gender" => (string)$student->gender?: null,
    "home_city" => (string)$student->home_city?: null,
    ],
    
    "studies" => [
    "grad_id" => (integer)$student->grad_id?: null,
    "branch_id" => (integer)$student->branch_id?: null,
    "year" => (string)$student->year?: null,
    "class_id" => (integer)$student->class_id?: null,
    "passout_year" => (integer)$student->passout_year?: null,
    "college" => (string)$student->College['name']?: null,
    ],
    
    ];
  }
  public function includeEvents(Student $student) {
    $events = $student->Events;

    return $this->collection($events, new EventTransformer(['student_id' => null, 'type' => null]));
  }
  public function includeBookmarkedContents(Student $student) {
    $contents = $student->BookmarkedContents;

    return $this->collection($contents, new ContentMiniTransformer);
  }
  public function includeAttendingEvents(Student $student) {
    $events = $student->AttendingEvents;

    return $this->collection($events, new EventTransformer(['student_id' => null, 'type' => null]));
  }
  public function includeSkills(Student $student) {
    $skills = $student->Skills;

    return $this->collection($skills, new SkillTransformer);
  }
// public function includeSocialAccounts(Student $student) {
//         $socials = $student->SocialAccounts;

//         return $this->collection($socials, new SocialTransformer);
//     }
  public function includeFollowed(Student $student) {
    $socials = $student->Followed;

    return $this->collection($socials, new StudentMiniTransformer);
  }
}
