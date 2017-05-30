<?php 
namespace App; 

use App\Student; 
use League\Fractal; 

class ContentAppreciateTransformer extends Fractal\TransformerAbstract 
{ 
	public function transform(ContentAppreciate $student) 
	{ 
		return [ 
		"content_appreciate_id" => (int)$student->content_appreciate_id?: 0 ,
		"username" => (string) $student->Appreciator['username'] ?: null,
		"title" => (string) $student->Appreciator['name'] ?: null,
		"about" => (string) $student->Appreciator['about'] ?: null,
		"photo" => (string) $student->Appreciator['image'] ?: null,
		"college" => (string) $student->Appreciator->College['name'] ?: 0
		]; 
	} 
} 