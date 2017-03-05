# CampusBox Api

<a name="login"></a>
## Route Files

- [login](#login)
- [home](#home)
- [events](#events)
- [content](#content)
- [student](#student)
- [search](#search)
- [autocomplete/data](#autocomplete)

<a name="login"></a>
## Login/Signup

### Sign up a user

> post /signup

recieve the following object if user exists then send error 

-type : facebook/google
-token : "token vlaue"
-interests : array 
    -each with : interest_ids
-skills : array 
    -each with : name (check if more than 5 then error)
-college : "college_id"
-roll : "roll_number" 

```json
example object returned
```

### login user
> post /logn


recieve the following object if user does not exist then send error 

-type : facebook/google
-token : "token vlaue"

```json
example object returned
```

<a name="home"></a>
## Home Pgae

### home page for a user

> get /home


<a name="events"></a>
## Events

### get all events
> get /events

send events which student is elligible for inter==false and collegeid matches that of student 
then inter == true 
only events which are yet to happen 


```json
example object returned
```

### get one event
> get /event/{id}

send data for one event

```json
example object returned
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
example object returned
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
example object returned
```
### delete  event
> delete /event/{id}

delete  event check if it belongs to the student
```json
example object returned
```




<a name="contents"></a>
## contents

### get all contents
> get /contents

send contents which student is interested in sort by latest added and followed by the person


```json
example object returned
```

### get one content
> get /content/{id}

send data for one content

```json
example object returned
```

### bookmark an content
> post /bookmarkcontent/{id}

check bookmark already exists otherwise add one 
```json
example object returned
```
### remove bookmark from an content
> delete /bookmarkcontent/{id}

check bookmark already exists then delete
```json
example object returned
```

### Like an content
> post /Likecontent/{id}

check Like already exists otherwise add one 
```json
example object returned
```
### remove Like from an content
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



### get one student
> get /student/{id}

send data for one student
send 
    student details ,
    - skillls,
    -events created,
    - content created ,
    -students following ,
    - events rsvped,
    -bookmarked content

```json
example object returned
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



### get one search
> get /search/{query}

send data for one search
send 
    -name
    -image
    -type = event/student/content
    -link

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
    -College_id
    -College name

```json
example object returned
```
### Colleges Autocomplete
> get /Colleges/{query}

send 
    -College_id
    -College name

```json
example object returned
```

### Event Types
> get /eventTypes

send 
    -Type_id
    -type_name

```json
example object returned
```

### Event Tags
> get /event tags

send 
    -Tag
```json
example object returned
```
### Event Tags
> get /eventtags/{query}

send 
    -Tag
```json
example object returned
```
