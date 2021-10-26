# Copy Grocy "Missing Products" to BRING List 

## Build Container

First build the container:

`docker build -t grocy-to-bring . `

## Run Container

Insert your Grocy URL, Grocy API Key and BRING Credentials to the .env file. Use the .env-example and copy it to .env

### Get your BRING List UUID

First you need to get your __BRING List UUID__. Insert your BRING Credentials in your _.env_ file and start the container with the following command to get your lists: 

`docker run --env-file .env --rm -t --name run-grocy-to-bring grocy-to-bring php ./bring.php`

You will see an output of your lists: 

Below are your bring lists with their UUID:  
1st List // UUID: 9z567898-mnbv-2d67-qwer-9876543123  
2nd List // UUID: e4567222-mnbv-2530-qwer-1234567890  

Note the __UUID__ of the list you want to use and add it to the _.env_ file. 

## Add items to BRING

Now simply run the container:

`docker run  --rm --name run-grocy-to-bring grocy-to-bring`

You can add it to your crontab e.g. and have it run every morning at 8:10: 

`10 8 * * * sudo docker run --rm --name run-grocy-to-bring grocy-to-bring 2>&1 | /usr/bin/logger -t GrocyToBring`

# Thank you

Thank you to helvete003 for reverse-engineering the Bring REST API: https://github.com/helvete003/bring-api

And of course a massive shout out to Bernd Bestel the founder of https://grocy.info/ 