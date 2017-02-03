<?php
require '.././libs/Slim/Slim.php';
require_once 'dbHelper.php';
require_once '../include/ip_address.php';
require_once '../include/Mobile-Detect-2.8.24/Mobile_Detect.php';


\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app = \Slim\Slim::getInstance();
$db = new dbHelper();
date_default_timezone_set('Asia/Kolkata');
/**
 * Database Helper Function templates
 */
/*
select(table name, where clause as associative array)
insert(table name, data as associative array, mandatory column names as array)
update(table name, column names as associative array, where clause as associative array, required columns as array)
delete(table name, where clause as array)
*/

//reported things
$app->get('/reports/:reported', function($reported) { 
    global $db;
    $rows = $db->select("report","id,reported_by_id,type,timestamp,type_id,reason,reported",array('reported' => $reported));
    echoResponse(200, $rows);
});
$app->post('/report', function() use ($app) { 
    $data = json_decode($app->request->getBody());
    $mandatory = array('reason');
    global $db;
    $rows = $db->insert("report", $data, $mandatory);
    if($rows["status"]=="success")
        $rows["message"] = "Reported successfully.";
    echoResponse(200, $rows);
});
$app->put('/report/:id', function($id) use ($app) { 
    $data = json_decode($app->request->getBody());
    $condition = array('id'=>$id);
    $mandatory = array();
    global $db;
    $rows = $db->update("report", $data, $condition, $mandatory);
    if($rows["status"]=="success")
        $rows["message"] = "Report updated successfully.";
    echoResponse(200, $rows);
});


// Followers
$app->get('/followers/:following_id/:following', function($following_id, $following) { 
    global $db;
    $rows = $db->select("followers","id, follower_id",array('following_id' => $following_id));
    echoResponse(200, $rows);
});
$app->post('/follower', function() use ($app) { 
    $data = json_decode($app->request->getBody());
    $mandatory = array('follower_id');
    global $db;
    $rows = $db->insert("followers", $data, $mandatory);
    if($rows["status"]=="success")
        $rows["message"] = "Follower added successfully.";
    echoResponse(200, $rows);
});
$app->put('/followers/:id', function($id) use ($app) { 
    $data = json_decode($app->request->getBody());
    $condition = array('id'=>$id);
    $mandatory = array();
    global $db;
    $rows = $db->update("followers", $data, $condition, $mandatory);
    if($rows["status"]=="success")
        $rows["message"] = "Report updated successfully.";
    echoResponse(200, $rows);
});




// colleges
$app->get('/colleges', function() { 
    global $db;
    $rows = $db->select("colleges","id,name,address,lat,long,city,logo,coverpic",array());
    echoResponse(200, $rows);
});

$app->get('/college/:id', function($id) { 
    global $db;
    $rows = $db->select("colleges","id,name,address,lat,long,city,logo,coverpic",array('id'=>$id));
    echoResponse(200, $rows);
});

$app->post('/college', function() use ($app) { 
    $data = json_decode($app->request->getBody());
    $mandatory = array('name');
    global $db;
    $rows = $db->insert("colleges", $data, $mandatory);
    if($rows["status"]=="success")
        $rows["message"] = "Product added successfully.";
    echoResponse(200, $rows);
});

$app->put('/college/:id', function($id) use ($app) { 
    $data = json_decode($app->request->getBody());
    $condition = array('id'=>$id);
    $mandatory = array();
    global $db;
    $rows = $db->update("colleges", $data, $condition, $mandatory);
    if($rows["status"]=="success")
        $rows["message"] = "Product information updated successfully.";
    echoResponse(200, $rows);
});

$app->delete('/college/:id', function($id) { 
    global $db;
    $rows = $db->delete("colleges", array('id'=>$id));
    if($rows["status"]=="success")
        $rows["message"] = "Product removed successfully.";
    echoResponse(200, $rows);
});

