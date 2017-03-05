# CampusBox Api

<a name="top"></a>
## Route Files
  [top](#top)

 -  [login](#login)
    - [#sign-up-a-user](#sign-up-a-user)
    - [#login-user](#login-user)
 -  [home](#home)
    - [#home-page-for-a-user](#home-page-for-a-user)
 -  [events](#events)
    - [#get-all-events](#get-all-events)
    - [#rsvp-an-event](#rsvp-an-event)
    - [#remove-rsvp-from-an-event](#remove-rsvp-from-an-event)
    - [#like-an-event](#like-an-event)
    - [#remove-like-from-an-event](#remove-like-from-an-event)
    - [#add-new-event](#add-new-event)
    - [#delete--event](#delete--event)
 -  [content](#content)
    - [#get-all-contents](#get-all-contents)
    - [#bookmark-a-content](#bookmark-a-content)
    - [#remove-bookmark-from-a-content](#remove-bookmark-from-a-content)
    - [#like-a-content](#like-a-content)
    - [#remove-like-from-a-content](#remove-like-from-a-content)
    - [#delete--event-1](#delete--event-1)
 -  [student](#student)
    - [#get-one-student](#get-one-student)
    - [#follow-an-student](#follow-an-student)
    - [#remove-follow-from-an-student](#remove-follow-from-an-student)
 -  [search](#search)
 -  [autocomplete/data](#autocomplete)

<a name="login"></a>
## Login/Signup
 [top](#top)
### Sign up a user

> post /signup

recieve the following object if user exists then send error 


 - type : facebook/google
 - token : "token vlaue"
 - interests : array 
     - each with : interest_ids
 - skills : array 
     - each with : name (check if more than 5 then error)
 - college : "college_id"
 - roll : "roll_number" 


```json
example object returned
```

### login user
> post /logn


recieve the following object if user does not exist then send error 

 - type : facebook/google
 - token : "token vlaue"

```json
example object returned
```

<a name="home"></a>
## Home Pgae
  [top](#top)

### home page for a user

> get /home


<a name="events"></a>
## Events
  [top](#top)

### get all events
> get /events

send events which student is elligible for inter==false and collegeid matches that of student 
then inter == true 
only events which are yet to happen 


```json
{
  "data": [
    {
      "id": 1000,
      "title": "AICHE Industrial Visit",
      "subtitle": null,
      "details": {
        "venue": null,
        "type": null,
        "team": 0,
        "price": 0,
        "description": "AIChE Student Chapter is conducting an industrial visit to SATIA PAPER MILLS, MUKTSAR",
        "rules": "AIChE Student Chapter is conducting an industrial visit to SATIA PAPER MILLS, MUKTSAR"
      },
      "timings": {
        "date": {
          "start": null,
          "end": null
        },
        "time": {
          "start": null,
          "end": null
        }
      },
      "Actions": {
        "Bookmarked": {
          "status": true,
          "total": 2,
          "bookmarks": 2
        },
        "Participants": {
          "status": true,
          "total": 4
        }
      },
      "contact": [
        {
          "name": null,
          "link": 0,
          "image": null
        },
        {
          "name": null,
          "link": 0,
          "image": null
        }
      ],
      "created": {
        "by": {
          "name": null,
          "link": 0,
          "image": null
        },
        "at": {
          "date": "2016-10-15 16:06:22.000000",
          "timezone_type": 3,
          "timezone": "UTC"
        }
      },
      "tags": {
        "0": {
          "name": null,
          "link": 0
        },
        "total": 4
      },
      "links": {
        "self": "/events/"
      }
    }]
}```

### get one event
> get /event/{id}

send data for one event

```json
same as above
```

### RSVP an event
> post /RsvpEvent/{id}

check rsvp already exists otherwise add one 
```json
example object returned
```
### remove RSVP from an event
> delete /RsvpEvent/{id}

check rsvp already exists then delete
```json
{
  "status": "ok",
  "message": "Rsvp Removed"
}
```

### Like an event
> post /LikeEvent/{id}

check Like already exists otherwise add one 
```json
example object returned
```
### remove Like from an event
> delete /likeEvent/{id}

check like already exists then delete
```json
example object returned
```
### add new event
> post /event

add new event 
```json
will send confirmation
```
### delete  event
> delete /event/{id}

delete  event check if it belongs to the student
```json
Same as above
```

<a name="contents"></a>
## contents
  [top](#top)

### get all contents
> get /contents

send contents which student is interested in sort by latest added and followed by the person


```json
{
  "data": [
    {
      "id": 13,
      "title": "How Cafes Are The New Workplaces!",
      "content": {
        "type": 0,
        "description": "###Human beings have an amazing ability ",
        "embed": "Lakshit Anand",
        "images": {
          "alt": null,
          "link": 0
        }
      },
      "created": {
        "by": {
          "name": "Lakshit Anand",
          "link": 1,
          "image": "https://avatars3.githubusercontent.com/u/6951276?v=3&s=400"
        },
        "at": "20161004T19:18:07"
      },
      "Actions": {
        "Appriciate": {
          "status": true,
          "total": 1
        },
        "Bookmarked": {
          "status": true,
          "total": 1
        }
      },
      "details": {
        "software": {
          "name": null,
          "link": 0
        },
        "euquipment": {
          "name": null,
          "link": 0
        }
      },
      "tags": {
        "name": null,
        "link": 0
      },
      "total": 1,
      "links": {
        "self": "/contents/"
      }
    },
```

### get one content
> get /content/{id}

send data for one content

```json
same as above
```

### bookmark a content
> post /bookmarkcontent/{id}

check bookmark already exists otherwise add one 
```json
example object returned
```
### remove bookmark from a content
> delete /bookmarkcontent/{id}

check bookmark already exists then delete
```json
example object returned
```

### Like a content
> post /Likecontent/{id}

check Like already exists otherwise add one 
```json
example object returned
```
### remove Like from a content
> delete /likeContent/{id}

check rsvp already exists then delete
```json
example object returned
```
### add new content
> post /content

add new content 
```json
example object returned
```
### delete  event
> delete /event/{id}

delete  event check if it belongs to the student
```json
example object returned
```




<a name="students"></a>
## students
  [top](#top)



### get one student
> get /student/{id}

send data for one student
send 
    student details ,
     -  skillls,
     - events created,
     -  content created ,
     - students following ,
     -  events rsvped,
     - bookmarked content

```json
{
  "data": {
    "id": 1,
    "name": "Lakshit Anand",
    "subtitle": "Web Developer, wants to create meaningful projects to positively impact the world. :sweat_smile: ",
    "photo": "https://avatars3.githubusercontent.com/u/6951276?v=3&s=400",
    "college": {
      "roll_number": 101506031,
      "name": "Thapar University",
      "hostelid": null,
      "room_number": "B305"
    },
    "contacts": {
      "email": "lakshit1001@ymail.com",
      "phone": 594664
    },
    "about": {
      "age": null,
      "gender": "Male",
      "home_city": "Delhi"
    },
    "studies": {
      "grad_id": null,
      "branch_id": null,
      "year": "2018",
      "class_id": null,
      "passout_year": 2017,
      "college": "Thapar University"
    },
    "Events": {
      "data": [
      ]
    },
    "Skills": {
      "data": [
        {
          "id": 0,
          "name": "AngularJS",
          "links": {
            "self": "/reports/"
          }
        },
        {
          "id": 0,
          "name": "Espanol",
          "links": {
            "self": "/reports/"
          }
        }
      ]
    },
    "SocialAccounts": {
      "data": []
    },
    "Followed": {
      "data": [
      ]
    },
    "BookmarkedContents": {
      "data": [
      ]
    },
    "AttendingEvents": {
      "data": [
      ]
    }
  }
}
```

### follow an student
> post /followstudent/{id}

check follow already exists otherwise add one 
```json
example object returned
```
### remove follow from an student
> delete /followstudent/{id}

check follow already exists then delete
```json
example object returned
```


<a name="search"></a>
## search
  [top](#top)



### get one search
> get /search/{query}

send data for one search
send 
     - name
     - image
     - type = event/student/content
     - link

```json
example object returned
```

### full an search
> post /fullsearch/{id}

check full objects of all 3 types in search 

```json
example object returned
```



<a name="autocomplete"></a>
## autocomplete/data (not protected by JWT)
  [top](#top)



### All Skills
> get /skills

send skills name

```json
example object returned
```

### Skills autocomplete
> get /skills/{query}

send skills name

```json
example object returned
```

### Colleges
> get /Colleges

send 
     - College_id
     - College name

```json
example object returned
```
### Colleges Autocomplete
> get /Colleges/{query}

send 
     - College_id
     - College name

```json
example object returned
```

### Event Types
> get /eventTypes

send 
     - Type_id
     - type_name

```json
example object returned
```

### Event Tags
> get /event tags

send 
     - Tag
```json
example object returned
```
### Event Tags
> get /eventtags/{query}

send 
     - Tag
```json
example object returned
```
