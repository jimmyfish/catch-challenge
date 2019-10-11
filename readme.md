**DITO LAKSONO YUDHA PUTRA**

killcoder212@gmail.com

(+62) 8990314474

Challenge v1
---

**Getting things ready**

Installing any dependency needed for project

` composer install `

**API Endpoint**

- Converting *.JSONL

    `/convert`
    
    parameter :
    - `src` The source of file that being downloaded
    
    optional parameter :
    - `filetype` Specify the output filetype, (available options : `*.csv`, `*.yaml`) . default filetype is `*.csv`
    
    usage example :
    
    `/convert?src=http://someexample.website/data/input.jsonl` This will output the file with `*.csv` filetype
    
    `/convert?src=http://someexample.website/data/input.jsonl&filetype=yaml` This will output the file with `*.yaml` filetype
    