//student
$app->get('/students', function() { 
    global $db;
    $rows = $db->select("student","id,college_id,name,roll_number,email,phone,photo,hostel,room_number,home_city,grad_id,branch_id,year,class_id,passout_year,age,gender",array());
    echoResponse(200, $rows);
});

$app->get('/student/:id', function($id) { 
    global $db;
    $rows = $db->select("student","id,college_id,name,roll_number,email,phone,photo,hostel,room_number,home_city,grad_id,branch_id,year,class_id,passout_year,age,gender",array('id'=>$id));
    echoResponse(200, $rows);
});

$app->post('/student', function() use ($app) { 
    $data = json_decode($app->request->getBody());
    $mandatory = array('name');
    global $db;
    $rows = $db->insert("student", $data, $mandatory);
    if($rows["status"]=="success")
        $rows["message"] = "Product added successfully.";
    echoResponse(200, $rows);
});

$app->put('/student/:id', function($id) use ($app) { 
    $data = json_decode($app->request->getBody());
    $condition = array('id'=>$id);
    $mandatory = array();
    global $db;
    $rows = $db->update("student", $data, $condition, $mandatory);
    if($rows["status"]=="success")
        $rows["message"] = "Product information updated successfully.";
    echoResponse(200, $rows);
});

$app->delete('/student/:id', function($id) { 
    global $db;
    $rows = $db->delete("student", array('id'=>$id));
    if($rows["status"]=="success")
        $rows["message"] = "Product removed successfully.";
    echoResponse(200, $rows);
});

//societies
$app->get('/societies', function() { 
    global $db;
    $rows = $db->select("societies","id,college_id,name,description,dp,created_by,website",array());
    echoResponse(200, $rows);
});

$app->get('/society/:id', function($id) { 
    global $db;
    $rows = $db->select("societies","id,college_id,name,description,dp,created_by,website",array('id'=>$id));
    echoResponse(200, $rows);
});

$app->post('/society', function() use ($app) { 
    $data = json_decode($app->request->getBody());
    $mandatory = array('name');
    global $db;
    $rows = $db->insert("societies", $data, $mandatory);
    if($rows["status"]=="success")
        $rows["message"] = "Product added successfully.";
    echoResponse(200, $rows);
});

$app->put('/society/:id', function($id) use ($app) { 
    $data = json_decode($app->request->getBody());
    $condition = array('id'=>$id);
    $mandatory = array();
    global $db;
    $rows = $db->update("societies", $data, $condition, $mandatory);
    if($rows["status"]=="success")
        $rows["message"] = "Product information updated successfully.";
    echoResponse(200, $rows);
});

$app->delete('/society/:id', function($id) { 
    global $db;
    $rows = $db->delete("societies", array('id'=>$id));
    if($rows["status"]=="success")
        $rows["message"] = "Product removed successfully.";
    echoResponse(200, $rows);
});

//society members

//get all members of a society
$app->get('/society_members/:society_id', function($id) { 
    global $db;
    $rows = $db->select("team_members","id,college_id,societyUsername,added_by_id,name,email,phone,position",array('society_id'=>$id));
    echoResponse(200, $rows);
});

$app->post('/society_member', function() use ($app) { 
    $data = json_decode($app->request->getBody());
    $mandatory = array('name');
    global $db;
    $rows = $db->insert("team_members", $data, $mandatory);
    if($rows["status"]=="success")
        $rows["message"] = "Product added successfully.";
    echoResponse(200, $rows);
});

$app->put('/society_member/:id', function($id) use ($app) { 
    $data = json_decode($app->request->getBody());
    $condition = array('id'=>$id);
    $mandatory = array();
    global $db;
    $rows = $db->update("team_members", $data, $condition, $mandatory);
    if($rows["status"]=="success")
        $rows["message"] = "Product information updated successfully.";
    echoResponse(200, $rows);
});

$app->delete('/society_member/:id', function($id) { 
    global $db;
    $rows = $db->delete("team_members", array('id'=>$id));
    if($rows["status"]=="success")
        $rows["message"] = "Product removed successfully.";
    echoResponse(200, $rows);
});

