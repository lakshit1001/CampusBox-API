<?php



namespace App;

use App\Student;
use League\Fractal;

class StudentTransformer extends Fractal\TransformerAbstract
{
  private $params = [];

  function __construct($params = []) {
    $this->params = $params;
  }
  protected $availableIncludes = [
  'Events',
  'Skills',
  'SocialAccounts',
  'Followed',
  'BookmarkedContents',
  // 'BookmarkedEvents'
  ];
  protected $defaultIncludes = [
  'Followed',
  'Events',
  'Skills',
  'SocialAccounts',
  'BookmarkedContents',
  'AttendingEvents',
  'CreativeContents'
  ];
  public function transform(Student $student)
  {
    $this->params['value1'] = false;
    if(isset($this->params['type']) && $this->params['type'] == 'get'){
      $followed = $student->Followed;
      for ($i=0; $i < count($followed); $i++) { 
          if($followed[$i]->username == $this->params['username']){
              $this->params['value1'] = true;
              break;
          }
      }
    } else {
      $this->params['value1'] = false;
    }
    return [
    "username" => (string)$student->username?: 0 ,
    "name" => (string)$student->name?: null,
    "subtitle" => (string)$student->about?: null,
    "photo" => (string)$student->image?: null,
    
    "college" => [
    "roll_number" => (integer)$student->roll_number?: null,
    "name" => (string)$student->College['name']?: null,
    "hostelid" => (integer)$student->hostel_id?: null,
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
    ],
    "following" => $this->params['value1']
    
    ];
  }
  public function includeEvents(Student $student) {
    $events = $student->Owner;

    return $this->Collection($events, new EventMiniTransformer);
  }
  public function includeBookmarkedContents(Student $student) {
    $contents = $student->BookmarkedContents;

    return $this->collection($contents, new ContentMiniTransformer);
  }
public function includeCreativeContents(Student $student) {
    $contents = $student->CreativeContents;

    return $this->collection($contents, new ContentMiniTransformer);
  }
  public function includeAttendingEvents(Student $student) {
    $events = $student->AttendingEvents;

    return $this->collection($events, new EventTransformer);
  }
  public function includeSkills(Student $student) {
    $skills = $student->Skills;

    return $this->collection($skills, new StudentSkillTransformer);
  }
public function includeSocialAccounts(Student $student) {
        $socials = $student->SocialAccounts;

        return $this->collection($socials, new SocialTransformer);
    }
  public function includeFollowed(Student $student) {
    $followers = $student->Followed;

    return $this->collection($followers, new StudentMiniTransformer);
  }
}
