#CREATIVE CULT REST API

##AUTH 
###register

/register

(post)

Fields
1. name: varchar
2. email: varchar
3. password: varchar


1. Wrong input returns 

        {
            id: 0,
            message: reason of failure
        }
   
2. success returns

        {
            id: 1,
            message: success
        }

##login

/login

(post)

Fields
1. email: varchar
2. password: varchar


1. if validation failes (pending)
2. if not already logged in
   

    {
       'id' => 1,
       'verdict' => 'success',
       'message' => 'new login complete',
       'user' => user info,
       'session_info' => session information
    }
3. If already logged in 

       {
            'id' => 1,
            'verdict' => 'success',
            'message' => 'already logged in',
            'user' => $user
            'session_info' => session information
       }
   
4. if fails
    
        {
            'id' => 0,
            'verdict' => 'session verdicut'
            'message' => 'why failed'
        }

## EVENT MANAGEMENT
### Create Event

/make_new_event

(post)

Fields
1. token (received after login)
2. business (MAKE_NEW_EVENT)
3. starting_datetime (datetime)
4. ending_datetime (datetime)
5. event_name (varchar)
6. description (text)


1. On success
   
       {
            'id' => 1,
            'message' => 'new event created successfully'
       }

2. Else id is 0

### Give Marks

/give_points

(post)

Fields
1. token (received after login)
2. business (GIVE_MARK)
3. user_id (user of whom marks is to be given)
4. event_id (event you want to take give marks of)
5. points


1. On success

       {
            'id' => 1,
            'message' => 'Points updated successfully'
       }
2. Else id is 0