// Society Skills

// all skills required in a society
$app->get('/society_skills/:society_id', function($society_id) { 
    global $db;
    $rows = $db->selectjoin("society_skills","skills","skill_id","id",array('society_id'=>$society_id));
    echoResponse(200, $rows);
});

$app->delete('/society_skills/:id', function($id) { 
    global $db;
    $rows = $db->delete("society_skills", array('id'=>$id));
    if($rows["status"]=="success")
        $rows["message"] = "Product removed successfully.";
    echoResponse(200, $rows);
});

// Events, latest first
$app->get('/events(/:type(/:date_from(/:date_to(/:price))))', function($type = '%',$date_from, $date_to, $price = '%') { 
    $date_from = date("Y-m-d H:i:s");
    global $db;
    if (isset($datefrom) && isset($date_to)) {
        $rows = $db->selectRange("events","id,college_id,society_id,created_by_id,name,description,contactperson1,contactperson2,date,venue,time,inter,time_created,price",array('type' => $type, 'price' => $price, ), "".$date_from."", "".$date_to."","date", "date desc");
    }
    elseif(isset($date_from) && !isset($date_to)){
        $rows = $db->selectMax("events","id,college_id,society_id,created_by_id,name,description,contactperson1,contactperson2,date,venue,time,inter,time_created",array('type' => $type, 'price' => $price, ), "".$date_from."", "date", "date desc");
    }
    echoResponse(200, $rows);
});

        $app->get('/event/:id', function($id) { 
            global $db;
            $rows = $db->select("events","id,college_id,society_id,created_by_id,name,description,contactperson1,contactperson2,date,venue,time,inter,time_created,price",array('id'=>$id));
            logDetails('get', 'rohanrohan', '/event/:id');
            echoResponse(200, $rows);
        });

        $app->post('/event', function() use ($app) { 
            $data = json_decode($app->request->getBody());
            $mandatory = array('name');
            global $db;
            $rows = $db->insert("events", $data, $mandatory);
            if($rows["status"]=="success")
                $rows["message"] = "Product added successfully.";
            echoResponse(200, $rows);
        });

        $app->put('/event/:id', function($id) use ($app) { 
            $data = json_decode($app->request->getBody());
            $condition = array('id'=>$id);
            $mandatory = array();
            global $db;
            $rows = $db->update("events", $data, $condition, $mandatory);
            if($rows["status"]=="success")
                $rows["message"] = "Product information updated successfully.";
            echoResponse(200, $rows);
        });

        $app->delete('/event/:id', function($id) { 
            global $db;
            $rows = $db->delete("events", array('id'=>$id));
            if($rows["status"]=="success")
                $rows["message"] = "Product removed successfully.";
            echoResponse(200, $rows);
        });

//Event Views

//all events views
// $app->get('/events_views', function() { 
//     global $db;
//     $rows = $db->select("eventViews","id,username,eventId,timestamp,device",array());
//     echoResponse(200, $rows);
// });

//particular event views
// $app->get('/event_views/:eventId', function($eventId) { 
//     global $db;
//     $rows = $db->select("eventViews","id,username,eventId,timestamp,device",array('eventId'=>$eventId));
//     echoResponse(200, $rows);
// });


// $app->post('/event_view', function() use ($app) { 
//     $data = json_decode($app->request->getBody());
//     $mandatory = array('username');
//     global $db;
//     $rows = $db->insert("eventViews", $data, $mandatory);
//     if($rows["status"]=="success")
//         $rows["message"] = "Product added successfully.";
//     echoResponse(200, $rows);
// });

//Event updates

//all event updates
        $app->get('/events_updates', function() { 
            global $db;
            $rows = $db->select("event_updates","id,event_id,title,message,timestamp,color,society_id,created_by_id",array());
            echoResponse(200, $rows);
        });

