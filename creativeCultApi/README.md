#CREATIVE CULT REST API

##AUTH 
###register
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



