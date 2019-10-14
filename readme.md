**DITO LAKSONO YUDHA PUTRA**

killcoder212@gmail.com

(+62) 8990314474

Challenge v1
---
This project is build with symfony skeleton version 4.3 (minimum installation) using design pattern concept for easy maintain for long term project.

```
Library list :
- symfony/orm-pack
- symfony/yaml
- symfony/http-client
- doctrine/annotations
- symfony/finder
```

**Getting things ready**

Installing any dependency needed for project

` composer install `

Configuring database

1. Open your .env file
2. Change the configuration based on your database config  
    change line `DATABASE_URL=mysql://user:password@127.0.0.1:3306/dbname`
3. Run migration
    `php bin/console migrate`
4. Enjoy with a cup of coffee ~

**API Endpoint**

- Converting *.JSONL

    `/convert`
    
    parameter :
    - `src` The source of file that being downloaded
    
    optional parameter :
    - `filetype` Specify the output filetype, (available options : `*.csv`, `*.yaml`) . default filetype is `*.csv`
    
    - `db` Save output into database (available options : `0, 1`) default option is `0`
    
    - `email` Email result to specific email `example : &email=example@site.com`
    
    usage example :
    
    `/convert?src=http://someexample.website/data/input.jsonl` This will output the file with `*.csv` filetype
    
    `/convert?src=http://someexample.website/data/input.jsonl&filetype=yaml` This will output the file with `*.yaml` filetype
    
    `http://catch.test/convert?src=https://s3-ap-southeast-2.amazonaws.com/catch-code-challenge/challenge-1-in.jsonl&filetype=yaml&db=1&email=killcoder212@gmail.com`  
    
    This will output the file with `*.yaml` filetype and save to database, and also will send to `killcoder212@gmail.com` with `*.yaml` file attach.
    
- Get inserted data  
when you give `db` parameter set to `1`, you will get batch number on json response, this number can be used for get the data from database.

    `/get/{batch_number}` - change `batch_number` with given batch number in json response.