//particular event updates
        $app->get('/event_updates/:event_id', function($event_id) { 
            global $db;
            $rows = $db->select("event_updates","id,title,message,timestamp,color,societyid",array('event_id'=>$event_id));
            echoResponse(200, $rows);
        });

        $app->put('/event_update/:id', function($id) use ($app) { 
            $data = json_decode($app->request->getBody());
            $condition = array('id'=>$id);
            $mandatory = array();
            global $db;
            $rows = $db->update("event_updates", $data, $condition, $mandatory);
            if($rows["status"]=="success")
                $rows["message"] = "Product information updated successfully.";
            echoResponse(200, $rows);
        });

        $app->post('/event_update', function() use ($app) { 
            $data = json_decode($app->request->getBody());
            $mandatory = array('username');
            global $db;
            $rows = $db->insert("event_updates", $data, $mandatory);
            if($rows["status"]=="success")
                $rows["message"] = "Product added successfully.";
            echoResponse(200, $rows);
        });

        $app->delete('/event_update/:id', function($id) { 
            global $db;
            $rows = $db->delete("event_updates", array('id'=>$id));
            if($rows["status"]=="success")
                $rows["message"] = "Product removed successfully.";
            echoResponse(200, $rows);
        });


//projects
        $app->get('/project/:student_id', function() { 
            global $db;
            $rows = $db->select("projects","id,student_id,name,description,link,active,people_required",array());
            echoResponse(200, $rows);
        });

        $app->get('/project/:id', function($id) { 
            global $db;
            $rows = $db->select("projects","id,student_id,name,description,link,active,people_required",array('id'=>$id));
            echoResponse(200, $rows);
        });

        $app->post('/project', function() use ($app) { 
            $data = json_decode($app->request->getBody());
            $mandatory = array('name');
            global $db;
            $rows = $db->insert("projects", $data, $mandatory);
            if($rows["status"]=="success")
                $rows["message"] = "Product added successfully.";
            echoResponse(200, $rows);
        });

        $app->put('/project/:id', function($id) use ($app) { 
            $data = json_decode($app->request->getBody());
            $condition = array('id'=>$id);
            $mandatory = array();
            global $db;
            $rows = $db->update("projects", $data, $condition, $mandatory);
            if($rows["status"]=="success")
                $rows["message"] = "Product information updated successfully.";
            echoResponse(200, $rows);
        });

        $app->delete('/project/:id', function($id) { 
            global $db;
            $rows = $db->delete("projects", array('id'=>$id));
            if($rows["status"]=="success")
                $rows["message"] = "Product removed successfully.";
            echoResponse(200, $rows);
        });

//project_skills

// get all skills required in this project
        $app->get('/project_skill/:project_id', function($project_id) { 
            global $db;
            $rows = $db->selectjoin("project_skills","skills","skill_id","id",array('project_id'=>$project_id));
            echoResponse(200, $rows);
        });

        $app->post('/project_skill', function() use ($app) { 
            $data = json_decode($app->request->getBody());
            $mandatory = array('name');
            global $db;
            $rows = $db->insert("project_skills", $data, $mandatory);
            if($rows["status"]=="success")
                $rows["message"] = "Product added successfully.";
            echoResponse(200, $rows);
        });

        $app->put('/project_skill/:id', function($id) use ($app) { 
            $data = json_decode($app->request->getBody());
            $condition = array('id'=>$id);
            $mandatory = array();
            global $db;
            $rows = $db->update("project_skills", $data, $condition, $mandatory);
            if($rows["status"]=="success")
                $rows["message"] = "Product information updated successfully.";
            echoResponse(200, $rows);
        });

        $app->delete('/project_skill/:id', function($id) { 
            global $db;
            $rows = $db->delete("project_skills", array('id'=>$id));
            if($rows["status"]=="success")
                $rows["message"] = "Product removed successfully.";
            echoResponse(200, $rows);
        });



//skills
        $app->get('/skills', function() { 
            global $db;
            $rows = $db->select("skills","id,name",array());
            echoResponse(200, $rows);
        });

        $app->get('/skill/:id', function($id) { 
            global $db;
            $rows = $db->select("skills","id,name",array('id'=>$id));
            echoResponse(200, $rows);
        });

