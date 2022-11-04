# hng_csv_task

 ### How to run locally
 - Run WAMP Server to start up PHP
 - Clone to your localhost - git clone https://github.com/dreywandowski/hng_csv_task.git
 - Open up Postman for API testing
 - Send a post request to the endpoint http://localhost/{cloned_folder}
 - In the body payload, send a form data with the parameter name "csv", ensure you upload the file as value.
 - Sucessful or failed response will be given and computed csv moved to the modified_csv folder
