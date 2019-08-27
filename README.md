# employees_api
A single page PHP-SQLITE3 API facilitating CRUD on an employees table

This is an API developed in PHP 7.1+ with SQLITE 3 extension only for testing purposes. It creates an unsecure sqlite3 db with name .employees.db in the same folder (so that it is invisble in finder). It is strictly not for production use. The DB is created in web folder so that it can be downloaded and verified in external tools without accessing server. 

Usage:
Host the PHP file on a writable folder in any web server supporting PHP 7.1+ with SQLITE 3 extension. For the below documentation purposes, the API is assumed to be hosted on a URL: http://dummyapiurl/api/employees.php

DB will be initialised on first call to the API URL.


All operations on employees data is done through HTTP POST requests. Permitted operations are described below:
**1. CREATE**

API URL: http://dummyapiurl/api/employees.php

Method: POST

Params: 

     - action - Should be equal to string "CREATE" 
     - empid - a valid integer
     - ename - should match reg exp /^[a-zA-Z ]{3,40}$/ 
     - dob - YYYY-MM-DD
     - email - a valid email address mobile - 10 digit number matching reg exp /^[6-9]{1}[0-9]{9}$/ 
     - addr - (ADDRESS) should match reg exp /^[a-zA-Z0-9 \s\.\,\#\/]{10,300}$/ 
     - latt - location latitute - a floating point number
     - long - location longitude - a floating point number
     - profilepic - Profile picture in JPEG format base 64 encoded. Base 64 Decoded jpeg image data should be less than 300KB

Response:
JSON: 
*In case of invalid data:*
eg : `{"error": "Employee image is too big","code":"9"}`
code field indicates the field which contains error. Codes are indicated below:

 - empid - 1 
 - ename - 2 
 - date_of_birth - 3  
 - emailid - 4 
 - mobile - 5 
 - address - 6 
 - latt - 7  
 - long - 8 
 - profilepic - 9
  
*DB Error:* `{"error": "Database error 1 ","code":"-1"}`

*Failure:* `{"error": "Employee creation failed","code":"-2"}`

*In case of success creation:*    `{"error":null,"msg":"Employee created successfully","code":"-99"}`

 **2. READ**
API URL: http://dummyapiurl/api/employees.php

Method: POST

Params: 

    -action - should be string . "READ"
    -empid - employee id which exists already in the DB

Response:
JSON

In case of non existent employee    `{"error": "Invalid employee ID","code":"1" }`

In case of existing employee 
`{"error":null,"data":[{"empid":100,"ename":"an emp","date_of_birth":"1986-12-10","emailid":"abcdef@gmail.com","mobile":"123456789","address":"address of employee","profilepic":"\/9j\/4AAQSkZJRgA\/\/Z","latt":12.971599,"long":77.594566}],"code":"0"}`

Note: profilepic is base64 encoded jpg data of the image

**3. UPDATE**
API URL: http://dummyapiurl/api/employees.php

Method: POST

Params: 

    -action - should be string . "UPDATE"
    -and all other parameters are same as CREATE

>  * Parameter validations are same as CREATE, except that employee id should be existing
>  * If profilepic supplied is empty, existing profile pic would continue.
>  * All other fields should be provided mandatorily whether you want to modify or not!

Response:

success update: `{"error":null,"msg":"Employee updated successfully","code":"-99"}`

Failed update: `{"error": "Employee update failed","code":"-2"}`

**4. DELETE**
API URL: http://dummyapiurl/api/employees.php

Method: POST

Params: 

    -action - should be string . "DELETE"
    -empid - employee id which exists already in the DB
Response:

Successful deletion: `{"error":null,"msg":"Employee deleted successfully","code":"-99"}`

Non existent employee: `{"error": "Employee does not exist","code":"1"}`

Failed Deletion: `{"error": "Employee delete failed","code":"-2"}`

**5. READ ALL** - Returns the complete list of employees in json format

Params: 
 `-action - should be string "READALL"`

Response: 

In case of no data  `{"error": "No data found","code":"-2"}`

Successful response: 

    {"error":null,"data":[{"empid":100,"ename":"an emp","date_of_birth":"1986-11-13","emailid":"efgh@gmail.com","mobile":"1234901234","address":"FLAT NO xx, QUA.......","profilepic":"\/9j\/4AA5v\/\/Z","latt":12.971599,"long":77.594566},{"empid":101,"ename":"another emp","date_of_birth":"1986-09-13","emailid":"efgh@gmail.com","mobile":"1234901234","address":"FLAT NO xx, QUA.......","profilepic":"\/9j\/4AAQSkZG3rX5v\/\/Z","latt":12.971599,"long":77.594566},{"empid":102,"ename":"one more emp","date_of_birth":"1986-10-13","emailid":"efgh@gmail.com","mobile":"1234901234","address":"FLAT NO xx, QUA.......","profilepic":"\/9j\/4AAQ\/Z","latt":12.971599,"long":77.594566}],"code":"0"}

Note: profilepic is base64 encoded jpg data of the image