// get all the skills in a student
        $app->get('/student_skills/:student_id', function($student_id) { 
            global $db;
            $rows = $db->selectjoin("student_skills","skills","skill_id","id",array('student_id'=>$student_id));
            echoResponse(200, $rows);
        });

        $app->post('/student_skills', function() use ($app) { 
            $data = json_decode($app->request->getBody());
            $mandatory = array('name');
            global $db;
            $rows = $db->insert("skills", $data, $mandatory);
            if($rows["status"]=="success")
                $rows["message"] = "Product added successfully.";
            echoResponse(200, $rows);
        });

// only unique entries will be posted
        $app->post('/skill', function() use ($app) { 
            $data = json_decode($app->request->getBody());
            $mandatory = array('name');
            global $db;
            $rows = $db->insert("skills", $data, $mandatory);
            if($rows["status"]=="success")
                $rows["message"] = "Product added successfully.";
            echoResponse(200, $rows);
        });

        $app->put('/skill/:id', function($id) use ($app) { 
            $data = json_decode($app->request->getBody());
            $condition = array('id'=>$id);
            $mandatory = array();
            global $db;
            $rows = $db->update("skills", $data, $condition, $mandatory);
            if($rows["status"]=="success")
                $rows["message"] = "Product information updated successfully.";
            echoResponse(200, $rows);
        });

        $app->delete('/skill/:id', function($id) { 
            global $db;
            $rows = $db->delete("skills", array('id'=>$id));
            if($rows["status"]=="success")
                $rows["message"] = "Product removed successfully.";
            echoResponse(200, $rows);
        });


        function echoResponse($status_code, $response) {
            global $app;
            $app->status($status_code);
            $app->contentType('application/json');
            echo json_encode($response,JSON_NUMERIC_CHECK);
        }
        function getIpAddress() {
            $ip_keys = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
            foreach ($ip_keys as $key) {
                if (array_key_exists($key, $_SERVER) === true) {
                    foreach (explode(',', $_SERVER[$key]) as $ip) {
                // trim for safety measures
                        $ip = trim($ip);
                // attempt to validate IP
                        if (validateIp($ip)) {
                            return $ip;
                        }
                    }
                }
            }
            return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
        }
/**
 * Ensures an ip address is both a valid IP and does not fall within
 * a private network range.
 */
function validateIp($ip)
{
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
        return false;
    }
    return true;
}

function logDetails($requestType, $username, $api){
    // check the device type
$detect = new Mobile_Detect;

    $deviceType = 'mobile';
    $deviceOs = 'ios';
    
    if ($detect->isMobile()){
        echo "mobile";
        if($detect->isiOS()){
            echo "ios phone";
        }elseif($detect->isAndroidOS()){
            $deviceOs = 'android';
            echo "android phone";
        }
    }elseif($detect->isTablet()){
        $deviceType = 'tablet';
        if($detect->isiOS()){
            $deviceOs = 'ios';
            echo "ios tablet";
        }elseif($detect->isAndroidOS()){
            $deviceOs = 'android';
            echo "android phone";
        }
    }else{
        $deviceType = 'desktop';
        echo "desktop";
    }
    if (getIpAddress()) {
        $ipAddress = getIpAddress();
    }else{
        $ipAddress = 'error';
    }
        $deviceType = 'mobile';
        $deviceOs = 'ios';
    
    $data = array('requestType' => $requestType, 'username' => $username, 'api' => $api, 'deviceType' => $deviceType, 'deviceOs' => $deviceOs, 'ipAddress' => $ipAddress);
    $mandatory = array();
    global $db;
    $rows = $db->insert("logs", $data, $mandatory);
    if($rows["status"]=="success"){
        $rows["message"] = "Reported successfully.";
        echo "rows added successfully";
    }
    echoResponse(200, $rows);
}

$app->run();
?>